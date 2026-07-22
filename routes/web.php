<?php

use App\Filament\Pages\ChatPage;
use App\Http\Controllers\AdminDeviceTokenController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\FirebaseMessagingServiceWorkerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/admin/chat', ChatPage::class)->name('admin.chat');

Route::get('/firebase-messaging-sw.js', FirebaseMessagingServiceWorkerController::class)
    ->name('firebase-messaging-sw');

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/admin/device-token', [AdminDeviceTokenController::class, 'store'])
        ->name('admin.device-token');
});

Route::get('payment-page/{user_id}', [PaymentController::class, 'paymentPage'])->name('payment-page');
Route::any('payment-callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/chat-page/{userId?}', ChatPage::class)->name('filament.pages.chat-page');

// Route::get('/test-notification', function () {
//     $admin = User::where('type', 'admin')->first();

//     if (!$admin) {
//         return "No admin found!";
//     }

//     Notification::make()
//         ->title('تنبيه تجريبي')
//         ->body('هذا التنبيه للتأكد من أن الروابط تعمل بشكل صحيح. اضغط على زر المشاهدة أدناه.')
//         ->success()
//         ->actions([
//             \Filament\Notifications\Actions\Action::make('view')
//                 ->label('مشاهدة المحادثة')
//                 ->url(route('filament.pages.chat-page', ['userId' => 2])),
//         ])
//         ->sendToDatabase($admin);

//     return "Done! Check your dashboard notifications.";
// });
