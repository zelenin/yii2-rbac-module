<?php

namespace Zelenin\yii\modules\Rbac\rules;

use yii\rbac\Item;
use yii\rbac\Rule;
use Yii;

class OwnItemRule extends Rule
{
    /** @var string */
    public $name = 'ownItemRule';

    /**
     * @param int $user
     * @param Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        $attribute = isset($params['attribute'])
            ? $params['attribute']
            : 'user_id';
        return isset($params['model']) && $user === $params['model']->$attribute;
    }
}
