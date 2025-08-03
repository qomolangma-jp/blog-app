<?php require 'header.php'; ?>


<?php
$sql = "
SELECT posts.*, users.username, categories.name AS category_name
FROM posts
JOIN users ON posts.user_id = users.id
JOIN categories ON posts.category_id = categories.id
WHERE 1=1
";

// 検索条件をSQLに追加
$params = [];

if ($category_id !== '') {
    $sql .= " AND posts.category_id = ?";
    $params[] = $category_id;
}

if ($keyword !== '') {
    $sql .= " AND (posts.title LIKE ? OR posts.content LIKE ? OR posts.memo LIKE ?)";
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
}

$sql .= " ORDER BY posts.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
?>

<div class="container">
    <div class="row">

        <?php while ($row = $stmt->fetch()): ?>
            <div class="col-lg-3 col-md-4 mb-3">
                <div class="card">
                    <?php if ($row['image_path']): ?>
                        <img src="<?= $row['image_path'] ?>">
                    <?php else: ?>
                        <img src="no_image.png">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a>
                        </h5>
                        <p class="card-text">
                            <?= nl2br(htmlspecialchars(mb_strimwidth($row['content'], 0, 100, '...'))) ?>
                        </p>

                        <?php if (isset($_SESSION['user'])): ?>
                            <a class="btn btn-primary" href="edit.php?id=<?= $row['id'] ?>">編集</a>
                            <a class="btn btn-danger" href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <small>
                            投稿者: <?= htmlspecialchars($row['username']) ?> |
                            カテゴリ: <?= htmlspecialchars($row['category_name']) ?> |
                            投稿日: <?= $row['created_at'] ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>

<?php require 'footer.php'; ?>
