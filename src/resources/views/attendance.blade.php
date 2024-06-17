@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="content">
    <form action="/attendance" method="post">
        @csrf
        <div class="attendance-form__date heading">
            <a class="attendance-form__date-link" href="{{ route('attendance', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}">＜</a>
            <span class="attendance-date">{{ $date }}</span>
            <a class="attendance-form__date-link" href="{{ route('attendance', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}">＞</a>
        </div>
        <div class="attendance-form__date">
            <label for="date">年月日検索:</label>
            <input class="date" type="date" id="date" name="date" value="{ request('date') }}">
            <button class="search-form__button-submit" type="submit">検索</button>
        </div>
        @if ($stamps->isEmpty())
        <p class="attendance__alert__date">対象がありません</p>
        @else
        <table class=" attendance__table">
            <tr class="attendance__row">
                <th class="attendance__label">名前</th>
                <th class="attendance__label">勤務開始</th>
                <th class="attendance__label">勤務終了</th>
                <th class="attendance__label">休憩時間</th>
                <th class="attendance__label">勤務時間</th>
            </tr>
            @foreach($stamps as $stamp)
            <tr class="attendance__row">
                <td class="attendance__data">{{ $stamp['user']->name }}</td>
                <td class="attendance__data">{{ (Carbon\Carbon::parse($stamp['start_work'])->format('H:i:s')) }}</td>
                <td class="attendance__data">
                    @if ($stamp['end_work'])
                    {{ (Carbon\Carbon::parse($stamp['end_work'])->format('H:i:s')) }}
                    @endif
                </td>
                <td class="attendance__data">
                    @if ($stamp['end_work'])
                    {{ $stamp['total_rest'] }}
                    @endif
                </td>
                <td class="attendance__data">
                    @if ($stamp['end_work'])
                    {{ $stamp['total_work'] }}
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </form>
    <!-- appendsページを移動してもdate情報を保持 -->
    {{ $stamps->appends(['date' => $date])->links('vendor.pagination.custom') }}
    @endif
</div>
@endsection