<?php

namespace Zelenin\yii\modules\Rbac\components;

use Yii;
use yii\db\ActiveRecord;
use yii\rbac\Assignment;
use yii\web\IdentityInterface;

class PhpManager extends \yii\rbac\PhpManager
{
    /** @var string */
    public $defaultRole = 'user';
    /** @var string */
    public $roleParam = 'role';

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        $user = Yii::$app->getUser();
        /** @var IdentityInterface|ActiveRecord|null $identity */
        $identity = $user->getIdentity();
        $assignments = parent::getAssignments($userId);
        $model = $userId === $user->getId()
            ? $identity
            : $identity::findOne($userId);
        if ($model) {
            $assignment = new Assignment;
            $assignment->userId = $userId;
            $assignment->roleName = $model->{$this->roleParam};
            $assignments[$assignment->roleName] = $assignment;
        }
        return $assignments;
    }
}
