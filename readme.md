# Yii2 RBAC module

[Yii2](http://www.yiiframework.com) RBAC module with generating assignments to DB from RBAC data store file rbac.php

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
		'authFile' => '@common/config/rbac.php',
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

For generating assignments from php file add

```php
'modules' => [
	'rbac' => \Zelenin\yii\modules\Rbac\Module::className()
]
```

to console config and run

```
php yii rbac/generate
```

For rbac.php example see ```example/rbac.php```

## Info

See [Yii2 authorization guide](https://github.com/yiisoft/yii2/blob/master/docs/guide/security-authorization.md)

## Author

[Aleksandr Zelenin](https://github.com/zelenin/), e-mail: [aleksandr@zelenin.me](mailto:aleksandr@zelenin.me)
