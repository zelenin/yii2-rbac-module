<?php

namespace Zelenin\yii\modules\Rbac\rules;

use Yii;
use yii\rbac\Rule;

class OwnItemRule extends Rule
{
    public $name = 'ownItemRule';

    public function execute($user, $item, $params)
    {
        $attribute = isset($params['attribute'])
            ? $params['attribute']
            : 'user_id';
        return isset($params['model']) && $user === $params['model']->$attribute;
    }
}
