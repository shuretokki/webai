<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('subscription_tier')
                    ->options([
                        'free' => 'Free',
                        'pro' => 'Pro',
                        'enterprise' => 'Enterprise',
                    ])
                    ->required()
                    ->default('free'),

                Forms\Components\Toggle::make('is_admin')
                    ->label('Administrator')
                    ->helperText('Grant full admin panel access'),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('subscription_tier')
                    ->colors([
                        'secondary' => 'free',
                        'success' => 'pro',
                        'primary' => 'enterprise',
                    ])
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('usage_stats.total_messages')
                    ->label('Messages')
                    ->getStateUsing(fn ($record) => $record->usages()->where('type', 'message_sent')->sum('messages'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('usage_stats.total_cost')
                    ->label('Total Cost')
                    ->getStateUsing(fn ($record) => '$' . number_format($record->usages()->sum('cost'), 2))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_tier')
                    ->options([
                        'free' => 'Free',
                        'pro' => 'Pro',
                        'enterprise' => 'Enterprise',
                    ]),

                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Administrators')
                    ->placeholder('All users')
                    ->trueLabel('Only admins')
                    ->falseLabel('Only non-admins'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
