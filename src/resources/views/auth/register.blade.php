@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="content">
    <h2 class="register-form__heading heading">会員登録</h2>
    <div class="register-form__inner">
        <form class="register-form__form" action="/register" method="post">
            @csrf
            <div class="register-form__group">
                <input class="register-form__group-input" type="text" name="name" placeholder="名前" value="{{ old('name') }}" />
                <div class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="register-form__group">
                <input class="register-form__group-input" type="email" name="email" placeholder="メールアドレス" value="{{ old('email') }}" />
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="register-form__group">
                <input class="register-form__group-input" type="password" name="password" placeholder="パスワード" />
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="register-form__group">
                <input class="register-form__group-input" type="password" name="password_confirmation" placeholder="確認用パスワード" />
                <div class="form__error">
                    @error('password_confirmation')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="register-form__button">
                <button class="form__button-submit btn" type="submit">会員登録</button>
            </div>
            <div class="login__link">
                <p class="login-text">アカウントをお持ちの方はこちらから</p>
                <a class="login-form__btn" href="/login">ログイン</a>
            </div>
        </form>
    </div>
</div>
@endsection