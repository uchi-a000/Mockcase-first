<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stamp;

class AttendanceUserController extends Controller
{

    public function attendance_user(Request $request)
    {
        $users = User::paginate(5);

        return view('attendance_user', compact('users'));
    }

    public function search(Request $request)
    {
        if ($request->has('reset')) {
            return redirect()->route('attendance_user')->withInput();
        }

        $query = User::query();
        $query = $this->getSearchQuery($request, $query);

        $users = $query->paginate(5);

        return view('attendance_user', compact('users'));
    }


    private function getSearchQuery($request, $query)
    {
        if (!empty($request->keyword)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('email', 'like', '%' . $request->keyword . '%');
            });
        }

        return $query;
    }


    //ユーザー毎の勤怠表の表示
    public function show(User $user, Request $request)
    {
        // ユーザーの勤怠情報を取得し、月の検索条件があればそれに基づいてフィルタリングする
        $query = Stamp::where('user_id', $user->id);

        // 月の検索条件を取得
        $month = $request->input('month', '');
        if (!empty($month)) {
            // 月の開始日と終了日を取得
                $startDate = new \DateTime("$month-01");
                $endDate = (clone $startDate)->modify('last day of this month')->setTime(23, 59, 59);

                $query->whereBetween('start_work', [$startDate, $endDate]);
        }

        //日付が古い順に並び替える昇順（ascending order）
        $query->orderBy('start_work', 'asc');

        $stamps = $query->paginate(5);

        // 対象がない場合のメッセージ
        $message = $stamps->isEmpty();

        return view('attendance_user_details', compact('user', 'stamps', 'month'));
    }
}




