<?php

use App\Http\Controllers\api\PaymentController;
use Filament\Notifications\Notification;
use App\Filament\Pages\ChatPage;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/admin/chat', ChatPage::class)->name('admin.chat');

Route::get('payment-page/{id}', [PaymentController::class, 'paymentPage'])->name('payment-page');
Route::any('payment-callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');

