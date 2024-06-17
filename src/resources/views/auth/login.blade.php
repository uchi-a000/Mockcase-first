@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="content">
    <h2 class="login-form__heading heading">ログイン</h2>
    <div class="login-form__inner">
        <form class="login-form__form" action="/login" method="post">
            @csrf
            <div class="login-form__group">
                <input class="login-form__group-input" type="email" name="email" placeholder="メールアドレス" value="{{ old('email') }}" />
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="login-form__group">
                <input class="login-form__group-input" type="password" name="password" placeholder="パスワード" />
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit btn" type="submit">ログイン</button>
            </div>

            <div class="register__link">
                <p class="register-text">アカウントをお持ちでない方はこちらから</p>
                <a class="register-form__btn" href=" /register">会員登録</a>
            </div>
        </form>
    </div>
</div>
@endsection