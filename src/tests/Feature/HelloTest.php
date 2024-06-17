<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\Stamp;
use App\Models\User;
use Carbon\Carbon;

class HelloTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleWithSpecificDateTime()
    {
        $user = User::factory()->create();

        $createdAt = Carbon::create(2024, 5, 10, 8, 0, 0);
        $stamp = Stamp::create([
            'user_id' => $user->id,
            'created_at' => $createdAt,
            'start_work' => null,
        ]);

        $fixedNow = Carbon::create(2024, 5, 11, 0, 0, 0);
        Carbon::setTestNow($fixedNow);

        Artisan::call('date:command');

        // 勤務開始時間が更新されたかどうかを確認
        if ($fixedNow->diffInDays($createdAt) > 0) {
            $expectedStartWork = $createdAt->copy()->addDays();
            $this->assertEquals($expectedStartWork, $stamp->start_work, '勤務開始時間が更新されるはずです');
        } else {
            $this->fail('期待されるアクションが行われませんでした');
        }

        Carbon::setTestNow();
    }
}
