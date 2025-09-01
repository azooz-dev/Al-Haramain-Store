<?php

namespace App\Filament\Resources\CouponResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponUsersRelationManager extends RelationManager
{
  protected static string $relationship = 'couponUsers';

  protected static ?string $title = 'Coupon Usage by Users';

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('user.name')
      ->columns([
        TextColumn::make('user.id')->label('User ID')->sortable()->searchable(),
        TextColumn::make('user.first_name')->label('First Name')->searchable(),
        TextColumn::make('user.last_name')->label('Last Name')->searchable(),
        TextColumn::make('user.email')->label('Email')->searchable(),
        TextColumn::make('times_used')->label('Times Used')->sortable(),
        TextColumn::make('created_at')->label('First Used')->dateTime()->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')->label('Last Used')->dateTime(),
      ])
      ->headerActions([])
      ->actions([])
      ->bulkActions([]);
  }
}
