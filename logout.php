<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $db->close();
}

session_destroy();
setcookie('remember_token', '', time() - 3600, '/');
header('Location: index.php');
exit;
