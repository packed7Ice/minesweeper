<?php
session_start();

require_once __DIR__ . '/game_logic.php';

// ---------- 画面状態 ----------
if (!isset($_SESSION['screen'])) {
    $_SESSION['screen'] = 'title'; // title / config / game
}

// ---------- 画面遷移系 ----------

// タイトルへ戻る
if (isset($_POST['goto_title'])) {
    $_SESSION = []; // 全リセット
    session_regenerate_id(true);
    $_SESSION['screen'] = 'title';
}

// タイトル → 設定画面
if (isset($_POST['start_from_title'])) {
    $_SESSION['screen'] = 'config';
}

// 設定画面 → ゲーム開始
if (isset($_POST['start_game'])) {
    $level = $_POST['level'] ?? 'beginner';

    // デフォルト
    $rows = 9;
    $cols = 9;
    $mines = 10;

    switch ($level) {
        case 'intermediate': // 中級
            $rows = 16;
            $cols = 16;
            $mines = 40;
            break;
        case 'expert': // 上級
            $rows = 16;
            $cols = 30;
            $mines = 99;
            break;
        case 'custom': // カスタム
            $rows = max(5, min(30, (int)($_POST['rows'] ?? 9)));
            $cols = max(5, min(30, (int)($_POST['cols'] ?? 9)));
            $mines = max(1, (int)($_POST['mines'] ?? 10));
            break;
        case 'beginner':
        default:
            break;
    }

    // ★ ここがポイント
    // すでにゲーム画面中（screen='game'）で、盤面も存在している場合は
    // 「リロードによるフォーム再送信」とみなして盤面を作り直さない。
    $alreadyGame = (isset($_SESSION['screen']) && $_SESSION['screen'] === 'game' && isset($_SESSION['board']));

    if (!$alreadyGame) {
        // 初回の「スタート」や、設定変更後の開始時だけ新規初期化
        initGame($rows, $cols, $mines);
    }

    $_SESSION['screen'] = 'game';
}

// ---------- ゲーム中の処理（マス操作など） ----------
if ($_SESSION['screen'] === 'game' && !empty($_SESSION['board'])) {
    $board = &$_SESSION['board'];
    $rows  = $_SESSION['rows'];
    $cols  = $_SESSION['cols'];
    $game_over = &$_SESSION['game_over'];
    $win       = &$_SESSION['win'];

    // 同じ設定でリセット
    if (isset($_POST['reset_game'])) {
        initGame($rows, $cols, $_SESSION['mines']);
    }

    // マス操作
    if (isset($_POST['row'], $_POST['col'], $_POST['action']) && !$game_over) {
        $r = (int)$_POST['row'];
        $c = (int)$_POST['col'];
        $action = $_POST['action'];

        if ($r >= 0 && $r < $rows && $c >= 0 && $c < $cols) {
            if ($action === 'flag') {
                if (!$board[$r][$c]['open']) {
                    $board[$r][$c]['flag'] = !$board[$r][$c]['flag'];
                }
            } elseif ($action === 'open') {
                if ($board[$r][$c]['flag']) {
                    // 旗が立っているときは開かない
                } else {
                    if ($board[$r][$c]['mine']) {
                        // 地雷を踏んだ
                        $board[$r][$c]['open'] = true;
                        $game_over = true;
                        $win = false;
                        // 全マス開く
                        for ($rr = 0; $rr < $rows; $rr++) {
                            for ($cc = 0; $cc < $cols; $cc++) {
                                $board[$rr][$cc]['open'] = true;
                            }
                        }
                    } else {
                        openCell($r, $c);
                        if (checkWin()) {
                            $game_over = true;
                            $win = true;
                        }
                    }
                }
            }
        }
    }
}

// ビュー側で使いやすいように変数にしておく
$screen = $_SESSION['screen'] ?? 'title';
