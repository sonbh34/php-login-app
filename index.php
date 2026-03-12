<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Remember me 쿠키 확인
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $db = getDB();
    $token = $_COOKIE['remember_token'];
    $stmt = $db->prepare("SELECT id, username FROM users WHERE remember_token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header('Location: dashboard.php');
        exit;
    }
    $db->close();
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>로그인</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>아이디 또는 이메일</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>비밀번호</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group checkbox">
                <label><input type="checkbox" name="remember"> 로그인 유지</label>
            </div>
            <button type="submit" class="btn">로그인</button>
        </form>
        <div class="links">
            <a href="register.php">회원가입</a> |
            <a href="forgot_password.php">비밀번호 찾기</a>
        </div>
    </div>
</div>
</body>
</html>
