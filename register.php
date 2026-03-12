<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$username || !$email || !$password) {
        $error = '모든 항목을 입력해주세요.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '올바른 이메일 형식이 아닙니다.';
    } elseif (strlen($password) < 6) {
        $error = '비밀번호는 6자 이상이어야 합니다.';
    } elseif ($password !== $password_confirm) {
        $error = '비밀번호가 일치하지 않습니다.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = '이미 사용 중인 아이디 또는 이메일입니다.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $username, $email, $hashed);
            $stmt->execute();
            $success = '회원가입이 완료되었습니다. 로그인해주세요.';
        }
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>회원가입</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>아이디</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>이메일</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>비밀번호 (6자 이상)</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>비밀번호 확인</label>
                <input type="password" name="password_confirm" required>
            </div>
            <button type="submit" class="btn">가입하기</button>
        </form>
        <div class="links">
            <a href="index.php">로그인으로 돌아가기</a>
        </div>
    </div>
</div>
</body>
</html>
