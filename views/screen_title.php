<!-- タイトル画面：導入とスタートボタン -->
<div>
    <div class="section-title">Welcome</div>
    <p class="section-desc">
        シンプルなサーバーサイド実装のマインスイーパーです。<br>
        まずは「ゲームスタート」を押して、使用するマス目と地雷数を選びましょう。
    </p>
    <div class="button-row">
        <form method="post">
            <button type="submit" name="start_from_title" value="1" class="primary-button">
                <span>ゲームスタート</span> <span>▶</span>
            </button>
        </form>
    </div>
</div>

<!-- 仕組みの説明パネル群 -->
<div class="panel-stack panel-stack--title">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">How it works</div>
            <div class="pill pill-outline">Session / PHP Only</div>
        </div>
        <p class="info-text">
            すべてのゲーム状態はサーバー側の <code>$_SESSION</code> で保持されます。<br>
            ページをリロードしても同じ盤面で遊び続けることができます。
        </p>
    </div>
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Next step</div>
            <div class="pill pill-soft">Ready</div>
        </div>
        <p class="info-text">
            ゲームを開始して、PHP だけで作るターン制ゲームの流れを体験してみましょう。
        </p>
    </div>
</div>
