<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use Modules\Review\Enums\ReviewStatus;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ReviewResource;
use App\Filament\Concerns\SendsFilamentNotifications;
use Modules\Review\Contracts\ReviewServiceInterface;

class ViewReview extends ViewRecord
{
  use SendsFilamentNotifications;
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
            ->options(ReviewStatus::options())
            ->default(fn($record) => $record->status instanceof ReviewStatus ? $record->status->value : $record->status)
            ->required()
            ->native(false),
        ])
        ->action(function (array $data, $record) {
          app(ReviewServiceInterface::class)->updateReviewStatus($record->id, $data['status']);

          return self::buildSuccessNotification(
            __('app.messages.review.status_updated'),
            __('app.messages.review.status_updated_body', ['status' => $data['status']])
          );
        })
        ->after(function () {
          $this->fillForm();
        })

    ];
  }

  protected function mutateFormDataBeforeFill(array $data): array
  {
    $reviewService = app(ReviewServiceInterface::class);
    $review = $reviewService->getReviewById($this->record->id);

    $data['user_name'] = $review->user ? $review->user->first_name . ' ' . $review->user->last_name : '';
    $data['product_name'] = $reviewService->getTranslatedOrderableName($review);
    $data['order_number'] = $review->order ? $review->order->order_number : '';
    $data['status_display'] = $review->status_label;

    return $data;
  }
}
