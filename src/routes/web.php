<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\StampController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AttendanceUserController;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// ログイン関連ルート
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth');

// ユーザー登録関連ルート
Route::get('/register', [RegisteredUserController::class, 'create']);
Route::post('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');




Route::middleware('auth')->group(function () {

    // メール認証
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        // 認証メールを送信するユーザーのメールアドレスをセッションから取得
        $sessionEmail = Session::get('verification_email');
        $currentEmail = $request->user()->email;

        // メールアドレスが一致しない場合にエラーを返す
        if ($sessionEmail && $currentEmail !== $sessionEmail) {
            return redirect()->route('verification.notice')->withErrors(['email' => '登録時のメールアドレスと一致しません。']);
        }

        $request->fulfill();
        return redirect('/');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        // 認証メールを送信するユーザーのメールアドレスをセッションに保存
        Session::put('verification_email', $request->user()->email);
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '確認リンクが送信されました');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    // 勤怠ページ
    Route::get('/', [HomeController::class, 'index']);

    // 出退勤・休憩打刻
    Route::post('/start_work', [StampController::class, 'start_work'])->name('start_work');
    Route::patch('/end_work', [StampController::class, 'end_work'])->name('end_work');
    Route::patch('/start_rest', [StampController::class, 'start_rest'])->name('start_rest');
    Route::patch('/end_rest', [StampController::class, 'end_rest'])->name('end_rest');

    // 日付一覧
    Route::get('/attendance', [AttendanceController::class, 'attendance'])->name('attendance');
    Route::post('/attendance', [AttendanceController::class, 'attendance'])->name('attendance');

    //ユーザー一覧
    Route::get('/attendance_user', [AttendanceUserController::class, 'attendance_user'])->name('attendance_user');;
    Route::get('/search', [AttendanceUserController::class, 'search'])->name('search');

    //ユーザー毎の勤怠表の表示
    Route::get('/users/{user}', [AttendanceUserController::class, 'show'])->name('attendance_user_details');
});
