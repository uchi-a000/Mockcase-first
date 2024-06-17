<?php

namespace Database\Factories;

use App\Models\Stamp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StampFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stamp::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 2024年の範囲でランダムな日時を生成
        $dummyDate = $this->faker->dateTimeBetween('2024-01-01', '2024-06-30');

        $startWork = $dummyDate->format('Y-m-d 09:i:s'); // 始業時間を9時に設定
        $endWork = $dummyDate->modify('+8 hours')->format('Y-m-d 20:i:s'); // 終業時間を20時に設定（勤務時間は8時間）

        // 休憩時間（ダミーのランダムな時間）
        $totalRestSeconds =3600; // 30分から60分の間でランダムに休憩時間を生成
        $totalRest = gmdate('H:i:s', $totalRestSeconds); // 秒数を時刻形式（HH:mm:ss）に変換

        // 勤務時間（勤務時間 - 休憩時間）
        $totalWorkSeconds = strtotime($endWork) - strtotime($startWork) - $totalRestSeconds;
        $totalWork = gmdate('H:i:s', $totalWorkSeconds); // 秒数を時刻形式（HH:mm:ss）に変換

        return [
            'start_work' => $startWork,
            'end_work' => $endWork,
            'total_rest' => $totalRest,
            'total_work' => $totalWork,
            'user_id' => User::all()->random()->id
        ];
    }
}
