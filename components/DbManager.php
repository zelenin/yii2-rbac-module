<?php

namespace Zelenin\yii\modules\Rbac\components;

use yii\rbac\Assignment;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\User;
use Yii;

class DbManager extends \yii\rbac\DbManager
{
    public $authFile = '@app/data/rbac.php';
    public $defaultRole = 'user';
    public $roleParam = 'role';
    /** @var User $user */
    private $user;

    public $cache = false;
    public $cachePrefix = 'rbac_';
    public $cacheDuration = 300;
    private $cacheJar = [];

    public function checkAccess($userId, $permissionName, $params = [])
    {
        if (!$this->cache && !$this->cacheDuration) {
            return parent::checkAccess($userId, $permissionName, $params);
        }

        $key = $this->cachePrefix . $userId . $permissionName . serialize($params);

        if (isset($this->cacheJar[$key])) {
            return $this->cacheJar[$key];
        }

        $cache = Yii::$app->getCache();
        $data = $cache->get($key);
        if ($data === false) {
            $data = parent::checkAccess($userId, $permissionName, $params);
            $this->cacheJar[$key] = $data;
            $cache->set($key, $data, $this->cacheDuration);
        }
        return $data;
    }

    public function init()
    {
        parent::init();
        if (isset(Yii::$app->user)) {
            $this->assignRole();
        }
    }

    public function load()
    {
        $this->authFile = Yii::getAlias($this->authFile);
        $children = [];
        $rules = [];
        $assignments = [];
        $items = [];

        $data = $this->loadFromFile($this->authFile);

        if (isset($data['rules'])) {
            foreach ($data['rules'] as $name => $ruleData) {
                $rules[$name] = unserialize($ruleData);
                $this->addRule($rules[$name]);
            }
        }

        if (isset($data['items'])) {
            foreach ($data['items'] as $name => $item) {
                $class = $item['type'] == Item::TYPE_PERMISSION
                    ? Permission::className()
                    : Role::className();

                $items[$name] = new $class([
                    'name' => $name,
                    'description' => isset($item['description']) ? $item['description'] : null,
                    'ruleName' => isset($item['ruleName']) ? $item['ruleName'] : null,
                    'data' => isset($item['data']) ? $item['data'] : null,
                    'createdAt' => isset($item['createdAt']) ? $item['createdAt'] : null,
                    'updatedAt' => isset($item['updatedAt']) ? $item['updatedAt'] : null,
                ]);
                $this->addItem($items[$name]);
            }

            foreach ($data['items'] as $name => $item) {
                if (isset($item['children'])) {
                    foreach ($item['children'] as $childName) {
                        if (isset($items[$childName])) {
                            $children[$name][$childName] = $items[$childName];
                            $this->addChild($items[$name], $items[$childName]);
                        }
                    }
                }
                if (isset($item['assignments'])) {
                    foreach ($item['assignments'] as $userId => $assignment) {
                        $assignments[$userId][$name] = new Assignment([
                            'userId' => $userId,
                            'roleName' => $assignment['roleName'],
                            'createdAt' => isset($assignment['createdAt']) ? $assignment['createdAt'] : null,
                        ]);
                        $this->assign($items[$assignment['roleName']], $userId);
                    }
                }
            }
        }
        return true;
    }

    protected function loadFromFile($file)
    {
        return is_file($file)
            ? require($file)
            : [];
    }

    public function assignRole()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            $identity = Yii::$app->getUser()->getIdentity();
            $userId = $identity->getId();
            $allRoles = array_keys($this->getRoles());

            if (!$identity->{$this->roleParam} || !in_array($identity->{$this->roleParam}, $allRoles)) {
                $identity->{$this->roleParam} = $this->defaultRole;
                $identity->save();
            }

            $assignments = array_keys($this->getAssignments($userId));
            if (!in_array($identity->{$this->roleParam}, $assignments)) {
                $this->revokeRoleAssignments($assignments, $userId);
                $role = $this->getRole($identity->{$this->roleParam});
                $this->assign($role, $userId);
            }
        }
    }

    public function revokeRoleAssignments($roles, $userId)
    {
        return $this->db->createCommand()
            ->delete($this->assignmentTable, ['user_id' => $userId, 'item_name' => $roles])
            ->execute() > 0;
    }
}
