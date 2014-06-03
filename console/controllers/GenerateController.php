<?php

namespace Zelenin\yii\modules\Rbac\console\controllers;

use yii\base\InvalidConfigException;
use Yii;
use yii\console\Controller;
use Zelenin\yii\modules\Rbac\components\DbManager;

class GenerateController extends Controller
{
    public function actionIndex()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->getAuthManager();
        if (!$auth instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before generating.');
        }
        $auth->removeAll();
        if ($auth->load()) {
            echo 'RBAC rules is generated' . PHP_EOL;
        }
    }
}
