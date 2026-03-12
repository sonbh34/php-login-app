<?php
require_once 'config.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$valid_token = false;

if ($token) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $valid_token = (bool)$user;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (strlen($password) < 6) {
            $error = '비밀번호는 6자 이상이어야 합니다.';
        } elseif ($password !== $password_confirm) {
            $error = '비밀번호가 일치하지 않습니다.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->bind_param('si', $hashed, $user['id']);
            $stmt->execute();
            $success = '비밀번호가 변경되었습니다.';
            $valid_token = false;
        }
    }
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 재설정</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>비밀번호 재설정</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="index.php">로그인</a></div>
        <?php elseif (!$valid_token): ?>
            <div class="alert alert-error">유효하지 않거나 만료된 링크입니다.</div>
        <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label>새 비밀번호 (6자 이상)</label>
                <input type="password" name="password" required autofocus>
            </div>
            <div class="form-group">
                <label>새 비밀번호 확인</label>
                <input type="password" name="password_confirm" required>
            </div>
            <button type="submit" class="btn">비밀번호 변경</button>
        </form>
        <?php endif; ?>
        <div class="links">
            <a href="index.php">로그인으로 돌아가기</a>
        </div>
    </div>
</div>
</body>
</html>
