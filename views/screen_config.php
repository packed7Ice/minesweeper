    <div>
        <div class="section-title">Setup</div>
        <p class="section-desc">
            難易度またはマス目のサイズを選んでください。<br>
            後から同じ設定で「リセット」しながら何度でも遊べます。
        </p>
        <div class="button-row">
            <form method="post">
                <button type="submit" name="goto_title" value="1" class="ghost-button">
                    ← タイトルへ戻る
                </button>
            </form>
        </div>
    </div>

    <div>
        <form method="post" class="panel config-panel">
            <div class="panel-header">
                <div class="panel-title">Difficulty</div>
                <div class="pill pill-outline">Preset &amp; Custom</div>
            </div>
            <div class="config-content">
                <div class="config-difficulty">
                    <div class="field">
                        <div class="field-label">難易度を選択</div>
                        <div class="radio-row radio-row--split">
                            <label class="radio-chip">
                                <input type="radio" name="level" value="beginner" checked>
                                <span class="radio-label">ビギナー (9×9 / 地雷 10)</span>
                            </label>
                            <label class="radio-chip">
                                <input type="radio" name="level" value="intermediate">
                                <span class="radio-label">中級 (16×16 / 地雷 40)</span>
                            </label>
                            <label class="radio-chip">
                                <input type="radio" name="level" value="expert">
                                <span class="radio-label">上級 (16×30 / 地雷 99)</span>
                            </label>
                            <label class="radio-chip">
                                <input type="radio" name="level" value="custom">
                                <span class="radio-label">カスタム</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="config-custom">
                    <div class="form-grid" style="margin-top: 10px;">
                        <div class="field">
                            <div class="field-label">行数 (5〜30, カスタム用)</div>
                            <input type="number" name="rows" class="field-input" value="9" min="5" max="30">
                        </div>
                        <div class="field">
                            <div class="field-label">列数 (5〜30, カスタム用)</div>
                            <input type="number" name="cols" class="field-input" value="9" min="5" max="30">
                        </div>
                        <div class="field">
                            <div class="field-label">地雷数 (1以上, カスタム用)</div>
                            <input type="number" name="mines" class="field-input" value="10" min="1">
                        </div>
                    </div>

                    <div class="button-row" style="margin-top: 16px;">
                        <button type="submit" name="start_game" value="1" class="primary-button">
                            この設定でスタート ▶
                        </button>
                    </div>
                    <p class="info-text" style="margin-top: 10px;">
                        ※ カスタムを選んでいる場合のみ、上の数値が反映されます。<br>
                        ※ 地雷数がマス数−1 を超える場合、自動的に調整されます。
                    </p>
                </div>
            </div>
        </form>
    </div>
