## メモ

### 構成

- Laravel 7.x
- Laradock

### 機能

- マルチ認証(/admin)
    - user と admin のマルチ認証
- 仮会員/本登録(/register)
    - メールアドレス認証後に本登録
- チャット(Pusher)
    - Public Channel: /chat/public
    - Private Channel: /admin/users -> /chat/private    
        - 管理者から個別ユーザーに対する一方通行的な通知・ステータス変更に使える
    - Presence Channel: /chat/presence/1, /chat/presence/2
        - ユーザー同士のチャットルームとして使える
        - 入力中の表示(whisper)
- ビデオチャット(映像/音声)
    - /video
    - 別ユーザーで/videoにアクセスし、◯◯さんと通話を開始する を押下
- API(Sanctum)
- Deploy(Envoy)