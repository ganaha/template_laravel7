## テンプレート

### 構成

- Laravel 7.x
- Laradock

### 機能

- マルチ認証(/admin)
- 仮会員/本登録
- チャット(ブロードキャスト)
    - Public Channel: /chat/public
    - Private Channel: /admin/users -> /chat/private    
        - 管理者から個別ユーザーに対する一方通行な通知・ステータス変更に使える
    - Presence Channel: /chat/presence/1, /chat/presence/2
        - ユーザー同士のチャットルームとして使える
        - 入力中の表示
        