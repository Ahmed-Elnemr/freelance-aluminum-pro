<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = \Illuminate\Notifications\DatabaseNotification::class;
//    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Checkbox::make('send_to_all')
                    ->label('إرسال إلى جميع المستخدمين')
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $set('users', null); // إلغاء تحديد المستخدمين عند اختيار "إرسال للكل"
                        }
                    }),

                Forms\Components\Select::make('users')
                    ->label('المستلمون')
                    ->options(User::all()->pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->hidden(fn (callable $get): bool => $get('send_to_all'))
                    ->disabled(fn (callable $get): bool => $get('send_to_all'))
                    ->required(fn (callable $get): bool => !$get('send_to_all'))
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if (!empty($state)) {
                            $set('send_to_all', false); // إلغاء تحديد "إرسال للكل" عند اختيار مستخدمين
                        }
                    }),

                Forms\Components\Tabs::make('Labels')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('title_en')
                                    ->label('Title (English)')
                                    ->required(),
                                Forms\Components\Textarea::make('body_en')
                                    ->label('Body (English)')
                                    ->required(),
                            ]),
                        Forms\Components\Tabs\Tab::make('العربية')
                            ->schema([
                                Forms\Components\TextInput::make('title_ar')
                                    ->label('العنوان (العربية)')
                                    ->required(),
                                Forms\Components\Textarea::make('body_ar')
                                    ->label('المحتوى (العربية)')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('notifiable.name')
                    ->label('المستخدم'),
                Tables\Columns\TextColumn::make('data.title')
                    ->label('العنوان'),

                Tables\Columns\TextColumn::make('data.body')
                    ->label('المحتوي'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
        ];
    }
}
