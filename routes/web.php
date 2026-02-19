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

