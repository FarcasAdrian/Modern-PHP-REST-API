<?php

require_once 'vendor/autoload.php';

use Db\Database;
use Classes\User;

$database = new Database();
$user = new User($database);
print_r($user->getAll());
