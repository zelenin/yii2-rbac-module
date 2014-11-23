<?php

namespace Zelenin\yii\modules\Rbac\components;

use Yii;
use yii\db\ActiveRecord;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\IdentityInterface;
use yii\web\User;

class DbManager extends \yii\rbac\DbManager
{
    /** @var string */
    public $itemFile = '@app/rbac/items.php';
    /** @var string */
    public $assignmentFile = '@app/rbac/assignments.php';
    /** @var string */
    public $ruleFile = '@app/rbac/rules.php';
    /** @var string */
    public $defaultRole = 'user';
    /** @var string */
    public $roleParam = 'role';
    /** @var User */
    private $user;

    /** @var bool */
    public $enableCaching = false;
    /** @var int */
    public $cachingDuration = 300;
    /** @var array */
    private $cache = [];

    /**
     * @inheritdoc
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        if (!$this->enableCaching || !$this->cachingDuration) {
            return parent::checkAccess($userId, $permissionName, $params);
        }
        $key = serialize([__CLASS__, $userId, $permissionName, $params]);

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $cache = Yii::$app->getCache();
        $data = $cache->get($key);
        if ($data === false) {
            $data = parent::checkAccess($userId, $permissionName, $params);
            $this->cache[$key] = $data;
            $cache->set($key, $data, $this->cachingDuration);
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

    /**
     * @return bool
     */
    public function load()
    {
        $_items = [];

        $items = $this->loadFromFile(Yii::getAlias($this->itemFile));
        $itemsMtime = @filemtime(Yii::getAlias($this->itemFile));
        $assignments = $this->loadFromFile(Yii::getAlias($this->assignmentFile));
        $rules = $this->loadFromFile(Yii::getAlias($this->ruleFile));

        foreach ($rules as $ruleData) {
            $this->addRule(unserialize($ruleData));
        }

        foreach ($items as $name => $item) {
            $class = $item['type'] == Item::TYPE_PERMISSION
                ? Permission::className()
                : Role::className();
            $_items[$name] = new $class([
                'name' => $name,
                'description' => isset($item['description']) ? $item['description'] : null,
                'ruleName' => isset($item['ruleName']) ? $item['ruleName'] : null,
                'data' => isset($item['data']) ? $item['data'] : null,
                'createdAt' => $itemsMtime,
                'updatedAt' => $itemsMtime
            ]);
            $this->addItem($_items[$name]);
        }

        foreach ($items as $name => $item) {
            if (isset($item['children'])) {
                foreach ($item['children'] as $childName) {
                    if (isset($_items[$childName])) {
                        $this->addChild($_items[$name], $_items[$childName]);
                    }
                }
            }
        }
        foreach ($assignments as $userId => $role) {
            $this->assign($_items[$role], $userId);
        }

        return true;
    }

    /**
     * @param string $file
     * @return array
     */
    protected function loadFromFile($file)
    {
        return is_file($file)
            ? require($file)
            : [];
    }

    public function assignRole()
    {
        $user = Yii::$app->getUser();
        if (!$user->getIsGuest()) {
            /** @var IdentityInterface|ActiveRecord $identity */
            $identity = $user->getIdentity();
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

    /**
     * @param array $roles
     * @param int $userId
     * @return bool
     */
    public function revokeRoleAssignments($roles, $userId)
    {
        return $this->db->createCommand()
            ->delete($this->assignmentTable, ['user_id' => $userId, 'item_name' => $roles])
            ->execute() > 0;
    }
}
