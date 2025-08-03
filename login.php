<?php
require 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = $user;

        $userId = $_SESSION['user']['id'];
        
        // users_sub に対象ユーザーが存在するか確認
        $stmt = $pdo->prepare("SELECT * FROM users_sub WHERE user_id = ?");
        $stmt->execute([$userId]);
        $userSub = $stmt->fetch();

        // 存在しない場合は新規レコードを挿入
        if (!$userSub) {
            $stmt = $pdo->prepare("INSERT INTO users_sub (user_id, point) VALUES (?, ?)");
            $stmt->execute([$userId, 0]); // 初期ポイントは 0
        }
        
        header("Location: mypage.php");
    } else {
        $error = 'ログイン情報が正しくありません';
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>ログイン</title></head>
<body>
<h1>ログイン</h1>
<form method="POST">
    ユーザー名：<br>
    <input type="text" name="username"><br>
    パスワード：<br>
    <input type="password" name="password"><br>
    <button type="submit">ログイン</button>
</form>
<?= $error ?>
</body>
</html>
