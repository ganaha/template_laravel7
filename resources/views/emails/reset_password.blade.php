@component('mail::message')
こんにちは！

以下のリンクからパスワードを再設定してください。

[メールアドレスを認証する]({{ $url }})

もしこのメールに覚えが無い場合は破棄してください。

{{ config('app.name') }}
@endcomponent
