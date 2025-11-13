# minesweeper

flowchart TD

A[開始] --> B[盤サイズ・地雷数の取得]
B --> C[二次元配列 board を生成]
C --> D[ランダムに地雷を配置]
D --> E[各マスの周囲8方向の地雷数を計算]
E --> F[board を $_SESSION に保存]
F --> G[終了]

flowchart TD

A[開始] --> B{対象セルは旗か？}
B -->|YES| Z[何もしない → 終了]
B -->|NO| C{地雷か？}

C -->|YES| D[GAME OVER]
D --> E[全地雷を OPEN]
E --> G[終了]

C -->|NO| F{隣接地雷数は 0？}

F -->|YES| H[周囲セルを再帰的に開く]
H --> I{全非地雷セルが開いた？}

F -->|NO| J[セルのみ開く]
J --> I{全非地雷セルが開いた？}

I -->|YES| K[勝利状態へ]
I -->|NO| G[続行 → 終了]

flowchart TD

A[クリック/タップ入力] --> B[操作モード判定（PC／モバイル）]
B --> C[fetch() により action=open/flag を送信]
C --> D[部分HTML（body-layout）を受信]
D --> E[DOM の該当部分を差し替え]
E --> F[UI の再バインド（イベント再登録）]
F --> G[終了]
