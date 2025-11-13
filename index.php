<?php
require_once __DIR__ . '/controller.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>PHP Minesweeper</title>
    <link rel="stylesheet" href="style.css">
        <!-- ★ 追加：外部JSを読み込む -->
    <script src="app.js" defer></script>
</head>
<body>
<div class="app">
    <div class="card">
        <?php include __DIR__ . '/views/header.php'; ?>

        <?php
        // 画面ごとにテンプレートを分岐
        if ($screen === 'title') {
            include __DIR__ . '/views/screen_title.php';
        } elseif ($screen === 'config') {
            include __DIR__ . '/views/screen_config.php';
        } elseif ($screen === 'game') {
            include __DIR__ . '/views/screen_game.php';
        }
        ?>
    </div>
</div>
</body>
</html>
