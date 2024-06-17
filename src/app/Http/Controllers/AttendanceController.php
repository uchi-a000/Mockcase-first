<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stamp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

    public function attendance(Request $request)
    {
        // リクエストから日付を取得し、Carbonインスタンスに変換
        $date = $request->input('date', Carbon::now()->toDateString());

        // 日付に一致するStampレコードを取得
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        $stamps = Stamp::whereBetween('start_work', [$startOfDay, $endOfDay])
            ->orderBy('start_work')
            ->paginate(5);

        if (!empty($request->date)) {
            $stamps->where('date', '=', $request->date);
        }

        // 対象がない場合のメッセージ
        $message = $stamps->isEmpty();

        return view('attendance', compact('date', 'stamps'));
    }

}
