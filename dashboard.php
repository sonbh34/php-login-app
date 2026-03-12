<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>대시보드</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>대시보드</h2>
        <p>환영합니다, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>님!</p>
        <p>로그인에 성공했습니다.</p>
        <a href="logout.php" class="btn btn-danger">로그아웃</a>
    </div>
</div>
</body>
</html>
