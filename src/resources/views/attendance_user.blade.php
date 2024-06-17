@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="content">
    <form class="search-form" action="{{ route('search') }}" method="get">
        @csrf
        <div class="attendance-form">
            <div class="attendance-form__text">
                <div class="attendance-form__text__inner">
                    <input class="attendance-form__keyword-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="名前またはメールアドレスで検索">
                </div>
            </div>
            <div class="search-form__button">
                <button class="search-form__button-submit" type="submit">検索</button>
                <button class="search-form__button-submit__reset" type="submit" name="reset">リセット</button>
            </div>
        </div>
    </form>

    <!-- $users が存在し、その中に1つ以上のアイテムがある場合 -->
    @if(isset($users) && $users->count() > 0)
    <table class="attendance__table">
        <thead>
            <tr class="attendance__row">
                <th class="attendance__label">名前</th>
                <th class="attendance__label">メールアドレス</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="attendance__row">
                <td class="attendance__data"><a href="{{ route('attendance_user_details', $user->id)  }}">{{ $user->name }}</a></td>
                <td class="attendance__data">{{ $user->email }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- ２ページ目以降もnameとemailを保持 -->
    {{ $users->appends(['keyword' => request('keyword')])->links('vendor.pagination.custom') }}
    @else
    <p>該当するデータがありません。</p>
    @endif
</div>
@endsection