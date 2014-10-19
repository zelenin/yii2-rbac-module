<?php

namespace Zelenin\yii\modules\Rbac\console\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use Zelenin\yii\modules\Rbac\components\DbManager;

class RbacController extends Controller
{
    /**
     * @throws InvalidConfigException
     */
    public function actionGenerate()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->getAuthManager();
        $auth->removeAll();
        if ($auth->load()) {
            echo PHP_EOL . 'RBAC rules are generated' . PHP_EOL;
        }
    }
}
