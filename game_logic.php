<?php

// ---------- ゲーム開始時の盤面を構築 ----------
function initGame($rows, $cols, $mines)
{
    // 地雷が多すぎる場合は最後の1マスは必ず安全にする
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

    // 地雷をランダムに設置し、重複設置は避ける
    $placed = 0;
    while ($placed < $mines) {
        $r = rand(0, $rows - 1);
        $c = rand(0, $cols - 1);
        if (!$board[$r][$c]['mine']) {
            $board[$r][$c]['mine'] = true;
            $placed++;
        }
    }

    // 各マスについて周囲8方向の地雷数を事前計算
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

    // 初期化済みの情報をすべてセッションに残し、次リクエストから参照する
    $_SESSION['board'] = $board;
    $_SESSION['rows'] = $rows;
    $_SESSION['cols'] = $cols;
    $_SESSION['mines'] = $mines;
    $_SESSION['game_over'] = false;
    $_SESSION['win'] = false;
}

// ---------- 再帰的にマスを開き、0なら周囲も広げる ----------
function openCell($r, $c)
{
    $rows = $_SESSION['rows'];
    $cols = $_SESSION['cols'];
    $board = &$_SESSION['board'];

    // 盤面外の座標は無視
    if ($r < 0 || $r >= $rows || $c < 0 || $c >= $cols) return;

    $cell = &$board[$r][$c];

    // 既に開いたor旗があるセルも処理不要
    if ($cell['open'] || $cell['flag']) return;

    $cell['open'] = true;

    // 周囲ゼロなら再帰的に近隣を開放する
    if (!$cell['mine'] && $cell['adjacent'] === 0) {
        for ($dr = -1; $dr <= 1; $dr++) {
            for ($dc = -1; $dc <= 1; $dc++) {
                if ($dr === 0 && $dc === 0) continue;
                openCell($r + $dr, $c + $dc);
            }
        }
    }
}

// ---------- 全ての安全マスを開けたかを判定 ----------
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
