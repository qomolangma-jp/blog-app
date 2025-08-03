<?php
require 'header.php';

// ログインしていない場合はログインページにリダイレクト
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
// 所有ポイントを取得
$stmt = $pdo->prepare("SELECT point FROM users_sub WHERE user_id = ?");
$stmt->execute([$user['id']]);
$userSub = $stmt->fetch();
$points = $userSub ? $userSub['point'] : 0; // ポイントがない場合は 0 を表示

// ポイント購入処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_points'])) {
    $purchasePoints = (int)$_POST['purchase_points'];

    // トランザクション開始
    $pdo->beginTransaction();
    try {
        // ポイントを更新
        $stmt = $pdo->prepare("UPDATE users_sub SET point = point + ? WHERE user_id = ?");
        $stmt->execute([$purchasePoints, $user['id']]);

        // 購入履歴を保存
        $stmt = $pdo->prepare("INSERT INTO purchase_history (user_id, points) VALUES (?, ?)");
        $stmt->execute([$user['id'], $purchasePoints]);

        // コミット
        $pdo->commit();

        // セッションにメッセージを保存
        $_SESSION['message'] = "ポイントを購入しました！";

        // POST-Redirect-GET パターン: リダイレクト
        header("Location: mypage.php");
        exit;

    } catch (Exception $e) {
        // ロールバック
        $pdo->rollBack();
        $message = "エラーが発生しました: " . $e->getMessage();
    }
}

// セッションからメッセージを取得して削除
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>マイページ</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">マイページ</h1>

    <div class="card">
        <div class="card-header">
            ユーザー情報
        </div>
        <div class="card-body">
            <h5 class="card-title">ようこそ、<?= htmlspecialchars($user['username']) ?> さん</h5>
            <p class="card-text">
                <strong>メールアドレス:</strong> <?= htmlspecialchars($user['email']) ?><br>
                <strong>登録日:</strong> <?= htmlspecialchars($user['created_at']) ?><br>
                <strong>所有ポイント:</strong> <?= htmlspecialchars($points) ?> ポイント
            </p>
            <a href="edit_profile.php" class="btn btn-primary">プロフィール編集</a>
            <a href="logout.php" class="btn btn-danger">ログアウト</a>
        </div>
    </div>

    <div class="mt-5">
        <h2>ポイント購入</h2>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" class="d-flex align-items-center">
            <label for="purchase_points" class="me-3">購入ポイント:</label>
            <select name="purchase_points" id="purchase_points" class="form-select me-3" style="width: auto;">
                <option value="1000">1000 ポイント</option>
                <option value="2000">2000 ポイント</option>
                <option value="3000">3000 ポイント</option>
                <option value="4000">4000 ポイント</option>
                <option value="5000">5000 ポイント</option>
                <option value="10000">10000 ポイント</option>
            </select>
            <button type="submit" class="btn btn-success">購入</button>
        </form>
    </div>    

    <div class="mt-5">
        <h2>購入履歴</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>購入日時</th>
                        <th>購入ポイント</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 購入履歴を取得
                    $stmt = $pdo->prepare("SELECT points, created_at FROM purchase_history WHERE user_id = ? ORDER BY created_at DESC");
                    $stmt->execute([$user['id']]);
                    $history = $stmt->fetchAll();

                    foreach ($history as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['created_at']) ?></td>
                            <td><?= htmlspecialchars($entry['points']) ?> ポイント</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        <h2>あなたの投稿</h2>
        <div class="row">
            <?php
            // ユーザーの投稿を取得
            $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user['id']]);
            $posts = $stmt->fetchAll();

            if ($posts):
                foreach ($posts as $post): ?>
                    <div class="col-lg-3 col-md-4 mb-3">
                        <div class="card">
                            <?php if ($post['image_path']): ?>
                                <img src="<?= $post['image_path'] ?>" class="card-img-top">
                            <?php else: ?>
                                <img src="no_image.png" class="card-img-top">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                                </h5>
                                <p class="card-text">
                                    <?= nl2br(htmlspecialchars(mb_strimwidth($post['content'], 0, 100, '...'))) ?>
                                </p>
                            </div>
                            <div class="card-footer">
                                <small>投稿日: <?= $post['created_at'] ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
            else: ?>
                <p>投稿がありません。</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>