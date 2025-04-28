<?php
use Filament\Notifications\Notification;
use App\Filament\Pages\ChatPage;
use App\Models\User;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/admin/chat', ChatPage::class)->name('admin.chat');



Route::get('/t', function () {
    $user = User::find(1);

    Notification::make()
        ->title('A database notification has been created')
        ->sendToDatabase($user);

    return 'ok';
});
