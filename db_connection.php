<?php
    $dsn = 'mysql:dbname=oneline_bbs;host=mysql';
    $user = 'phpadmin';
    $password = 'zaq12wsx';
    $dbh = new PDO($dsn, $user, $password);
    $dbh->query('SET NAMES utf8');
    date_default_timezone_set("Asia/Tokyo");
?>