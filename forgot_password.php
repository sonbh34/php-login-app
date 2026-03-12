<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '올바른 이메일을 입력해주세요.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->bind_param('ssi', $token, $expires, $user['id']);
            $stmt->execute();

            // 실제 환경에서는 이메일로 링크를 발송합니다.
            // 여기서는 링크를 직접 표시합니다.
            $reset_link = "http://localhost/login/reset_password.php?token=" . $token;
            $success = "비밀번호 재설정 링크가 생성되었습니다.<br><a href='{$reset_link}'>여기를 클릭하여 재설정</a>";
        } else {
            $success = '해당 이메일로 가입된 계정이 있다면 재설정 링크를 보내드립니다.';
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
    <title>비밀번호 찾기</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>비밀번호 찾기</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>가입 시 이메일</label>
                <input type="email" name="email" required autofocus>
            </div>
            <button type="submit" class="btn">재설정 링크 받기</button>
        </form>
        <div class="links">
            <a href="index.php">로그인으로 돌아가기</a>
        </div>
    </div>
</div>
</body>
</html>
