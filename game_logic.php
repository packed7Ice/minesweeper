<?php

// ---------- ゲーム初期化 ----------
function initGame($rows, $cols, $mines)
{
    // 地雷数が多すぎる場合のガード
    $maxMines = $rows * $cols - 1;
    if ($mines > $maxMines) {
        $mines = $maxMines;
    }

    $board = [];
    for ($r = 0; $r < $rows; $r++) {
        $board[$r] = [];
        for ($c = 0; $c < $cols; $c++) {
            $board[$r][$c] = [
                'mine'     => false,
                'adjacent' => 0,
                'open'     => false,
                'flag'     => false,
            ];
        }
    }

    // 地雷をランダム配置
    $placed = 0;
    while ($placed < $mines) {
        $r = rand(0, $rows - 1);
        $c = rand(0, $cols - 1);
        if (!$board[$r][$c]['mine']) {
            $board[$r][$c]['mine'] = true;
            $placed++;
        }
    }

    // 周囲地雷数を計算
    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($board[$r][$c]['mine']) {
                $board[$r][$c]['adjacent'] = -1;
                continue;
            }
            $count = 0;
            for ($dr = -1; $dr <= 1; $dr++) {
                for ($dc = -1; $dc <= 1; $dc++) {
                    if ($dr === 0 && $dc === 0) continue;
                    $nr = $r + $dr;
                    $nc = $c + $dc;
                    if ($nr < 0 || $nr >= $rows || $nc < 0 || $nc >= $cols) continue;
                    if ($board[$nr][$nc]['mine']) {
                        $count++;
                    }
                }
            }
            $board[$r][$c]['adjacent'] = $count;
        }
    }

    $_SESSION['board'] = $board;
    $_SESSION['rows'] = $rows;
    $_SESSION['cols'] = $cols;
    $_SESSION['mines'] = $mines;
    $_SESSION['game_over'] = false;
    $_SESSION['win'] = false;
}

// ---------- 再帰的にマスを開く ----------
function openCell($r, $c)
{
    $rows = $_SESSION['rows'];
    $cols = $_SESSION['cols'];
    $board = &$_SESSION['board'];

    if ($r < 0 || $r >= $rows || $c < 0 || $c >= $cols) return;

    $cell = &$board[$r][$c];

    if ($cell['open'] || $cell['flag']) return;

    $cell['open'] = true;

    // 周囲ゼロなら周囲も開く
    if (!$cell['mine'] && $cell['adjacent'] === 0) {
        for ($dr = -1; $dr <= 1; $dr++) {
            for ($dc = -1; $dc <= 1; $dc++) {
                if ($dr === 0 && $dc === 0) continue;
                openCell($r + $dr, $c + $dc);
            }
        }
    }
}

// ---------- 勝利判定 ----------
function checkWin()
{
    $board = $_SESSION['board'];
    $rows = $_SESSION['rows'];
    $cols = $_SESSION['cols'];
    $mines = $_SESSION['mines'];

    $openCount = 0;
    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($board[$r][$c]['open'] && !$board[$r][$c]['mine']) {
                $openCount++;
            }
        }
    }

    $totalCells = $rows * $cols;
    $safeCells = $totalCells - $mines;

    return $openCount === $safeCells;
}
