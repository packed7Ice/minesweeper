// app.js
// 盤面UI操作とAJAX通信をまとめて制御するクライアントスクリプト

window.addEventListener('load', () => {
    let clickMode = 'open'; // 'open' or 'flag'
    const endpoint = window.location.href; // index.phpへのPOST先（現在のURLをそのまま利用）

    // -----------------------------
    // セルサイズ自動調整（レスポンシブ）
    // -----------------------------
    function recalcCellSize() {
        const board = document.querySelector('.board');
        const boardWrapper = board ? board.closest('.board-wrapper') : null;
        if (!board || !boardWrapper) return;

        const rows = parseInt(board.dataset.rows || '9', 10);
        const cols = parseInt(board.dataset.cols || '9', 10);

        const wrapperWidth = boardWrapper.clientWidth || window.innerWidth;
        const maxBoardWidth = wrapperWidth - 4;

        const viewportHeight = window.innerHeight;
        const availableHeight = Math.max(200, viewportHeight - 260);

        const sizeFromWidth = maxBoardWidth / cols;
        const sizeFromHeight = availableHeight / rows;

        // 横幅と縦幅の制約を両立できる最小値を採用し、極端な値は丸める
        let size = Math.floor(Math.min(sizeFromWidth, sizeFromHeight, 48));
        size = Math.max(16, size);

        document.documentElement.style.setProperty('--cell-size', size + 'px');
        console.log('[Minesweeper] cell size:', size);
    }

    let resizeTimer = null;
    // 連続リサイズ時に計算を絞るため簡易デバウンスを入れる
    window.addEventListener('resize', () => {
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(recalcCellSize, 80);
    });

    // -----------------------------
    // 操作モードラベル更新
    // -----------------------------
    function updateModeLabel() {
        const modeToggleBtn = document.getElementById('clickModeToggle');
        if (!modeToggleBtn) return;

        if (clickMode === 'open') {
            modeToggleBtn.textContent = '操作切替: 開く';
        } else {
            modeToggleBtn.textContent = '操作切替: 旗';
        }
    }

    // -----------------------------
    // 盤面HTMLの差し替え＋再バインド
    // -----------------------------
    function replaceBodyLayoutFromHtml(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newLayout = doc.querySelector('.body-layout');
        const currentLayout = document.querySelector('.body-layout');
        if (newLayout && currentLayout) {
            currentLayout.replaceWith(newLayout);
            // 差し替えたので、イベントを再セット
            bindGameUI();
            // 新しい盤面に対してセルサイズを再計算
            recalcCellSize();
        } else {
            console.warn('body-layout not found in HTML, fallback to full reload');
            // 最悪の場合は通常のリロードに戻す
            window.location.reload();
        }
    }

    // -----------------------------
    // セルクリック → Ajaxでindex.phpにPOST
    // -----------------------------
    async function sendCellAction(row, col, action) {
        const params = new URLSearchParams();
        params.append('row', row);
        params.append('col', col);
        params.append('action', action);

        try {
            const resp = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'fetch'
                },
                body: params.toString(),
                cache: 'no-store'
            });
            if (!resp.ok) {
                throw new Error('HTTP ' + resp.status);
            }
            // 差分HTML（.body-layout）だけを受け取り、DOM差し替えで高速化
            const html = await resp.text();
            replaceBodyLayoutFromHtml(html);
        } catch (e) {
            console.error('Ajax failed, fallback submit', e);
            // フェイルセーフ：従来のフォーム送信で対応
            const form = document.getElementById('cellActionForm');
            const rowInput = document.getElementById('cellRow');
            const colInput = document.getElementById('cellCol');
            const actionInput = document.getElementById('cellAction');
            if (form && rowInput && colInput && actionInput) {
                rowInput.value = row;
                colInput.value = col;
                actionInput.value = action;
                form.submit();
            }
        }
    }

    // -----------------------------
    // ゲームUIにイベントをバインド
    // ※ HTML差し替え後にも呼ばれる
    // -----------------------------
    function bindGameUI() {
        console.log('[Minesweeper] bindGameUI');

        const cells = document.querySelectorAll('.cell-interactive');
        const modeToggleBtn = document.getElementById('clickModeToggle');

        // セルクリック
        cells.forEach(cell => {
            const row = cell.dataset.row;
            const col = cell.dataset.col;

            // 左クリック
            cell.addEventListener('click', (e) => {
                e.preventDefault();
                const action = (clickMode === 'flag') ? 'flag' : 'open';
                sendCellAction(row, col, action);
            });

            // 右クリック（常に旗トグル）
            cell.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                sendCellAction(row, col, 'flag');
            });
        });

        // ★ スマホ判定（タッチデバイスでなければボタンを隠す）
        if (modeToggleBtn) {
            const isTouchDevice =
                ('ontouchstart' in window) ||
                navigator.maxTouchPoints > 0 ||
                navigator.msMaxTouchPoints > 0;

            if (!isTouchDevice) {
                // PC では完全に非表示（CSSだけだとキーボードフォーカスなどで見える場合もあるので念のため）
                modeToggleBtn.style.display = 'none';
            } else if (!modeToggleBtn.dataset.bound) {
                modeToggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    clickMode = (clickMode === 'open') ? 'flag' : 'open';
                    updateModeLabel();
                });
                modeToggleBtn.dataset.bound = '1';
            }
        }

        updateModeLabel();
        recalcCellSize();
    }

    // 初期化
    bindGameUI();
});
