<?php
// echo date('Y-m-d H:i:s'), PHP_EOL;
// sleep(3);
// echo 'OK', PHP_EOL;

$redis = new Redis;
var_dump($redis->connect('127.0.0.1', 6379));
var_dump($redis->set('a', time()));
var_dump($redis->get('a'));

$pdo = new PDO('mysql:dbname=mysql;host=127.0.0.1', 'root', 'root');
var_dump($pdo->query('select 123'));
