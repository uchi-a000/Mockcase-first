<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Stamp;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // 最新の出勤レコードを取得
        $latestStamp = Stamp::where('user_id', $user->id)->latest()->first();

        $status = 0; // 初期値（出勤前）

        if ($latestStamp) {
            if ($latestStamp->end_work) {
                $status = 0; // 退勤後（出勤前）
            } else {
                // 休憩中かどうかを判定 最新の休憩レコードを取得
                $latestRest = $latestStamp->rests()->latest()->first();

                if ($latestRest && !$latestRest->end_rest) {
                    $status = 2; // 休憩中
                } else {
                    $status = 1; // 出勤中
                }
            }
        }
        return view('index', compact('status'));
    }
}
