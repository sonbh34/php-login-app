<?php
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'login_db');
define('DB_PORT', getenv('MYSQLPORT') ?: 3306);

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        die('DB 연결 실패: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

session_start();

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCK_TIME', 15); // 분
