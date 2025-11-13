<?php
$board = $_SESSION['board'];
$rows  = $_SESSION['rows'];
$cols  = $_SESSION['cols'];
$mines = $_SESSION['mines'];
$game_over = $_SESSION['game_over'] ?? false;
$win       = $_SESSION['win'] ?? false;

// フラグ数（残り地雷数の目安表示用）
$flags = 0;
foreach ($board as $row) {
    foreach ($row as $cell) {
        if ($cell['flag']) $flags++;
    }
}
$remainingMines = max(0, $mines - $flags);
?>
<div class="body-layout">
    <!-- 左側：説明・ボタン・ステータス -->
    <div>
        <div class="section-title">Play</div>
        <p class="section-desc">
            左クリックでマスを開きます。<br>
            右クリックでフラグの設置・解除ができます。<br>
            タッチデバイスなど右クリックしづらい環境では、下の「操作モード切替」ボタンで
            左クリックの動作を「開く / 旗」に切り替えられます。
        </p>
        <div class="button-row">
            <form method="post">
                <button type="submit" name="reset_game" value="1" class="secondary-button">
                    ↻ 同じ設定でやり直す
                </button>
            </form>
            <form method="post">
                <button type="submit" name="goto_title" value="1" class="ghost-button">
                    ← タイトルへ戻る
                </button>
            </form>
        </div>

        <?php if ($game_over): ?>
            <div class="status <?php echo $win ? 'status-success' : 'status-fail'; ?>">
                <?php if ($win): ?>
                    🎉 クリア！ 全ての安全なマスを開くことができました。
                <?php else: ?>
                    💥 地雷を踏んでしまいました…。リセットして再チャレンジ！
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="status status-neutral">
                残り地雷数（目安）：<span class="info-highlight"><?php echo $remainingMines; ?></span>
            </div>
        <?php endif; ?>

        <!-- クリックモード切り替えボタン -->
        <div style="margin-top: 16px;">
            <button id="clickModeToggle" class="secondary-button" type="button">
                操作モード: 開く（左クリック） / 右クリック: 旗
            </button>
        </div>

        <!-- ズーム（セルサイズ）調整 -->
        <div class="panel" style="margin-top: 18px;">
            <div class="panel-header">
                <div class="panel-title">Zoom</div>
                <div class="pill pill-outline" id="zoomValueLabel">セルサイズ: 30px</div>
            </div>
            <input
                type="range"
                id="cellSizeSlider"
                class="zoom-slider"
                min="18"
                max="48"
                value="30"
            >
            <p class="info-text" style="margin-top: 8px;">
                セルサイズを変更すると、小さい画面でも盤面がカードの枠内に収まりやすくなります。
            </p>
        </div>
    </div>

    <!-- 右側：盤面 -->
    <div>
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Board</div>
                <div class="pill pill-outline">
                    <?php echo $rows . ' × ' . $cols . ' / 💣 ' . $mines; ?>
                </div>
            </div>

            <!-- セル送信用の隠しフォーム（JSから操作） -->
            <form id="cellActionForm" method="post" style="display:none;">
                <input type="hidden" name="row" id="cellRow">
                <input type="hidden" name="col" id="cellCol">
                <input type="hidden" name="action" id="cellAction">
            </form>

            <div class="board-wrapper">
                <table class="board">
                    <?php for ($r = 0; $r < $rows; $r++): ?>
                        <tr>
                            <?php for ($c = 0; $c < $cols; $c++):
                                $cell = $board[$r][$c];
                                ?>
                                <td>
                                    <?php if ($cell['open']): ?>
                                        <?php if ($cell['mine']): ?>
                                            <div class="cell-open">
                                                <span class="mine">💣</span>
                                            </div>
                                        <?php else: ?>
                                            <?php if ($cell['adjacent'] > 0):
                                                $numClass = 'num' . min(8, max(1, $cell['adjacent']));
                                                ?>
                                                <div class="cell-open <?php echo $numClass; ?>">
                                                    <?php echo $cell['adjacent']; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="cell-open empty">&nbsp;</div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button
                                            type="button"
                                            class="cell-interactive"
                                            data-row="<?php echo $r; ?>"
                                            data-col="<?php echo $c; ?>"
                                        >
                                            <?php echo $cell['flag'] ? '⚑' : ''; ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </table>
            </div>
        </div>
    </div>
</div>
