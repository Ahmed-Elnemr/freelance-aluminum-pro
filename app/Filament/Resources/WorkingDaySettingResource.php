<?php

namespace App\Filament\Resources;

use App\Enum\DayOfWeekEnum;
use App\Filament\Resources\WorkingDaySettingResource\Pages;
use App\Models\WorkingDaySetting;
use App\Models\WorkingHourBlockedSlot;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkingDaySettingResource extends Resource
{
    protected static ?string $model = WorkingDaySetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getModelLabel(): string
    {
        return __('dashboard.working_day_settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.working_day_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.working_day_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.working_day_settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('day')
                            ->label(__('dashboard.day'))
                            ->formatStateUsing(fn ($state) => $state instanceof DayOfWeekEnum ? $state->label() : $state)
                            ->disabled()
                            ->dehydrated(false),

                        Toggle::make('is_active')
                            ->label(__('dashboard.active'))
                            ->default(true),

                        TimePicker::make('start_time')
                            ->label(__('dashboard.start_time'))
                            ->required()
                            ->native(false)
                            ->seconds(false),

                        TimePicker::make('end_time')
                            ->label(__('dashboard.end_time'))
                            ->required()
                            ->native(false)
                            ->seconds(false),
                    ])->columns(2),

                Section::make(__('dashboard.blocked_slots'))
                    ->schema([
                        Forms\Components\CheckboxList::make('blocked_slots')
                            ->label('')
                            ->options(function (WorkingDaySetting $record) {
                                $slots = $record->generateSlots();
                                $options = [];
                                foreach ($slots as $slot) {
                                    $options[$slot['time']] = $slot['time'].' '.$slot['period'];
                                }

                                return $options;
                            })
                            ->formatStateUsing(function (WorkingDaySetting $record) {
                                return WorkingHourBlockedSlot::where('day', $record->day)
                                    ->pluck('slot_time')
                                    ->map(fn ($time) => substr($time, 0, 5))
                                    ->toArray();
                            })
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, WorkingDaySetting $record) {
                                WorkingHourBlockedSlot::where('day', $record->day)->delete();
                                foreach ($state as $time) {
                                    WorkingHourBlockedSlot::create([
                                        'day' => $record->day,
                                        'slot_time' => $time,
                                    ]);
                                }
                            })
                            ->columns(4)
                            ->searchable()
                            ->bulkToggleable(),
                    ])
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day')
                    ->label(__('dashboard.day'))
                    ->formatStateUsing(fn (DayOfWeekEnum $state): string => $state->label()),

                TextColumn::make('start_time')
                    ->label(__('dashboard.start_time'))
                    ->time('H:i'),

                TextColumn::make('end_time')
                    ->label(__('dashboard.end_time'))
                    ->time('H:i'),

                IconColumn::make('is_active')
                    ->label(__('dashboard.active'))
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWorkingDaySettings::route('/'),
        ];
    }
}
