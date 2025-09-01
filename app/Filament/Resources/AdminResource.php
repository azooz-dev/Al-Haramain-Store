<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin\Admin;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $slug = 'admins';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return __('app.resources.admin.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('app.resources.admin.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.resources.admin.plural_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.user_management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('app.forms.admin.basic_information'))
                    ->description(__('app.forms.admin.basic_information_description'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label(__('app.forms.admin.first_name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('app.forms.admin.enter_first_name'))
                                    ->columnSpan(1),

                                TextInput::make('last_name')
                                    ->label(__('app.forms.admin.last_name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('app.forms.admin.enter_last_name'))
                                    ->columnSpan(1),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label(__('app.forms.admin.email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder(__('app.forms.admin.enter_email'))
                                    ->columnSpan(1),

                                TextInput::make('phone')
                                    ->label(__('app.forms.admin.phone'))
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder(__('app.forms.admin.enter_phone'))
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(false),

                Section::make(__('app.forms.admin.authentication_security'))
                    ->description(__('app.forms.admin.authentication_security_description'))
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label(__('app.forms.admin.password'))
                                    ->password()
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->rules([
                                        Password::min(8)
                                            ->letters()
                                            ->mixedCase()
                                            ->numbers()
                                            ->symbols()
                                    ])
                                    ->placeholder(__('app.forms.admin.enter_password'))
                                    ->columnSpan(1),

                                TextInput::make('password_confirmation')
                                    ->label(__('app.forms.admin.confirm_password'))
                                    ->password()
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->same('password')
                                    ->placeholder(__('app.forms.admin.enter_confirm_password'))
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(false),

                Section::make(__('app.forms.admin.role_assignment'))
                    ->description(__('app.forms.admin.role_assignment_description'))
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Select::make('roles')
                            ->label(__('app.forms.admin.user_roles'))
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->placeholder(__('app.forms.admin.select_roles'))
                            ->helperText(__('app.forms.admin.roles_help'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label(__('app.columns.admin.first_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('last_name')
                    ->label(__('app.columns.admin.last_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label(__('app.columns.admin.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('phone')
                    ->label(__('app.columns.admin.phone'))
                    ->searchable()
                    ->icon('heroicon-m-phone'),

                BadgeColumn::make('roles.name')
                    ->label(__('app.columns.admin.roles'))
                    ->separator(',')
                    ->colors([
                        'primary' => 'super_admin',
                        'success' => 'admin',
                        'warning' => 'manager',
                        'info' => 'editor',
                        'secondary' => 'viewer',
                    ]),

                IconColumn::make('verified')
                    ->label(__('app.columns.admin.verified'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('email_verified_at')
                    ->label(__('app.columns.admin.email_verified'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('app.columns.admin.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('app.columns.admin.updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('verified')
                    ->label(__('app.forms.admin.filters.account_status'))
                    ->options([
                        '1' => 'Verified',
                        '0' => 'Unverified',
                    ]),

                SelectFilter::make('email_verified_at')
                    ->label(__('app.forms.admin.filters.email_status'))
                    ->options([
                        '1' => 'Email Verified',
                        '0' => 'Email Not Verified',
                    ]),

                Filter::make('has_roles')
                    ->label(__('app.forms.admin.filters.has_roles'))
                    ->query(fn($query) => $query->whereHas('roles'))
                    ->toggle(),

                Filter::make('created_date')
                    ->label(__('app.forms.admin.filters.created_date'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('app.forms.admin.filters.created_from')),
                        DatePicker::make('created_until')
                            ->label(__('app.forms.admin.filters.created_until')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn($query) => $query->whereDate('created_at', '>=', $data['created_from'])
                            )
                            ->when(
                                $data['created_until'],
                                fn($query) => $query->whereDate('created_at', '<=', $data['created_until'])
                            );
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

                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.admin.confirm_delete_heading'))
                        ->modalDescription(__('app.messages.admin.confirm_delete_description'))
                        ->modalSubmitActionLabel('Yes, delete user')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('app.messages.admin.deleted_success'))
                                ->body(__('app.messages.admin.deleted_success_body'))
                        ),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.admin.confirm_delete_bulk_heading'))
                        ->modalDescription(__('app.messages.admin.confirm_delete_bulk_description'))
                        ->modalSubmitActionLabel('Yes, delete users'),
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
            'view' => Pages\ViewAdmin::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles'])
            ->withCount(['roles']);
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : 'danger';
    }
}
