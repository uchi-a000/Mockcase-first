@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="content">
    <form class="attendance-form" action="{{ route('attendance_user_details', ['user' => $user->id]) }}" method="get">
        @csrf
        <div class="attendance-form">
            <div class="attendance-form__month">
                <label for="month">月検索:</label>
                <input class="month" type="month" id="month" name="month" value="{{ request('month') }}">
                <button class="search-form__button-submit" type="submit">検索</button>
                <div class="attendance__alert">{{ $user->name }} さんの勤怠表</div>
            </div>
        </div>
    </form>
    @if ($stamps->isEmpty())
    <p class="attendance__alert__month">対象がありません</p>
    @else
    <table class="attendance__table">
        <tr class="attendance__row">
            <th class="attendance__label">日付</th>
            <th class="attendance__label">勤務開始</th>
            <th class="attendance__label">勤務終了</th>
            <th class="attendance__label">休憩時間</th>
            <th class="attendance__label">勤務時間</th>
        </tr>
        @foreach($stamps as $stamp)
        <tr class="attendance__row">
            <td class="attendance__data">{{ (Carbon\Carbon::parse($stamp['start_work'])->format('Y-m-d')) }}</td>
            <td class="attendance__data">{{ (Carbon\Carbon::parse($stamp['start_work'])->format('H:i:s')) }}</td>
            <td class="attendance__data">{{ (Carbon\Carbon::parse($stamp['end_work'])->format('H:i:s')) }}</td>
            <td class="attendance__data">{{ $stamp->total_rest }}</td>
            <td class="attendance__data">{{ $stamp->total_work }}</td>
        </tr>
        @endforeach
    </table>
    {{ $stamps->appends(['month' => request('month')])->links('vendor.pagination.custom') }}
    @endif
</div>
@endsection