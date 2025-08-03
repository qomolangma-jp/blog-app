<?php 
require 'header.php';
require 'auth.php';

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームからのデータ取得
    $title = $_POST['title'];
    $content = $_POST['content'];
    $memo = $_POST['memo'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user']['id'];

    // 画像のアップロード処理
    $uploadDir = 'uploads/';

    /*-------------------- ここから画像処理  ----------------------*/
    // ディレクトリが存在しなければ作成（パーミッションは755相当）
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;
    
        // MIMEタイプと拡張子の確認（セキュリティ対策）
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = $targetPath;
            }
        }
    }
    /*-------------------- //ここまで画像処理  ----------------------*/
    
    $stmt = $pdo->prepare("
    INSERT INTO posts (title, memo, content, created_at, user_id, category_id, image_path)
    VALUES (?, ?, ?, NOW(), ?, ?, ?)
    ");
    $stmt->execute([$title, $memo, $content, $user_id, $category_id, $imagePath]);

    header('Location: index.php');
}
?>

<div class="container">
    <form method="POST" enctype="multipart/form-data">
        タイトル：<br>
        <input type="text" name="title"><br>

        カテゴリ：<br>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select><br>

        本文：<br>
        <textarea name="content" rows="10" cols="50"></textarea><br>

        memo：<br>
        <textarea name="memo" rows="10" cols="50"></textarea><br>

        画像アップロード：<br>
        <input type="file" name="image"><br>

        <button type="submit">投稿</button>
    </form>
</div>

<?php require 'footer.php'; ?>
