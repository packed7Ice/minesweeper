# minesweeper

## 1. 盤面初期化処理（game_logic.php）
```mermaid
flowchart TD;
    A[開始] --> B[盤サイズ・地雷数の取得];
    B --> C[二次元配列 board を生成];
    C --> D[ランダムに地雷を配置];
    D --> E[各マスの周囲8方向の地雷数を計算];
    E --> F[board を $_SESSION に保存];
    F --> G[終了];
```

## 2. セルを開く処理（openCell）

```mermaid
flowchart TD;
    A[開始] --> B{対象セルは旗か？}
    B -->|YES| Z[何もしない → 終了];
    B -->|NO| C{地雷か？};

    C -->|YES| D[GAME OVER];
    D --> E[全地雷を OPEN];
    E --> G[終了];

    C -->|NO| F{隣接地雷数は 0？};

    F -->|YES| H[周囲セルを再帰的に開く];
    H --> I{全非地雷セルが開いた？};

    F -->|NO| J[セルのみ開く];
    J --> I{全非地雷セルが開いた？};

    I -->|YES| K[勝利状態へ];
    I -->|NO| G[続行 → 終了];
```

## 3. フロントエンド操作処理（app.js）

```mermaid
flowchart TD;
    A[クリック/タップ入力] --> B[操作モード判定（PC／モバイル）];
    B --> C["fetch()"により action=open/flag を送信];
    C --> D[部分HTML（body-layout）を受信];
    D --> E[DOM の該当部分を差し替え];
    E --> F[UI の再バインド（イベント再登録）];
    F --> G[終了];
```

## 4. 画面遷移図（Webページ遷移）

```mermaid
graph TD;

    T[タイトル画面<br/>screen = title] -->|Start ボタン| C[設定画面<br/>screen = config];

    C -->|難易度決定 / Start| G[ゲーム画面<br/>screen = game];
    C -->|戻る / Back| T;

    G -->|Back ボタン| C;
    G -->|Reset ボタン| G;

    %% ゲーム結果は画面自体は game のままメッセージ表示
    G -->|クリア / すべての非地雷マスをOpen| GR[ゲーム画面内: CLEAR 表示];
    G -->|地雷を踏む| GL[ゲーム画面内: GAME OVER 表示];

    GR -->|もう一度プレイ| C;
    GL -->|もう一度プレイ| C;

```

## 5. ゲーム状態遷移図（ロジック側）

```mermaid
stateDiagram-v2
    [*] --> Init : アクセス or Reset

    Init: 盤面初期化
    Init --> Ready : 地雷配置 / 隣接数計算 / $_SESSION 保存

    Ready: プレイ準備完了（全マス閉じている）
    Ready --> Playing : 最初のマスを開く / 旗を立てる

    Playing: プレイ中
    Playing --> Clear : 全ての非地雷マスが open
    Playing --> GameOver : 地雷マスを open

    Clear: クリア状態（盤面は固定）
    GameOver: ゲームオーバー状態（地雷表示）

    Clear --> Init : 再スタート（Reset / New Game）
    GameOver --> Init : 再スタート（Reset / New Game）

    Playing --> Playing : open / flag 操作を継続

```

## 6.クリック操作の詳細フロー（open / flag 判定）

```mermaid
flowchart TD
    A[ユーザー入力（クリック/タップ）] --> B{PCの操作か？}
    B -->|Yes| C{右クリックか？}
    B -->|No| D{現在のモード}

    C -->|Yes| E[アクション = flag]
    C -->|No| F[アクション = open]

    D -->|open| F2[アクション = open]
    D -->|flag| E2[アクション = flag]

    E --> G["sendCellAction(flag)"]
    E2 --> G

    F --> H["sendCellAction(open)"]
    F2 --> H

    G --> I["PHP(controller.php)へ送信"]
    H --> I

    I --> J["更新HTML(body-layout)を受信"]
    J --> K[DOM差し替え & UI再バインド]
    K --> L[処理完了]
```

## 7.PC / Mobile 操作モード切替フロー

```mermaid
stateDiagram-v2
    [*] --> Detect

    Detect: 端末判定（isTouch）
    Detect --> PC: PC操作
    Detect --> Mobile: タッチ操作

    state PC {
        [*] --> PC_Default
        PC_Default: 左クリック=open\n<br/>右クリック=flagclick flag
        PC_Default --> PC_Default : 操作継続
    }

    state Mobile {
        [*] --> M_Open
        M_Open: モード=open\n<br/>ボタン：開く
        M_Open --> M_Flag : モード切替
        M_Open --> M_Open : セルタップ→open

        M_Flag: モード=flag\n<br/>ボタン：旗
        M_Flag --> M_Open : モード切替
        M_Flag --> M_Flag : セルタップ→flag
    }

    PC --> Detect : 再描画
    Mobile --> Detect : 再描画
```