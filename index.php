<?php

require_once 'config_php.php';
require_once 'src/user.php';

$conn = new PDO ("mysql:host=$host;dbname=$database", $user,$password);

if ($conn->errorCode() !=null) {
    echo "Połączenie nieudane. Bląd:" . "$conn->errorInfo()[2]";
    exit;
}
echo "Poączenie udane.";




$user = User::loadUserById($conn, 6);

$user->delete($conn);



/* $user = User::loadUserById($conn, 1);
$user->setEmail('aaaaaaaaa.pl');
$user->saveToDB($conn);



/* $user = User::loadAllUsers($conn);

var_dump($user);



$user = new User();

$user->setEmail('-test@test.pl');
$user->setUsername('Terster Testerny');
$user->setPassword('test');

$user->savetoDB($conn);

$conn = null; */