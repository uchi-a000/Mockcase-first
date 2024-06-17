@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css?0000') }}">
@endsection

@section('content')
<div class="content__head">
    <div class="content__head--inner">
        <h2 class="email__form">メールアドレスの確認</h2>
        <p class="email__form--content">登録ありがとうございます！
            <br /> 利用を開始する前に、メールに記載されたリンクをクリックして、ログインをしてください。
            <br />もしメールが届かない場合は、別のリンクを送信します。
        </p>

        @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success" role="alert">
            メールアドレスへの確認用リンクを再送信しました。
        </div>
        @endif

        @if ($errors->has('email'))
        <div class="alert alert-danger" role="alert">
            {{ $errors->first('email') }}
        </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="email--form__submit" type="submit">確認メールを再送信</button>
        </form>
        <div class="register--form">
            <a class="register--form__btn" href="/register">会員登録画面へ</a>
        </div>
    </div>
</div>
@endsection