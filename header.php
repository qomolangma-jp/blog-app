<?php
require 'db.php';
session_start();
// カテゴリ一覧を取得
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// GETパラメータを取得
$keyword = $_GET['keyword'] ?? '';
$category_id = $_GET['category_id'] ?? '';


?>

<!DOCTYPE html>
<html>
<head>
    <title>ブログ一覧</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body>
<header class="p-3 mb-3 text-bg-dark">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-decoration-none">
                <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="create.php" class="nav-link px-2 text-white">ブログ作成</a></li>
                <li><a href="index.php" class="nav-link px-2 text-white">ブログ一覧</a></li>
                <li><a href="create_puroduct.php" class="nav-link px-2 text-white">商品登録</a></li>
                <li><a href="products_list.php" class="nav-link px-2 text-white">商品一覧</a></li>
            </ul>

            <form method="GET" action="index.php" class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
                <div class="input-group">
                    <select class="form-select" name="category_id">
                        <option value="">すべて</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input value="<?= htmlspecialchars($keyword) ?>" name="keyword" type="search" class="form-control" placeholder="検索してね" aria-label="Search">
                    <button class="btn btn-outline-secondary" type="submit">検索開始</button>
                </div>
            </form>

            <div class="dropdown text-end">
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu text-small">
                        <li><span class="dropdown-item"><?= htmlspecialchars($_SESSION['user']['username']) ?> さん</span></li>
                        <li><a class="dropdown-item" href="mypage.php">マイページ</a></li>
                        <li><a class="dropdown-item" href="create.php">新規投稿</a></li>
                        <li><a class="dropdown-item" href="logout.php">ログアウト</a></li>
                    </ul>
                <?php else: ?>
                    <p>ログインしていません。</p>
                    <a href="login.php">ログイン</a>
                    <a href="register.php">新規登録</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
