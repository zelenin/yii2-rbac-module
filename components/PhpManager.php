<?php

namespace Zelenin\yii\modules\Rbac\components;

use Yii;
use yii\rbac\Assignment;

class PhpManager extends \yii\rbac\PhpManager
{
    /** @var string */
    public $roleParam = 'role';

    public function getAssignments($userId)
    {
        $user = Yii::$app->getUser();
        $assignments = [];
        if (!$user->getIsGuest()) {
            $assignment = new Assignment;
            $assignment->userId = $userId;
            $assignment->roleName = $user->getIdentity()->{$this->roleParam};
            $assignments[$assignment->roleName] = $assignment;
        }
        return $assignments;
    }
}
