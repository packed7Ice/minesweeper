<?php
session_start(); // 盤面状態を維持するため必ずセッションを開始

require_once __DIR__ . '/game_logic.php';

// ---------- 画面状態の初期化 ----------
if (!isset($_SESSION['screen'])) {
    $_SESSION['screen'] = 'title'; // title / config / game
}

// ---------- 画面遷移ハンドリング ----------

// タイトルに戻るリクエストが来たらセッションを初期化して完全にリセット
if (isset($_POST['goto_title'])) {
    $_SESSION = [];
    session_regenerate_id(true); // セッション固定化対策も兼ねてIDを再発行
    $_SESSION['screen'] = 'title';
}

// タイトルから設定画面へ遷移
if (isset($_POST['start_from_title'])) {
    $_SESSION['screen'] = 'config';
}

// 設定画面からゲーム開始
if (isset($_POST['start_game'])) {
    $level = $_POST['level'] ?? 'beginner';

    // デフォルトの盤面設定（ビギナー）
    $rows = 9;
    $cols = 9;
    $mines = 10;

    // 送信された難易度に応じて盤面サイズと地雷数を切り替える
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

    // 既にゲーム画面でプレイ中かどうかを判定し、無駄な再初期化を避ける
    $alreadyGame = (isset($_SESSION['screen']) && $_SESSION['screen'] === 'game' && isset($_SESSION['board']));

    if (!$alreadyGame) {
        // 初回開始または設定変更時のみ盤面を初期化
        initGame($rows, $cols, $mines);
    }

    // 次の描画リクエストではゲーム画面を開く
    $_SESSION['screen'] = 'game';
}

// ---------- ゲーム中の入力処理 ----------
// 盤面が存在する場合だけ操作リクエストを適用
if ($_SESSION['screen'] === 'game' && !empty($_SESSION['board'])) {
    $board = &$_SESSION['board'];
    $rows  = $_SESSION['rows'];
    $cols  = $_SESSION['cols'];
    $game_over = &$_SESSION['game_over'];
    $win       = &$_SESSION['win'];

    // 同じ設定で遊び直したい場合のリセット
    if (isset($_POST['reset_game'])) {
        initGame($rows, $cols, $_SESSION['mines']);
    }

    // マスの開閉・旗操作リクエスト
    if (isset($_POST['row'], $_POST['col'], $_POST['action']) && !$game_over) {
        $r = (int)$_POST['row'];
        $c = (int)$_POST['col'];
        $action = $_POST['action'];

        // 盤面外の座標リクエストは無視する
        if ($r >= 0 && $r < $rows && $c >= 0 && $c < $cols) {
            if ($action === 'flag') {
                // 未オープンのマスだけ旗のON/OFFを許可
                if (!$board[$r][$c]['open']) {
                    $board[$r][$c]['flag'] = !$board[$r][$c]['flag'];
                }
            } elseif ($action === 'open') {
                if ($board[$r][$c]['flag']) {
                    // 旗が立っている場合は安全のため開かない
                } else {
                    if ($board[$r][$c]['mine']) {
                        // 地雷を踏んだ場合はゲームオーバーにして全マスを開示
                        $board[$r][$c]['open'] = true;
                        $game_over = true;
                        $win = false;
                        for ($rr = 0; $rr < $rows; $rr++) {
                            for ($cc = 0; $cc < $cols; $cc++) {
                                $board[$rr][$cc]['open'] = true;
                            }
                        }
                    } else {
                        // 安全マスなら連鎖的に開き、勝利判定を行う
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

// ビュー側で参照しやすいように表示すべき画面名を渡す
$screen = $_SESSION['screen'] ?? 'title';
