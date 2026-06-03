<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = parse_ini_file(__DIR__ . '/../.env');

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $dotenv['DB_HOST'],
    'database'  => $dotenv['DB_NAME'],
    'username'  => $dotenv['DB_USER'],
    'password'  => $dotenv['DB_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();