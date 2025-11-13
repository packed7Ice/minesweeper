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
    <div>
        <div class="section-title">Play</div>
        <p class="section-desc">
            上段のボタンで<strong>「マスを開く」</strong>、下段のボタンで<strong>「旗を立てる / 外す」</strong>ができます。<br>
            すべての安全なマスを開けるとクリアです。
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
    </div>

    <div>
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Board</div>
                <div class="pill pill-outline">
                    <?php echo $rows . ' × ' . $cols . ' / 💣 ' . $mines; ?>
                </div>
            </div>

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
                                        <form method="post" class="cell-form">
                                            <input type="hidden" name="row" value="<?php echo $r; ?>">
                                            <input type="hidden" name="col" value="<?php echo $c; ?>">
                                            <button type="submit" name="action" value="open" class="cell-btn">
                                                Open
                                            </button>
                                            <button type="submit" name="action" value="flag" class="cell-btn">
                                                <?php echo $cell['flag'] ? '⚑ Flag' : 'Flag'; ?>
                                            </button>
                                        </form>
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
