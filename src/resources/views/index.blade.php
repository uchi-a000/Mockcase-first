@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="home__alert heading">
        {{ Auth::user()->name }} さんお疲れ様です！
    </div>

    <div class="home__panel">
        <div class="home-panel__inner">
            <form class="home__button" action="/start_work" method="post">
                @csrf
                @if($status == 0)
                <button class="home__button-submit" type="submit" name="start_work">勤務開始</button>
                @else
                <button class="home__button-submit" type="submit" name="start_work" disabled>勤務開始</button>
                @endif
            </form>
            <form class="home__button" action="/end_work" method="post">
                @method('PATCH')
                @csrf
                @if($status == 1) <!-- 出勤中の場合のみ勤務終了ボタンを表示 -->
                <button class="home__button-submit" type="submit" name="end_work">勤務終了</button>
                @else
                <button class="home__button-submit" type="submit" name="end_work" disabled>勤務終了</button>
                @endif
            </form>
            <form class="home__button" action="/start_rest" method="post">
                @method('PATCH')
                @csrf
                @if($status == 1) <!-- 出勤中の場合のみ休憩開始ボタンを表示 -->
                <button class="home__button-submit" type="submit" name="start_rest">休憩開始</button>
                @else
                <button class="home__button-submit" type="submit" name="start_rest" disabled>休憩開始</button>
                @endif
            </form>
            <form class="home__button" action="/end_rest" method="post">
                @method('PATCH')
                @csrf
                @if($status == 2) <!-- 休憩中の場合のみ休憩終了ボタンを表示 -->
                <button class="home__button-submit" type="submit" name="end_rest">休憩終了</button>
                @else
                <button class="home__button-submit" type="submit" name="end_rest" disabled>休憩終了</button>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection