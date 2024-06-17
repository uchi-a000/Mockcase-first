<?php

namespace App\Http\Controllers;

use App\Models\Rest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Stamp;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Auth;
use PharIo\Manifest\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StampController extends Controller
{

    public function start_work()
    {
        // 現在認証しているuser取得
        $user = Auth::user();

        // 現在認証されている最新のidをstampモデルから取得
        $latestStamp = Stamp::where('user_id', $user->id)->latest()->first();

        if (!$latestStamp || $latestStamp->end_work) {
            // 出勤がないかすでに退勤している場合（！＝trueでない場合true）出勤開始を行う
            Stamp::create([
                'user_id' => $user->id,
                'start_work' => Carbon::now(),
            ]);
        }
        return redirect()->back();
    }



    public function end_work()
    {
        $user = Auth::user();
        // 現在認証されている最新のレコードを取得
        $latestStamp = Stamp::where('user_id', $user->id)->latest()->first();

        $totalRestSeconds = 0;

        $rests = Rest::where('stamp_id', $latestStamp->id)->get();

        foreach ($rests as $rest) {
            //$restのrest_timeプロパティを取得しそれをCarbonインスタンスに変換
            $restTimeSeconds = new Carbon($rest->rest_time);
            // +=で$totalRestSecondsに加算して代入/$restTimeSecondsと時刻00:00:00との差を秒数で計算
            $totalRestSeconds += $restTimeSeconds->diffInSeconds(Carbon::createFromTime(0, 0, 0));
        }

        $now = Carbon::now();
        $startWork = new Carbon($latestStamp->start_work);
        // 勤務時間の計算（勤務開始時刻から勤務終了時刻までの秒数 - 休憩時間）
        $totalWorkSeconds  = $now->diffInSeconds($startWork) - $totalRestSeconds;

        //'%02d:%02d:%02d'=各数値を2桁になるように0埋め
        $totalWork = sprintf('%02d:%02d:%02d', floor($totalWorkSeconds / 3600), floor(($totalWorkSeconds % 3600) / 60), $totalWorkSeconds % 60);
        $totalRest = sprintf('%02d:%02d:%02d', floor($totalRestSeconds / 3600), floor(($totalRestSeconds % 3600) / 60), $totalRestSeconds % 60);

        if ($latestStamp) {
            // 出勤レコードが存在する場合に退勤処理を行う
            $latestStamp->end_work = $now;
            $latestStamp->total_work = $totalWork;
            $latestStamp->total_rest = $totalRest;
            $latestStamp->save();
        }

        return redirect()->back();
    }


    public function start_rest()
    {
        $user = Auth::user();
        $latestStamp = Stamp::where('user_id', $user->id)->latest()->first();

        if ($latestStamp && !$latestStamp->end_work) {
            // 出勤中かつ退勤していない場合に休憩を開始する
            Rest::create([
                'stamp_id' => $latestStamp->id,
                'start_rest' => Carbon::now(),
            ]);
        }

        return redirect()->back();
    }


    public function end_rest()
    {
        $user = Auth::user();

        // ログインしているユーザーの最新の休憩レコードを取得
        $latestRest = Rest::whereHas('stamp', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->first();

        // 休憩時間が存在したら
        if ($latestRest) {
            $startRest = new Carbon($latestRest->created_at);
            $now = Carbon::now();
            $RestTimeSeconds = $now->diffInSeconds($startRest);

            $RestTime = sprintf('%02d:%02d:%02d', floor($RestTimeSeconds / 3600), floor(($RestTimeSeconds % 3600) / 60), $RestTimeSeconds % 60);

            $latestRest->end_rest = $now;
            $latestRest->rest_time = $RestTime;
            $latestRest->save();
        }

        return redirect()->back();
    }

}
