// app.js

window.addEventListener('load', () => {
    const SCROLL_KEY = 'ms_scroll_y';

    // =======================
    //  0. スクロール位置の保存・復元
    // =======================
    function saveScrollPosition() {
        try {
            sessionStorage.setItem(SCROLL_KEY, String(window.scrollY || 0));
        } catch (e) {
            console.warn('scroll save failed:', e);
        }
    }

    function restoreScrollPosition() {
        try {
            const saved = sessionStorage.getItem(SCROLL_KEY);
            if (saved !== null) {
                const y = parseInt(saved, 10);
                if (Number.isFinite(y)) {
                    window.scrollTo(0, y);
                }
                sessionStorage.removeItem(SCROLL_KEY);
            }
        } catch (e) {
            console.warn('scroll restore failed:', e);
        }
    }

    // すべてのフォーム送信前にスクロール位置を保存
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            saveScrollPosition();
        });
    });

    // =======================
    //  1. セルクリック処理
    // =======================
    const actionForm  = document.getElementById('cellActionForm');
    const rowInput    = document.getElementById('cellRow');
    const colInput    = document.getElementById('cellCol');
    const actionInput = document.getElementById('cellAction');
    const cells       = document.querySelectorAll('.cell-interactive');
    const modeToggleBtn = document.getElementById('clickModeToggle');

    console.log('[Minesweeper] app.js loaded');
    console.log('cells found:', cells.length);

    let clickMode = 'open'; // 'open' or 'flag'

    function submitCellAction(row, col, action) {
        if (!actionForm || !rowInput || !colInput || !actionInput) {
            console.warn('cellActionForm or inputs not found');
            return;
        }
        rowInput.value = row;
        colInput.value = col;
        actionInput.value = action;
        // form.submit() でも submit イベントは発火しないので手動で保存
        saveScrollPosition();
        actionForm.submit();
    }

    // ◆ 操作モード切り替えボタン（開く/旗）
    if (modeToggleBtn) {
        function updateModeLabel() {
            if (clickMode === 'open') {
                modeToggleBtn.textContent = '操作モード: 開く（左クリック） / 右クリック: 旗';
            } else {
                modeToggleBtn.textContent = '操作モード: 旗（左クリック） / 右クリック: 旗';
            }
        }
        updateModeLabel();

        modeToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            clickMode = (clickMode === 'open') ? 'flag' : 'open';
            updateModeLabel();
        });
    }

    // ◆ マスへのイベント付与
    if (cells.length && actionForm) {
        cells.forEach(cell => {
            const row = cell.dataset.row;
            const col = cell.dataset.col;

            // 左クリック
            cell.addEventListener('click', (e) => {
                e.preventDefault();
                const action = (clickMode === 'flag') ? 'flag' : 'open';
                submitCellAction(row, col, action);
            });

            // 右クリック（常に旗トグル）
            cell.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                submitCellAction(row, col, 'flag');
            });
        });
    } else {
        console.log('no cells or no actionForm yet (タイトル/設定画面なら正常)');
    }

    // =======================
    //  2. セルサイズ自動調整（レスポンシブ）
    // =======================
    const board = document.querySelector('.board');
    const boardWrapper = board ? board.closest('.board-wrapper') : null;

    function recalcCellSize() {
        if (!board || !boardWrapper) return;

        const rows = parseInt(board.dataset.rows || '9', 10);
        const cols = parseInt(board.dataset.cols || '9', 10);

        const wrapperWidth = boardWrapper.clientWidth || window.innerWidth;
        const maxBoardWidth = wrapperWidth - 4;

        const viewportHeight = window.innerHeight;
        const availableHeight = Math.max(200, viewportHeight - 260);

        const sizeFromWidth = maxBoardWidth / cols;
        const sizeFromHeight = availableHeight / rows;

        let size = Math.floor(Math.min(sizeFromWidth, sizeFromHeight, 48));
        size = Math.max(16, size);

        document.documentElement.style.setProperty('--cell-size', size + 'px');
        console.log('cell size recalced:', size);
    }

    recalcCellSize();

    let resizeTimer = null;
    window.addEventListener('resize', () => {
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            recalcCellSize();
        }, 80);
    });

    // セルサイズ計算が終わった後にスクロール復元
    setTimeout(() => {
        restoreScrollPosition();
    }, 0);
});
