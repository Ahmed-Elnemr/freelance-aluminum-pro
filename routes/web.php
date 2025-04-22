<?php

use App\Filament\Pages\ChatPage;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/admin/chat', ChatPage::class)->name('admin.chat');
