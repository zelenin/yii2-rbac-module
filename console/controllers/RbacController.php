<?php

namespace Zelenin\yii\modules\Rbac\console\controllers;

use yii\base\InvalidConfigException;
use yii\console\Controller;
use Yii;
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
        if (!$auth instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before generating.');
        }
        $auth->removeAll();
        if ($auth->load()) {
            echo PHP_EOL . 'RBAC rules are generated' . PHP_EOL;
        }
    }
}
