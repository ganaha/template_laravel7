@component('mail::message')
こんにちは！

以下のリンクから登録を完了させてください。

[メールアドレスを認証する]({{ $url }})

もしこのメールに覚えが無い場合は破棄してください。

{{ config('app.name') }}
@endcomponent
