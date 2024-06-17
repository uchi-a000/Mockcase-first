<?php

namespace App\Console\Commands;

use App\Models\Stamp;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateNightShiftWorkAndRest extends Command
{
    protected $signature = 'update:night-shift-work-rest';
    protected $description = 'Update night shift work and rest times for employees';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::transaction(function () {
            $stamps = Stamp::whereNotNull('start_work')
            ->whereNotNull('end_work')
            ->get();

            foreach ($stamps as $stamp) {
                $this->updateStamp($stamp);
            }
        });

        $this->info('Night shift work records updated successfully.');
    }

    private function updateStamp($stamp)
    {
        $startWork = new Carbon($stamp->start_work);
        $endWork = new Carbon($stamp->end_work);

        if ($endWork && $startWork->day != $endWork->day) {
            $midnight = $startWork->copy()->endOfDay();
            $nextDay = $endWork->copy()->startOfDay();

            if ($startWork->lt($midnight) && $endWork->gte($nextDay)) {
                $this->processShiftCrossingTwoDays($stamp, $midnight, $nextDay);
            }
        }
    }


    private function processShiftCrossingTwoDays($stamp, $midnight, $nextDay)
    {

        $startWork = new Carbon($stamp->start_work);
        $endWork = new Carbon($stamp->end_work);

        $totalRestToday = 0;
        $totalRestNextDay = 0;
        $rests = Rest::where('stamp_id', $stamp->id)->get();

        foreach ($rests as $rest) {
            $startRest = new Carbon($rest->start_rest);
            $endRest = new Carbon($rest->end_rest);

                if ($startRest->day != $endRest->day) {
                    // 休憩時間が日を跨ぐ場合
                    if ($startRest->lt($midnight) && $endRest->gt($nextDay)) {
                        $totalRestToday += $startRest->diffInSeconds($midnight) + 1;
                        $totalRestNextDay += $nextDay->diffInSeconds($endRest);
                    }
                } else {
                    // 休憩時間が日を跨がない場合
                    if ($startRest->lt($midnight) && $endRest->lte($midnight)) {
                        $totalRestToday += $startRest->diffInSeconds($endRest);
                    } elseif ($startRest->gte($nextDay) && $endRest->gt($nextDay)) {
                        $totalRestNextDay += $startRest->diffInSeconds($endRest);
                    }
                }
            }

        // 当日の勤務情報
        $totalWorkTodaySeconds = $startWork->diffInSeconds($midnight) - $totalRestToday;
        $totalWorkToday = gmdate('H:i:s', $totalWorkTodaySeconds);
        $stamp->start_work = $startWork;
        $stamp->end_work = $midnight;
        $stamp->total_rest = gmdate('H:i:s', $totalRestToday);
        $stamp->total_work = $totalWorkToday;
        $stamp->save();

        // 翌日の出勤操作
        $totalWorkNextDaySeconds = $nextDay->diffInSeconds($endWork) - $totalRestNextDay;
        $totalWorkNextDay = gmdate('H:i:s', $totalWorkNextDaySeconds);
        $nextDayStamp = new Stamp();
        $nextDayStamp->user_id = $stamp->user_id;
        $nextDayStamp->start_work = $nextDay;
        $nextDayStamp->end_work = $endWork;
        $nextDayStamp->total_work = $totalWorkNextDay;
        $nextDayStamp->total_rest = gmdate('H:i:s', $totalRestNextDay);
        $nextDayStamp->save();
    }
}
