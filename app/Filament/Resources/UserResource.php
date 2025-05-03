<?php

namespace App\Filament\Resources;

use App\Enum\UserTypeEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    public static function getNavigationLabel(): string
    {
        return __('Clients');
    }

    public static function getModelLabel(): string
    {
        return __('Client');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Clients');
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', UserTypeEnum::CLIENT->value)->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('mobile')
                    ->label(__('Mobile Number'))
                    ->tel()
                    ->maxLength(20),

                Forms\Components\Hidden::make('type')
                    ->default(UserTypeEnum::CLIENT->value),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))->searchable(),
                Tables\Columns\TextColumn::make('mobile')

                    ->label(__('Mobile'))->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Is active'))
                    ->boolean(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('حالة التحقق')
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        0 => 'في انتظار التفعيل',
                        1 => 'تم التحقق',
                        default => 'غير معروف',
                    })
                    ->colors([
                        'warning' => fn ($state) => (int) $state === 0,
                        'success' => fn ($state) => (int) $state === 1,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F - h:i A')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('حالة التفعيل')
                    ->options([
                        '1' => 'مفعل',
                        '0' => 'غير مفعل',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة التحقق')
                    ->options([
                        '1' => 'تم التحقق',
                        '0' => 'في انتظار التفعيل',
                    ]),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }


}
