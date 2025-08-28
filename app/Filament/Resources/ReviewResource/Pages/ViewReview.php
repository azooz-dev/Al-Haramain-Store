<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Models\Review\Review;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ReviewResource;

class ViewReview extends ViewRecord
{
  protected static string $resource = ReviewResource::class;

  protected function getHeaderActions(): array
  {
    return [
      \Filament\Actions\Action::make('change_status')
        ->label(__('app.actions.change_status'))
        ->icon('heroicon-o-pencil')
        ->color('warning')
        ->form([
          \Filament\Forms\Components\Select::make('status')
            ->label(__('app.fields.new_status'))
            ->options([
              Review::PENDING => __('app.status.pending'),
              Review::APPROVED => __('app.status.approved'),
              Review::REJECTED => __('app.status.rejected'),
            ])
            ->default(fn($record) => $record->status)
            ->required()
            ->native(false),
        ])
        ->action(function (array $data, $record) {
          $record->update(['status' => $data['status']]);

          \Filament\Notifications\Notification::make()
            ->title(__('app.messages.status_updated'))
            ->success()
            ->send();
        })
        ->after(function () {
          $this->fillForm();
        })

    ];
  }

  protected function mutateFormDataBeforeFill(array $data): array
  {
    $review = $this->record->load(['user', 'product', 'order']);

    $data['user_name'] = $review->user ? $review->user->first_name . ' ' . $review->user->last_name : '';
    $data['product_name'] = $review->product ? $review->product->name : '';
    $data['order_number'] = $review->order ? $review->order->order_number : '';
    $data['status_display'] = $review->status_label;

    return $data;
  }
}
