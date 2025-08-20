<?php


use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;


class productResource extends Resource
{
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Components\Section::make(__('app.forms.product.basic_information'))
          ->description(__('app.forms.product.basic_information_description'))
          ->icon('heroicon-o-cube')
          ->schema([
            Components\Grid::make(3)
              ->schema([
                Components\TextInput::make('sku')
                  ->label(__('app.forms.product.sky'))
                  ->required()
                  ->unique(ignoreRescord: true)
                  ->maxLength(50)
                  ->placeholder(__('app.forms.product.enter_sku'))
                  ->prefixIcon('heroicon-o-cube')
                  ->columnSpan(1),

                Components\TextInput::make('quantity')
                  ->label(__('app.forms.product.quantity'))
                  ->numeric()
                  ->placeholder(__('app.forms.product.enter_quantity'))
                  ->minValue(0)
                  ->required()
                  ->prefixIcon('heroicon-o-cube')
                  ->columnSpan(1),

                Components\Hidden::make('slug')
                  ->columnSpan(1),
              ])
          ])
          ->collapsed(false)
          ->collapsible()
          ->columns(3),

        Components\Section::make(__('app.forms.product.translation'))
          ->description(__('app.forms.product.translation_description'))
          ->schema([
            Components\Tabs::make('translation')
              ->tabs([
                Components\Tabs\Tab::make(__('app.forms.product.english'))
                  ->icon('heroicon-o-cube')
                  ->schema([
                    Components\TextInput::make('en.name')
                      ->label(__('app.forms.product.name_en'))
                      ->required()
                      ->maxLength(225)
                      ->placeholder(__('app.forms.app.enter_name_en'))
                      ->rules(['required', 'string', 'max:255'])
                      ->prefixIcon('heroicon-o-cube')
                      ->columnSpanFull(),

                    Components\Textarea::make('en.description')
                      ->label(__('app.forms.product.description_en'))
                      ->required()
                      ->placeholder(__('app.forms.product.enter_description_en'))
                      ->maxLength(65535)
                      ->prefixIcon('heroicon-o-cube')
                      ->rows(6)
                      ->columnSpanFull(),
                  ]),

                Components\Tabs\Tab::make(__('app.forms.product.arabic'))
                  ->icon('heroicon-o-cube')
                  ->schema([
                    Components\TextInput::make('ar.name')
                      ->label(__('app.forms.product.name_ar'))
                      ->required()
                      ->placeholder(__('app.forms.product.enter_name_ar'))
                      ->maxLength(255)
                      ->rules(['required', 'string', 'max:255'])
                      ->prefixIcon('heroicon-o-cube')
                      ->columnSpanFull(),

                    Components\Textarea::make('ar.description')
                      ->label(__('app.forms.product.description_en'))
                      ->placeholder(__('app.forms.product.enter_description_ar'))
                      ->required()
                      ->maxLength(65535)
                      ->rows(6)
                      ->prefixIcon('heroicon-o-cube')
                      ->columnSpanFull(),
                  ]),
              ])
              ->columnSpanFull(),
          ])
          ->collapsible()
          ->collapsed(false),

      ]);
  }
}
