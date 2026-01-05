# 🎬 Cinema Management System

## 🚀 Quick Start

### Add next line to /etc/hosts on your machine
```bash
127.0.0.1       api.cinema
```

### Start of container

```bash
docker compose up -d
```

### Entering in PHP container

```bash
docker exec -it --user root cinema_management_php bash
```


### ⚙️ Init of Yii2

Inside container:

```bash
cd yii-project/
php init
composer update
```

### 🧩 Local configuration(outside container)

### 1. `params-local.php`

Create file:

```bash
nano ./www/yii-project/api/config/params-local.php
```

Place:

```php
<?php

return [
];
```

---

### 2. `main-local.php` (API)

Create file:

```bash
nano ./www/yii-project/api/config/main-local.php
```

Place:

```php
<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty)
            'cookieValidationKey' => 'mcC2AY35aZThEP47jF_hBJrNGkOYWIVn',
        ],
    ],
];

if (!YII_ENV_TEST) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        'allowedIPs' => ['*'],
    ];
}

return $config;
```

---

### 🗄 Database configuration

Create file:

```bash
nano ./www/yii-project/common/config/main-local.php
```

Replace:

```php
'db' => [
    'class' => \yii\db\Connection::class,
    'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
],
```

With:

```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=cinema_management_mysql;dbname=cinema_management;local_infile=1',
    'username' => 'yii',
    'password' => 'yii',
    'charset' => 'utf8',
    'attributes' => [
        PDO::MYSQL_ATTR_LOCAL_INFILE => true,
    ],
],
'testDB' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=cinema_management_mysql;dbname=cinema_management_test;local_infile=1',
    'username' => 'yii',
    'password' => 'yii',
    'charset' => 'utf8',
    'attributes' => [
        PDO::MYSQL_ATTR_LOCAL_INFILE => true,
    ],
],
```

---

### 📦 Migration

Inside container:

```bash
php yii migrate
php yii migrate --db=testDB
```

---

## 👤 Create base users

```bash
php yii user/create admin@gmail.com admin password123 ADMIN
php yii user/create user@gmail.com user password123 USER
```

