<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';



    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('app.columns.payment.method'))
                    ->badge(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label(__('app.columns.payment.transaction_id'))
                    ->copyable()
                    ->wrap()
                    ->limit(32),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('app.columns.payment.amount'))
                    ->money('USD')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('app.columns.payment.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'paid' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-x-circle',
                        'refunded' => 'heroicon-o-arrow-path',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label(__('app.columns.payment.paid_at'))
                    ->dateTime('M j, Y g:i A')
                    ->toggleable(),
            ])
            ->paginated(false)
            ->striped()
            ->headerActions([])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('app.empty_states.no_payments'))
            ->emptyStateDescription(__('app.empty_states.no_payments_description'));
    }
}
