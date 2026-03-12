<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (!$username || !$password) {
    $_SESSION['error'] = '아이디와 비밀번호를 입력해주세요.';
    header('Location: index.php');
    exit;
}

$db = getDB();

$stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = '아이디 또는 비밀번호가 올바르지 않습니다.';
    header('Location: index.php');
    exit;
}

// 잠금 확인
if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
    $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
    $_SESSION['error'] = "로그인 시도 횟수 초과. {$remaining}분 후 다시 시도해주세요.";
    header('Location: index.php');
    exit;
}

if (!password_verify($password, $user['password'])) {
    $attempts = $user['login_attempts'] + 1;
    if ($attempts >= MAX_LOGIN_ATTEMPTS) {
        $locked_until = date('Y-m-d H:i:s', strtotime('+' . LOCK_TIME . ' minutes'));
        $db->prepare("UPDATE users SET login_attempts = ?, locked_until = ? WHERE id = ?")
           ->bind_param('isi', $attempts, $locked_until, $user['id']);
        $db->execute();
        $_SESSION['error'] = "로그인 시도 횟수(" . MAX_LOGIN_ATTEMPTS . "회) 초과. " . LOCK_TIME . "분간 잠깁니다.";
    } else {
        $db->prepare("UPDATE users SET login_attempts = ? WHERE id = ?")
           ->bind_param('ii', $attempts, $user['id']);
        $db->execute();
        $remaining = MAX_LOGIN_ATTEMPTS - $attempts;
        $_SESSION['error'] = "비밀번호가 올바르지 않습니다. (남은 시도: {$remaining}회)";
    }
    header('Location: index.php');
    exit;
}

// 로그인 성공
$db->prepare("UPDATE users SET login_attempts = 0, locked_until = NULL WHERE id = ?")
   ->bind_param('i', $user['id'])->execute();

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

// 로그인 유지
if ($remember) {
    $token = bin2hex(random_bytes(32));
    $db->prepare("UPDATE users SET remember_token = ? WHERE id = ?")
       ->bind_param('si', $token, $user['id'])->execute();
    setcookie('remember_token', $token, time() + (30 * 24 * 3600), '/');
}

$db->close();
header('Location: dashboard.php');
exit;
