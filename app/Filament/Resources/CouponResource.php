<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Coupon\Enums\CouponType;
use Modules\Coupon\Enums\CouponStatus;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CouponResource\Pages;
use Modules\Coupon\Contracts\CouponServiceInterface;
use App\Filament\Concerns\SendsFilamentNotifications;
use Filament\Support\Exceptions\Halt;

class CouponResource extends Resource
{
    use SendsFilamentNotifications;
    protected static ?string $model = Coupon::class;

    protected static ?string $slug = 'coupons';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?string $navigationLabel = 'Coupons';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Coupon';

    protected static ?string $pluralModelLabel = 'Coupons';

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    /**
     * Get navigation badge with coupon count
     */
    public static function getNavigationBadge(): ?string
    {
        return app(CouponServiceInterface::class)->getCouponsCount();
    }


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
        return __('app.resources.coupon.navigation_label');
    }

    /**
     * Get the translated model label
     */
    public static function getModelLabel(): string
    {
        return __('app.resources.coupon.label');
    }

    /**
     * Get the translated plural model label
     */
    public static function getPluralModelLabel(): string
    {
        return __('app.resources.coupon.plural_label');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('app.forms.coupon.section_details'))
                    ->columns(12)
                    ->schema([
                        Grid::make(12)->schema([
                            TextInput::make('code')
                                ->label(__('app.forms.coupon.code'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50)
                                ->columnSpan(4),
                            TextInput::make('name')
                                ->label(__('app.forms.coupon.name'))
                                ->required()
                                ->maxLength(100)
                                ->columnSpan(8),
                        ]),

                        Grid::make(12)->schema([
                            Select::make('type')
                                ->label(__('app.forms.coupon.type'))
                                ->required()
                                ->options(CouponType::options())
                                ->native(false)
                                ->columnSpan(4),
                            TextInput::make('discount_amount')
                                ->label(__('app.forms.coupon.discount_amount'))
                                ->numeric()
                                ->required()
                                ->step('0.01')
                                ->suffix(function (callable $get) {
                                    return $get('type') === CouponType::PERCENTAGE->value ? '%' : 'US';
                                })
                                ->rule(function (callable $get) {
                                    return $get('type') === CouponType::PERCENTAGE->value
                                        ? 'between:0,100'
                                        : 'min:0';
                                })
                                ->columnSpan(4),
                            Select::make('status')
                                ->label(__('app.forms.coupon.status'))
                                ->required()
                                ->options(CouponStatus::options())
                                ->native(false)
                                ->columnSpan(4),
                        ]),

                        Grid::make(12)->schema([
                            TextInput::make('usage_limit')
                                ->label(__('app.forms.coupon.usage_limit'))
                                ->numeric()
                                ->minValue(0)
                                ->helperText(__('app.forms.coupon.usage_limit_help'))
                                ->columnSpan(4),
                            TextInput::make('usage_limit_per_user')
                                ->label(__('app.forms.coupon.usage_limit_per_user'))
                                ->numeric()
                                ->minValue(0)
                                ->helperText(__('app.forms.coupon.usage_limit_per_user_help'))
                                ->columnSpan(4),
                            DatePicker::make('start_date')
                                ->label(__('app.forms.coupon.start_date'))
                                ->native(false)
                                ->columnSpan(2),
                            DatePicker::make('end_date')
                                ->label(__('app.forms.coupon.end_date'))
                                ->native(false)
                                ->after('start_date')
                                ->columnSpan(2),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $couponService = app(CouponServiceInterface::class);

        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('app.columns.coupon.code'))
                    ->searchable()
                    ->copyable()
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label(__('app.columns.coupon.name'))
                    ->searchable()
                    ->toggleable()
                    ->alignCenter(),
                BadgeColumn::make('type')
                    ->label(__('app.columns.coupon.type'))
                    ->color(fn(CouponType $state): string => match ($state) {
                        CouponType::FIXED => 'primary',
                        CouponType::PERCENTAGE => 'warning',
                    })
                    ->formatStateUsing(fn(CouponType $state): string => $state->label())
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('discount_amount')
                    ->label(__('app.columns.coupon.discount'))
                    ->formatStateUsing(function (Coupon $record) {
                        $value = number_format((float) $record->discount_amount, 2);
                        return $record->type === CouponType::PERCENTAGE ? $value . '%' : $value . ' ' . __('app.forms.coupon.sar');
                    })
                    ->sortable()
                    ->alignCenter()
                    ->icon('heroicon-o-currency-dollar'),
                TextColumn::make('coupon_users_sum_times_used')
                    ->label(__('app.columns.coupon.times_used'))
                    ->sortable()
                    ->alignCenter()
                    ->icon('heroicon-o-user-group'),
                TextColumn::make('usage_limit')
                    ->label(__('app.columns.coupon.usage_limit'))
                    ->formatStateUsing(fn($state) => $state === null ? 'Unlimited' : $state)
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('remaining_uses')
                    ->label(__('app.columns.coupon.remaining_uses'))
                    ->state(function (Coupon $record) use ($couponService) {
                        return $couponService->getRemainingUses($record);
                    })
                    ->sortable()
                    ->alignCenter()
                    ->icon('heroicon-o-clock'),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->alignCenter()
                    ->icon('heroicon-o-clock'),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->alignCenter()
                    ->icon('heroicon-o-clock'),
                BadgeColumn::make('status')
                    ->color(fn(CouponStatus $state): string => $state->color())
                    ->formatStateUsing(fn(CouponStatus $state): string => $state->label())
                    ->sortable()
                    ->alignCenter()
                    ->icon(fn(CouponStatus $state): string => $state->icon()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter()
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CouponStatus::options()),
                SelectFilter::make('type')
                    ->options(CouponType::options()),
                Filter::make('active_now')
                    ->label(__('app.status.active'))
                    ->query(function (Builder $query) {
                        $today = now()->toDateString();
                        $query->where('status', CouponStatus::ACTIVE)
                            ->where(function (Builder $q) use ($today) {
                                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
                            })
                            ->where(function (Builder $q) use ($today) {
                                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
                            });
                    }),
                Filter::make('has_remaining')
                    ->label(__('app.columns.coupon.remaining_uses'))
                    ->query(function (Builder $query) {
                        $query->where(function (Builder $q) {
                            $q->whereNull('usage_limit')
                                ->orWhereRaw('usage_limit > (SELECT COALESCE(SUM(times_used), 0) FROM coupon_users WHERE coupon_users.coupon_id = coupons.id)');
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(__('app.actions.toggle_status'))
                        ->action(function (Coupon $record) {
                            $couponService = app(CouponServiceInterface::class);
                            $couponService->toggleCouponStatus($record->id);
                            // Refresh the record to get updated status
                            $record->refresh();
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-o-power')
                        ->color('success')
                        ->successNotification(
                            fn($record) => self::buildSuccessNotification(
                                __('app.messages.coupon.status_updated'),
                                __('app.messages.coupon.status_updated_body', ['status' => $record->status === CouponStatus::ACTIVE ? __('app.status.active') : __('app.status.inactive')])
                            )
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.coupon.confirm_delete_heading'))
                        ->modalDescription(__('app.messages.coupon.confirm_delete_description'))
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->before(function (Coupon $record) {
                            $couponService = app(CouponServiceInterface::class);
                            if (!$couponService->canDeleteCoupon($record)) {
                                self::buildErrorNotification(
                                    __('app.messages.coupon.must_be_empty'),
                                    __('app.messages.coupon.must_be_empty_description')
                                )->send();

                                throw new Halt();
                            }
                            return true;
                        })
                        ->successNotification(
                            fn($record) => self::buildSuccessNotification(
                                __('app.messages.coupon.deleted_success'),
                                __('app.messages.coupon.deleted_success_body', ['name' => $record->name])
                            )
                        ),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label(__('app.actions.activate'))
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $couponService = app(CouponServiceInterface::class);
                            $ids = $records->pluck('id')->toArray();
                            $couponService->activateCoupons($ids);
                        })
                        ->color('success')
                        ->icon('heroicon-o-bolt')
                        ->successNotification(
                            fn($records) => self::buildSuccessNotification(
                                __('app.messages.coupon.activated_success_bulk'),
                                __('app.messages.coupon.activated_success_body_bulk')
                            )
                        ),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(__('app.actions.deactivate'))
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $couponService = app(CouponServiceInterface::class);
                            $ids = $records->pluck('id')->toArray();
                            $couponService->deactivateCoupons($ids);
                        })
                        ->color('danger')
                        ->icon('heroicon-o-power')
                        ->successNotification(
                            fn($records) => self::buildSuccessNotification(
                                __('app.messages.coupon.deactivated_success_bulk'),
                                __('app.messages.coupon.deactivated_success_body_bulk')
                            )
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.coupon.confirm_delete_bulk_heading'))
                        ->modalDescription(__('app.messages.coupon.confirm_delete_bulk_description'))
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->before(function (\Illuminate\Support\Collection $records) {
                            $couponService = app(CouponServiceInterface::class);
                            foreach ($records as $record) {
                                if (!$couponService->canDeleteCoupon($record)) {
                                    return self::buildErrorNotification(
                                        __('app.messages.coupon.must_be_empty'),
                                        __('app.messages.coupon.must_be_empty_description')
                                    );
                                }
                            }
                            return true;
                        })
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->successNotification(
                            fn($records) => self::buildSuccessNotification(
                                __('app.messages.coupon.deleted_success_bulk'),
                                __('app.messages.coupon.deleted_success_body_bulk')
                            )
                        ),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\CouponResource\RelationManagers\CouponUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'view' => Pages\ViewCoupon::route('/{record}'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }

    /**
     * Get the Eloquent query for the resource
     * Uses service layer for query building
     */
    public static function getEloquentQuery(): Builder
    {
        return app(CouponServiceInterface::class)->getQueryBuilder();
    }
}
