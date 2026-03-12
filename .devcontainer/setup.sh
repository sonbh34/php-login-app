#!/bin/bash

# MySQL 시작
sudo service mysql start

# DB 및 테이블 생성
sudo mysql -u root << 'EOF'
CREATE DATABASE IF NOT EXISTS login_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE login_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(64) DEFAULT NULL,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
EOF

# config.php 자동 생성
cat > /workspaces/php-login-app/config.php << 'PHPEOF'
<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'login_db');

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die('DB 연결 실패: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

session_start();

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCK_TIME', 15);
PHPEOF

echo "✅ 설정 완료! 아래 명령어로 서버 시작:"
echo "php -S 0.0.0.0:8080"
