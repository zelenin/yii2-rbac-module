<?php

namespace Zelenin\yii\modules\Rbac;

use yii\base\BootstrapInterface;
use Yii;
use Zelenin\yii\modules\Rbac\console\controllers\RbacController;

class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            if (!isset($app->controllerMap['rbac'])) {
                $app->controllerMap['rbac'] = RbacController::className();
            }
        }
    }
}
