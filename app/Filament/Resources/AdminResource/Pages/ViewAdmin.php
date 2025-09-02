<?php

namespace App\Filament\Resources\AdminResource\Pages;

use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\Grid;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\AdminResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;

class ViewAdmin extends ViewRecord
{
  protected static string $resource = AdminResource::class;

  public function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        // Basic Information Section
        Section::make(__('app.forms.admin.basic_information'))
          ->description(__('app.forms.admin.basic_information_description'))
          ->icon('heroicon-o-user')
          ->schema([
            Grid::make(3)
              ->schema([
                TextEntry::make('name')
                  ->label(__('app.forms.admin.name'))
                  ->size(TextEntry\TextEntrySize::Large)
                  ->weight('bold')
                  ->icon('heroicon-o-user-circle'),

                TextEntry::make('email')
                  ->label(__('app.forms.admin.email'))
                  ->icon('heroicon-m-envelope')
                  ->copyable()
                  ->url(fn($record) => "mailto:{$record->email}")
                  ->color('primary'),

                TextEntry::make('phone')
                  ->label(__('app.forms.admin.phone'))
                  ->icon('heroicon-m-phone')
                  ->copyable()
                  ->url(fn($record) => $record->phone ? "tel:{$record->phone}" : null)
                  ->color('info'),
              ]),
          ])
          ->collapsible(false),

        // Account Status Section
        Section::make(__('app.forms.admin.account_status'))
          ->description(__('app.forms.admin.account_status_description'))
          ->icon('heroicon-o-shield-check')
          ->schema([
            Grid::make(2)
              ->schema([
                IconEntry::make('verified')
                  ->label(__('app.columns.admin.verified'))
                  ->boolean()
                  ->trueIcon('heroicon-o-check-circle')
                  ->falseIcon('heroicon-o-x-circle')
                  ->trueColor('success')
                  ->falseColor('danger')
                  ->size(IconEntry\IconEntrySize::Large),

                IconEntry::make('email_verified_at')
                  ->label(__('app.columns.admin.email_verified'))
                  ->boolean()
                  ->trueIcon('heroicon-o-check-circle')
                  ->falseIcon('heroicon-o-x-circle')
                  ->trueColor('success')
                  ->falseColor('danger')
                  ->size(IconEntry\IconEntrySize::Large),
              ]),

            TextEntry::make('email_verified_at')
              ->label(__('app.forms.admin.email_verified_at'))
              ->dateTime()
              ->icon('heroicon-o-calendar')
              ->color('success')
              ->visible(fn($record) => $record->email_verified_at !== null),
          ])
          ->collapsible(false),

        // Role Assignment Section
        Section::make(__('app.forms.admin.role_assignment'))
          ->description(__('app.forms.admin.role_assignment_description'))
          ->icon('heroicon-o-shield-check')
          ->schema([
            TextEntry::make('roles')
              ->label(__('app.forms.admin.user_roles'))
              ->listWithLineBreaks()
              ->separator(',')
              ->badge()
              ->colors([
                'primary' => 'super_admin',
                'success' => 'admin',
                'warning' => 'manager',
                'info' => 'editor',
                'secondary' => 'viewer',
              ])
              ->icon('heroicon-o-shield-check')
              ->state(fn($record) => $record->roles->pluck('name')->toArray()),

            TextEntry::make('roles_count')
              ->label(__('app.forms.admin.total_roles'))
              ->state(fn($record) => $record->roles->count())
              ->icon('heroicon-o-calculator')
              ->color('info'),
          ])
          ->collapsible(false),

        // System Information Section
        Section::make(__('app.forms.admin.system_information'))
          ->description(__('app.forms.admin.system_information_description'))
          ->icon('heroicon-o-cog-6-tooth')
          ->schema([
            Grid::make(2)
              ->schema([
                TextEntry::make('created_at')
                  ->label(__('app.columns.admin.created'))
                  ->dateTime()
                  ->icon('heroicon-o-calendar')
                  ->color('success'),

                TextEntry::make('updated_at')
                  ->label(__('app.columns.admin.updated'))
                  ->dateTime()
                  ->icon('heroicon-o-calendar-days')
                  ->color('warning'),
              ]),

            TextEntry::make('id')
              ->label(__('app.forms.admin.admin_id'))
              ->icon('heroicon-o-finger-print')
              ->color('secondary')
              ->copyable(),
          ])
          ->collapsible(false),

        // Additional Information Section
        Section::make(__('app.forms.admin.additional_information'))
          ->description(__('app.forms.admin.additional_information_description'))
          ->icon('heroicon-o-information-circle')
          ->schema([
            KeyValueEntry::make('account_details')
              ->label(__('app.forms.admin.account_details'))
              ->keyLabel(__('app.forms.admin.field'))
              ->valueLabel(__('app.forms.admin.value'))
              ->columnSpanFull()
              ->state(fn($record) => [
                __('app.forms.admin.account_type') => 'Administrator',
                __('app.forms.admin.access_level') => $record->roles->count() > 0 ? 'Role-based' : 'Basic',
                __('app.forms.admin.last_activity') => session()->get('last_activity_' . Auth::guard('admin')->id()) ?? __('app.forms.admin.never'),
                __('app.forms.admin.status') => $record->verified ? 'Active' : 'Inactive',
              ]),
          ])
          ->collapsible(false),
      ]);
  }
}
