<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use App\Models\Coupon\Coupon;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewCoupon extends ViewRecord
{
  protected static string $resource = CouponResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\EditAction::make(),
      Actions\DeleteAction::make(),
    ];
  }



  protected function getInfolistComponents(): array
  {
    /** @var Coupon $record */
    $record = $this->getRecord();

    return [
      Section::make('Coupon Details')
        ->columns(12)
        ->schema([
          Grid::make(12)->schema([
            TextEntry::make('code')->label('Code')->columnSpan(3)->copyable(),
            TextEntry::make('name')->label('Name')->columnSpan(9),
          ]),
          Grid::make(12)->schema([
            TextEntry::make('type')->label('Type')->columnSpan(3)->formatStateUsing(fn($s) => ucfirst($s)),
            TextEntry::make('discount_amount')
              ->label('Discount')
              ->state(function (Coupon $coupon) {
                $value = number_format((float) $coupon->discount_amount, 2);
                return $coupon->type === Coupon::PERCENTAGE ? $value . '%' : $value . ' SAR';
              })
              ->columnSpan(3),
            TextEntry::make('status')->label('Status')->columnSpan(3)->formatStateUsing(fn($s) => ucfirst($s)),
            TextEntry::make('created_at')->dateTime()->label('Created')->columnSpan(3),
          ]),
          Grid::make(12)->schema([
            TextEntry::make('usage_limit')->label('Usage Limit')->columnSpan(3)->formatStateUsing(fn($s) => $s === null ? 'Unlimited' : $s),
            TextEntry::make('usage_limit_per_user')->label('Per User Limit')->columnSpan(3)->formatStateUsing(fn($s) => $s === null ? 'Unlimited' : $s),
            TextEntry::make('start_date')->label('Start Date')->date()->columnSpan(3),
            TextEntry::make('end_date')->label('End Date')->date()->columnSpan(3),
          ]),
          Grid::make(12)->schema([
            TextEntry::make('times_used_total')
              ->label('Total Times Used')
              ->state(function (Coupon $coupon) {
                return (int) $coupon->couponUsers()->sum('times_used');
              })
              ->columnSpan(3),
            TextEntry::make('remaining_uses')
              ->label('Remaining Uses')
              ->state(function (Coupon $coupon) {
                if ($coupon->usage_limit === null) {
                  return 'Unlimited';
                }
                $used = (int) $coupon->couponUsers()->sum('times_used');
                return max($coupon->usage_limit - $used, 0);
              })
              ->columnSpan(3),
          ]),
        ]),
    ];
  }
}
