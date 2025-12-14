<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserUsageResource\Pages;
use App\Models\UserUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserUsageResource extends Resource
{
    protected static ?string $model = UserUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Usage Analytics';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('type')
                    ->options([
                        'message_sent' => 'Message Sent',
                        'ai_response' => 'AI Response',
                        'file_upload' => 'File Upload',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('tokens')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('messages')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('bytes')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->default(0),

                Forms\Components\KeyValue::make('metadata')
                    ->keyLabel('Key')
                    ->valueLabel('Value'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'message_sent',
                        'success' => 'ai_response',
                        'warning' => 'file_upload',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('tokens')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Tokens'),
                    ]),

                Tables\Columns\TextColumn::make('messages')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Messages'),
                    ]),

                Tables\Columns\TextColumn::make('bytes')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 2).' KB' : '0 KB')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total KB')
                            ->formatStateUsing(fn ($state) => number_format($state / 1024, 2).' KB'),
                    ]),

                Tables\Columns\TextColumn::make('cost')
                    ->money('USD')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Cost')
                            ->money('USD'),
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'message_sent' => 'Message Sent',
                        'ai_response' => 'AI Response',
                        'file_upload' => 'File Upload',
                    ]),

                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListUserUsages::route('/'),
            'create' => Pages\CreateUserUsage::route('/create'),
            'edit' => Pages\EditUserUsage::route('/{record}/edit'),
        ];
    }
}
