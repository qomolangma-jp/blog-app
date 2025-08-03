<?php 
require 'header.php';
require 'auth.php';

// 商品データ取得（編集時）
$product = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE products_id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products_name = $_POST['products_name'];
    $prd_type_id = $_POST['prd_type_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $prd_text = $_POST['prd_text'];

    // 画像アップロード処理
    $photos = [];
    for ($i = 1; $i <= 3; $i++) {
        $photo_field = "prd_photo_$i";
        if (!empty($_FILES[$photo_field]['name'])) {
            $target = 'uploads/' . basename($_FILES[$photo_field]['name']);
            move_uploaded_file($_FILES[$photo_field]['tmp_name'], $target);
            $photos[] = $target;
        } else {
            // 編集時は既存画像を保持
            $photos[] = !empty($_POST["old_photo_$i"]) ? $_POST["old_photo_$i"] : null;
        }
    }

    if (!empty($_POST['id'])) {
        // 編集処理
        $stmt = $pdo->prepare("
            UPDATE products SET
            products_name = ?, prd_type_id = ?, price = ?, stock = ?,
            prd_photo_1 = ?, prd_photo_2 = ?, prd_photo_3 = ?, prd_text = ?
            WHERE products_id = ?
        ");
        $stmt->execute([
            $products_name, $prd_type_id, $price, $stock,
            $photos[0], $photos[1], $photos[2], $prd_text, $_POST['id']
        ]);
    } else {
        // 新規作成
        $stmt = $pdo->prepare("
            INSERT INTO products 
            (products_name, prd_type_id, price, stock, prd_photo_1, prd_photo_2, prd_photo_3, prd_text)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $products_name, $prd_type_id, $price, $stock,
            $photos[0], $photos[1], $photos[2], $prd_text
        ]);
    }

    header('Location: products_list.php');
    exit;
}
?>

<div class="container">
    <form method="POST" enctype="multipart/form-data">
        <?php if ($product): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['products_id']) ?>">
        <?php endif; ?>
        商品名：<br>
        <input type="text" name="products_name" value="<?= htmlspecialchars($product['products_name'] ?? '') ?>"><br>

        商品種別ID：<br>
        <input type="number" name="prd_type_id" value="<?= htmlspecialchars($product['prd_type_id'] ?? '') ?>"><br>

        金額：<br>
        <input type="number" name="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>"><br>

        在庫数：<br>
        <input type="number" name="stock" value="<?= htmlspecialchars($product['stock'] ?? '') ?>"><br>

        商品画像1：<br>
        <?php if (!empty($product['prd_photo_1'])): ?>
            <img src="<?= htmlspecialchars($product['prd_photo_1']) ?>" width="100"><br>
            <input type="hidden" name="old_photo_1" value="<?= htmlspecialchars($product['prd_photo_1']) ?>">
        <?php endif; ?>
        <input type="file" name="prd_photo_1"><br>
        商品画像2：<br>
        <?php if (!empty($product['prd_photo_2'])): ?>
            <img src="<?= htmlspecialchars($product['prd_photo_2']) ?>" width="100"><br>
            <input type="hidden" name="old_photo_2" value="<?= htmlspecialchars($product['prd_photo_2']) ?>">
        <?php endif; ?>
        <input type="file" name="prd_photo_2"><br>
        商品画像3：<br>
        <?php if (!empty($product['prd_photo_3'])): ?>
            <img src="<?= htmlspecialchars($product['prd_photo_3']) ?>" width="100"><br>
            <input type="hidden" name="old_photo_3" value="<?= htmlspecialchars($product['prd_photo_3']) ?>">
        <?php endif; ?>
        <input type="file" name="prd_photo_3"><br>

        商品説明：<br>
        <textarea name="prd_text"><?= htmlspecialchars($product['prd_text'] ?? '') ?></textarea><br>

        <button type="submit"><?= $product ? '更新' : '投稿' ?></button>
    </form>
</div>

<?php require 'footer.php'; ?>
