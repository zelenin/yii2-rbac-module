# Yii2 RBAC module

[Yii2](http://www.yiiframework.com) RBAC module with generating assignments to DB from RBAC data storage files

## Installation

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

```
php composer.phar require zelenin/yii2-rbac-module "dev-master"
```

or add

```
"zelenin/yii2-rbac-module": "dev-master"
```

to the require section of your ```composer.json```

## Usage

Configure AuthManager component in config:

```php
'components' => [
    'authManager' => [
        'class' => \Zelenin\yii\modules\Rbac\components\DbManager::className(),
        'itemFile' => '@common/config/rbac/items.php',
        'assignmentFile' => '@common/config/rbac/assignments.php',
        'ruleFile' => '@common/config/rbac/rules.php',
        'defaultRole' => 'user',
		'roleParam' => 'role', // User model attribute
		// optional
		'enableCaching' => false,
		'cachingDuration' => 60
	]
]
```

Run:

```
php yii migrate --migrationPath=@yii/rbac/migrations/
```

or use sql file in ```@yii/rbac/migrations/```

For generating assignments from php storage files run

```
php yii rbac/generate
```

For storage files examples see ```example``` directory

## Info

See [Yii2 authorization guide](https://github.com/yiisoft/yii2/blob/master/docs/guide/security-authorization.md)

## Author

[Aleksandr Zelenin](https://github.com/zelenin/), e-mail: [aleksandr@zelenin.me](mailto:aleksandr@zelenin.me)
