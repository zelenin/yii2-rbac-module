<?php

use yii\rbac\Item;
use Zelenin\yii\modules\Rbac\rules\OwnItemRule;

$ownItemRule = new OwnItemRule;

return [
    'rules' => [
        $ownItemRule->name => serialize($ownItemRule)
    ],
    'items' => [
        'readPost' => ['type' => Item::TYPE_PERMISSION],
        'editPost' => ['type' => Item::TYPE_PERMISSION, 'children' => ['readPost']],
        'editOwnPost' => ['type' => Item::TYPE_PERMISSION, 'children' => ['editPost'], 'ruleName' => $ownItemRule->name],

        'user' => [
            'type' => Item::TYPE_ROLE,
            'description' => 'User',
            'children' => [
                'readPost',
                'editOwnPost'
            ]
        ],
        'administrator' => [
            'type' => Item::TYPE_ROLE,
            'description' => 'Administrator',
            'children' => [
                'user',
                'editPost'
            ]
        ]
    ],
    'assignments' => [
        // 1 => 'administrator' // userId => role
    ]
];
