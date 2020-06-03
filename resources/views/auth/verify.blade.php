@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">仮会員登録完了</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                        メールアドレス宛に再送信しました。
                        </div>
                    @endif

                    ご本人様確認のため、入力したメールアドレス宛に本登録の案内メールを送信しました。<br>
                    <br>
                    案内メール本文内のURLにアクセスしていただき、登録を完了させてください。<br>
                    <br>
                    メールが届いていない場合下記をクリックしてください。<br>
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">再送信する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
