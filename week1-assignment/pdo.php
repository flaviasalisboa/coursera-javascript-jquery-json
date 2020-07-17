<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc2','flavia','php200-2020');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>