<?php
require_once __DIR__ . '/controller.php';

// 画面部分（.body-layout）の HTML をキャプチャする
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

// Ajax（JSのfetch）からのリクエストなら、.body-layout だけ返す
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
        // 通常表示時は .body-layout をここに出力
        echo $bodyLayoutHtml;
        ?>
    </div>
</div>
</body>
</html>
