<?php
$host='localhost'; $db='mousik'; $user='root'; $pass='';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8",$user,$pass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(PDOException $e){ die('DB error: '.$e->getMessage()); }

session_start();
