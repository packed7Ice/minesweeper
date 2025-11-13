document.addEventListener('DOMContentLoaded', () => {
    // =======================
    //  1. セルクリック処理
    // =======================
    const actionForm = document.getElementById('cellActionForm');
    const rowInput   = document.getElementById('cellRow');
    const colInput   = document.getElementById('cellCol');
    const actionInput = document.getElementById('cellAction');
    const cells = document.querySelectorAll('.cell-interactive');
    const modeToggleBtn = document.getElementById('clickModeToggle');

    let clickMode = 'open'; // 'open' or 'flag'

    function submitCellAction(row, col, action) {
        if (!actionForm) return;
        rowInput.value = row;
        colInput.value = col;
        actionInput.value = action;
        actionForm.submit();
    }

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

            // 右クリック（フラグ専用）
            cell.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                submitCellAction(row, col, 'flag');
            });
        });
    }

    // =======================
    //  2. セルサイズ（ズーム）
    // =======================
    const slider = document.getElementById('cellSizeSlider');
    const zoomLabel = document.getElementById('zoomValueLabel');

    function applyCellSize(size) {
        document.documentElement.style.setProperty('--cell-size', size + 'px');
        if (zoomLabel) {
            zoomLabel.textContent = 'セルサイズ: ' + size + 'px';
        }
    }

    if (slider) {
        let saved = localStorage.getItem('ms_cell_size');
        let size = saved ? parseInt(saved, 10) : parseInt(slider.value, 10);
        if (!Number.isFinite(size)) size = 30;
        slider.value = size;
        applyCellSize(size);

        slider.addEventListener('input', () => {
            const v = parseInt(slider.value, 10);
            applyCellSize(v);
            localStorage.setItem('ms_cell_size', v);
        });
    } else {
        // JSだけでデフォルト値を設定（保険）
        applyCellSize(30);
    }
});
