<?php
require_once __DIR__ . '/controller.php';

// 画面本体（.body-layout）を一度バッファに貯めてAJAX/通常描画で使い回す
ob_start();
?>
<div class="body-layout">
    <?php if ($screen === 'title'): ?>
        <?php include __DIR__ . '/views/screen_title.php'; ?>
    <?php elseif ($screen === 'config'): ?>
        <?php include __DIR__ . '/views/screen_config.php'; ?>
    <?php elseif ($screen === 'game'): ?>
        <?php include __DIR__ . '/views/screen_game.php'; ?>
    <?php endif; ?>
</div>
<?php
$bodyLayoutHtml = ob_get_clean();

// fetch経由の差分更新リクエストではbody-layoutだけ返す
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          $_SERVER['HTTP_X_REQUESTED_WITH'] === 'fetch';

if ($isAjax) {
    echo $bodyLayoutHtml;
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>PHP Minesweeper</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <!-- jsを読み込む -->
    <script src="app.js" defer></script>
</head>
<body>
<div class="app">
    <div class="card">
        <?php include __DIR__ . '/views/header.php'; ?>
        <?php
        // 通常描画ではここでレイアウト全体を出力する
        echo $bodyLayoutHtml;
        ?>
    </div>
</div>
</body>
</html>
