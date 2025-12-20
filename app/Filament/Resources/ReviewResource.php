<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Concerns\SendsFilamentNotifications;
use Modules\Review\Contracts\ReviewServiceInterface;

class ReviewResource extends Resource
{
  use SendsFilamentNotifications;
  protected static ?string $model = Review::class;

  protected static ?string $slug = 'reviews';

  protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

  protected static ?string $navigationGroup = 'Store Management';

  protected static ?string $navigationLabel = 'Reviews';

  protected static ?int $navigationSort = 4;

  protected static ?string $modelLabel = 'Review';

  protected static ?string $pluralModelLabel = 'Reviews';

  protected static ?string $recordTitleAttribute = 'id';

  /**
   * Get the translated navigation group
   */
  public static function getNavigationGroup(): ?string
  {
    return __('app.navigation.store_management');
  }

  /**
   * Get the translated navigation label
   */
  public static function getNavigationLabel(): string
  {
    return __('app.resources.review.navigation_label');
  }

  /**
   * Get the translated model label
   */
  public static function getModelLabel(): string
  {
    return __('app.resources.review.label');
  }

  /**
   * Get the translated plural model label
   */
  public static function getPluralModelLabel(): string
  {
    return __('app.resources.review.plural_label');
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make(__('app.forms.review.status_management'))
          ->description(__('app.forms.review.status_management_description'))
          ->icon('heroicon-o-cog-6-tooth')
          ->schema([
            Forms\Components\TextInput::make('status_display')
              ->label(__('app.forms.review.status'))
              ->disabled()
              ->required()
              ->dehydrated(false)
              ->prefixIcon('heroicon-o-check-circle'),

            Forms\Components\Section::make(__('app.forms.review.review_details'))
              ->description(__('app.forms.review.review_details_description'))
              ->icon('heroicon-o-chat-bubble-left-right')
              ->schema([
                Forms\Components\Grid::make(2)
                  ->schema([
                    Forms\Components\TextInput::make('rating')
                      ->label(__('app.forms.review.rating'))
                      ->prefixIcon('heroicon-o-star')
                      ->disabled(),

                    Forms\Components\TextInput::make('user_name')
                      ->label(__('app.forms.review.user'))
                      ->disabled()
                      ->prefixIcon('heroicon-o-user')
                      ->dehydrated(false)
                      ->default(''),

                    Forms\Components\TextInput::make('product_name')
                      ->label(__('app.forms.review.product'))
                      ->disabled()
                      ->prefixIcon('heroicon-o-cube')
                      ->dehydrated(false)
                      ->default(''),

                    Forms\Components\TextInput::make('order_number')
                      ->label(__('app.forms.review.order'))
                      ->disabled()
                      ->prefixIcon('heroicon-o-shopping-bag')
                      ->prefix('#')
                      ->dehydrated(false)
                      ->default(''),
                  ]),
              ])
              ->collapsible(),
          ])
          ->columns(2)
          ->collapsible(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label(__('app.columns.review.id'))
          ->searchable()
          ->sortable()
          ->weight('bold')
          ->color('primary')
          ->alignCenter()
          ->icon('heroicon-o-hashtag'),

        Tables\Columns\TextColumn::make('user.first_name')
          ->label(__('app.columns.review.user'))
          ->searchable()
          ->sortable()
          ->icon('heroicon-o-user')
          ->alignCenter()
          ->color('gray')
          ->formatStateUsing(function ($record) {
            if ($record->user) {
              return $record->user->first_name . ' ' . $record->user->last_name;
            }
            return 'N/A';
          }),

        Tables\Columns\TextColumn::make('product.sku')
          ->label(__('app.columns.review.product'))
          ->getStateUsing(function (Review $record) {
            return app(ReviewServiceInterface::class)->getOrderableIdentifier($record);
          })
          ->searchable()
          ->sortable()
          ->icon('heroicon-o-cube')
          ->color('blue')
          ->alignCenter()
          ->limit(30),

        Tables\Columns\TextColumn::make('rating')
          ->label(__('app.columns.review.rating'))
          ->sortable()
          ->icon('heroicon-o-star')
          ->color('warning')
          ->formatStateUsing(fn(int $state): string => str_repeat('⭐', $state))
          ->alignCenter(),

        Tables\Columns\TextColumn::make('comment')
          ->label(__('app.columns.review.comment'))
          ->searchable()
          ->limit(50)
          ->icon('heroicon-o-chat-bubble-left')
          ->alignCenter()
          ->color('gray'),

        Tables\Columns\TextColumn::make('order.order_number')
          ->label(__('app.columns.review.order'))
          ->searchable()
          ->sortable()
          ->icon('heroicon-o-shopping-bag')
          ->color('success')
          ->alignCenter()
          ->prefix('#'),

        Tables\Columns\BadgeColumn::make('status')
          ->label(__('app.columns.review.status'))
          ->sortable()
          ->alignCenter()
          ->formatStateUsing(fn($state): string => $state instanceof ReviewStatus ? $state->label() : ucfirst($state))
          ->icon(fn($state): string => $state instanceof ReviewStatus ? $state->icon() : 'heroicon-o-question-mark-circle')
          ->color(fn($state): string => $state instanceof ReviewStatus ? $state->color() : 'gray'),

        Tables\Columns\TextColumn::make('created_at')
          ->label(__('app.columns.review.created_at'))
          ->dateTime()
          ->sortable()
          ->icon('heroicon-o-calendar')
          ->alignCenter()
          ->color('gray'),
      ])
      ->filters([
        SelectFilter::make('status')
          ->label(__('app.filters.review.status'))
          ->options(ReviewStatus::options()),

        SelectFilter::make('rating')
          ->label(__('app.filters.review.rating'))
          ->options([
            1 => '1 ⭐',
            2 => '2 ⭐⭐',
            3 => '3 ⭐⭐⭐',
            4 => '4 ⭐⭐⭐⭐',
            5 => '5 ⭐⭐⭐⭐⭐',
          ]),

        Filter::make('created_at')
          ->form([
            Forms\Components\DatePicker::make('created_from')
              ->label(__('app.filters.review.created_from')),
            Forms\Components\DatePicker::make('created_until')
              ->label(__('app.filters.review.created_until')),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['created_from'],
                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
              )
              ->when(
                $data['created_until'],
                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
              );
          })

      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make()
            ->icon('heroicon-o-eye')
            ->color('info'),

          Tables\Actions\Action::make('change_status')
            ->label(__('app.actions.change_status'))
            ->icon('heroicon-o-pencil')
            ->color('warning')
            ->form([
              \Filament\Forms\Components\Select::make('status')
                ->label(__('app.fields.new_status'))
                ->options(ReviewStatus::options())
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
        ]),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('approve_selected')
            ->label(__('app.actions.review.approve_selected'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('app.actions.review.approve_selected_modal_heading'))
            ->modalDescription(__('app.actions.review.approve_selected_modal_description'))
            ->action(function (Collection $records): void {
              $ids = $records->pluck('id')->toArray();
              app(ReviewServiceInterface::class)->bulkApproveReviews($ids);
            })
            ->modalIcon('heroicon-o-check-circle'),

          Tables\Actions\BulkAction::make('reject_selected')
            ->label(__('app.actions.review.reject_selected'))
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->modalHeading(__('app.actions.review.reject_selected_modal_heading'))
            ->modalDescription(__('app.actions.review.reject_selected_modal_description'))
            ->action(function (Collection $records): void {
              $ids = $records->pluck('id')->toArray();
              app(ReviewServiceInterface::class)->bulkRejectReviews($ids);
            }),

          Tables\Actions\DeleteBulkAction::make()
            ->icon('heroicon-o-trash')
            ->color('danger'),
        ]),
      ])
      ->defaultSort('created_at', 'desc')
      ->striped()
      ->paginated([10, 25, 50, 100]);
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
      'index' => Pages\ListReviews::route('/'),
      'view' => Pages\ViewReview::route('/{record}'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return app(ReviewServiceInterface::class)->getQueryBuilder();
  }

  public static function getNavigationBadge(): ?string
  {
    return (string) app(ReviewServiceInterface::class)->getReviewsCountByStatus(ReviewStatus::PENDING->value);
  }
}
