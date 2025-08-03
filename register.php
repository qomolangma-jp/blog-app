<?php 
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        echo "ユーザー登録が完了しました！";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo "エラー: ユーザー名またはメールアドレスが既に使用されています。";
        } else {
            echo "エラー: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>ユーザー登録</title>
</head>
<body>
    <h1>ユーザー登録</h1>
    <form method="POST" action="register.php">
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">登録</button>
    </form>
</body>
</html>

<?php

?>