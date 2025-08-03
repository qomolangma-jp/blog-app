<?php 
require 'header.php';
require 'auth.php';

$id = $_GET['id'] ?? 0;

// 記事の取得
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    exit("記事が見つかりません");
}

// カテゴリ一覧の取得
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $memo = $_POST['memo'];
    $category_id = $_POST['category_id'];

    // 画像のアップロード処理
    $imagePath = $post['image_path'];
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = $targetPath;
            }
        }
    }

    $stmt = $pdo->prepare("
        UPDATE posts SET title = ?, memo = ?, content = ?, category_id = ?, image_path = ?
        WHERE id = ?
    ");
    $stmt->execute([$title, $memo, $content, $category_id, $imagePath, $id]);

    header("Location: index.php");
}

?>

<div class="container">
    <form method="POST" enctype="multipart/form-data">
        タイトル：<br>
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>"><br>

        カテゴリ：<br>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $post['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        本文：<br>
        <textarea name="content" rows="10" cols="50"><?= htmlspecialchars($post['content']) ?></textarea><br>

        memo：<br>
        <textarea name="memo" rows="10" cols="50"><?= htmlspecialchars($post['memo']) ?></textarea><br>

        <?php if ($post['image_path']): ?>
            <p>現在の画像：<br><img src="<?= $post['image_path'] ?>" width="200"></p>
        <?php endif; ?>

        画像を変更する場合は再アップロード：<br>
        <input type="file" name="image"><br>


        <button type="submit">更新</button>
    </form>

    <p><a href="index.php">← 戻る</a></p>
</div>
<?php require 'footer.php'; ?>