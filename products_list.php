<?php
require 'header.php';
require 'auth.php';

// DB接続を追加
require 'db.php'; // ←DB接続ファイル名に合わせて変更してください

// 商品一覧取得（JOINで種別名取得）
$stmt = $pdo->query("
    SELECT p.*, pt.prd_type_name 
    FROM products p
    LEFT JOIN products_type pt ON p.prd_type_id = pt.prd_type_id
    ORDER BY p.products_id DESC
");
if ($stmt) {
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = [];
    echo "<p>商品情報の取得に失敗しました。</p>";
}
?>

<div class="container">
    <h2>商品一覧</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>商品名</th>
                <th>種別名</th>
                <th>金額</th>
                <th>在庫数</th>
                <th>画像1</th>
                <th>画像2</th>
                <th>画像3</th>
                <th>説明</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $prd): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($prd['products_id']) ?>
                    <a class="btn btn-danger btn-sm" href="create_puroduct.php?id=<?= htmlspecialchars($prd['products_id']) ?>">編集</a>
                </td>
                <td><?= htmlspecialchars($prd['products_name']) ?></td>
                <td><?= htmlspecialchars($prd['prd_type_name']) ?></td>
                <td><?= htmlspecialchars($prd['price']) ?></td>
                <td><?= htmlspecialchars($prd['stock']) ?></td>
                <td>
                    <?php if ($prd['prd_photo_1']): ?>
                        <img src="<?= htmlspecialchars($prd['prd_photo_1']) ?>" width="80">
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($prd['prd_photo_2']): ?>
                        <img src="<?= htmlspecialchars($prd['prd_photo_2']) ?>" width="80">
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($prd['prd_photo_3']): ?>
                        <img src="<?= htmlspecialchars($prd['prd_photo_3']) ?>" width="80">
                    <?php endif; ?>
                </td>
                <td><?= nl2br(htmlspecialchars($prd['prd_text'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'footer.php'; ?>