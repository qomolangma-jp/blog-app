<?php require 'db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo "記事が見つかりません";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title><?php echo htmlspecialchars($post['title']); ?></title></head>
<body>
<h1><?php echo htmlspecialchars($post['title']); ?></h1>
<p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
<p><a href="index.php">← 一覧に戻る</a></p>
</body>
</html>
