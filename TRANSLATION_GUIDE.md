# Filament Translation Guide

This guide explains how to implement comprehensive translations in your Filament admin panel following best practices.

## 📁 File Structure

```
lang/
├── en/
│   ├── app.php          # Application-specific translations (English)
│   ├── auth.php         # Authentication translations
│   ├── pagination.php   # Pagination translations
│   ├── passwords.php    # Password reset translations
│   └── validation.php   # Validation translations
├── ar/
│   ├── app.php          # Application-specific translations (Arabic)
│   ├── auth.php         # Authentication translations
│   ├── pagination.php   # Pagination translations
│   ├── passwords.php    # Password reset translations
│   └── validation.php   # Validation translations
└── vendor/
    ├── filament/        # Filament core translations
    ├── filament-panels/ # Filament panels translations
    ├── filament-forms/  # Filament forms translations
    ├── filament-tables/ # Filament tables translations
    └── ...              # Other Filament component translations
```

## 🔧 Setup

### 1. Language Service Provider

Your `LanguageServiceProvider` is already configured to support English and Arabic:

```php
// app/Providers/Language/LanguageServiceProvider.php
LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
    $switch
        ->locales(['en', 'ar'])
        ->visible(insidePanels: true);
});
```

### 2. Translation Files

We've created comprehensive translation files:

-   `lang/en/app.php` - English translations
-   `lang/ar/app.php` - Arabic translations

### 3. HasTranslations Trait

Use the `HasTranslations` trait in your resources for helper methods:

```php
use App\Traits\HasTranslations;

class CategoryResource extends Resource
{
    use HasTranslations;

    // Your resource code...
}
```

## 📝 Translation Keys Structure

### Important: Translation Key Format

All translation keys must use the `__()` helper function with the `app.` prefix:

```php
// ✅ Correct - Uses __() helper with app. prefix
->label(__('app.forms.category.name_en'))
->placeholder(__('app.forms.category.enter_name_en'))
->copyMessage(__('app.messages.slug_copied'))

// ❌ Incorrect - Raw translation keys don't work
->label('forms.category.name_en')
->placeholder('forms.category.enter_name_en')
->copyMessage('messages.slug_copied')
```

### Navigation

```php
'navigation' => [
    'store_management' => 'Store Management',
    'user_management' => 'User Management',
    'content_management' => 'Content Management',
    'system' => 'System',
],
```

### Resources

```php
'resources' => [
    'category' => [
        'label' => 'Category',
        'plural_label' => 'Categories',
        'navigation_label' => 'Categories',
        'navigation_group' => 'navigation.store_management',
        'navigation_icon' => 'heroicon-o-squares-2x2',
        'navigation_sort' => 1,
    ],
],
```

### Forms

```php
'forms' => [
    'category' => [
        'information' => 'Category Information',
        'information_description' => 'Enter the basic details for this category',
        'translations' => 'Translations',
        'english' => 'English',
        'arabic' => 'Arabic',
        'name_en' => 'Name (EN)',
        'name_ar' => 'Name (AR)',
        // ... more fields
    ],
],
```

### Table Columns

```php
'columns' => [
    'translated_name' => 'Category Name',
    'translated_description' => 'Description',
    'product_count' => 'Products',
    'slug' => 'Slug',
    'image' => 'Image',
    // ... more columns
],
```

### Messages

```php
'messages' => [
    'slug_copied' => 'Slug copied!',
    'category_created' => 'Category created successfully.',
    'category_updated' => 'Category updated successfully.',
    'category_deleted' => 'Category deleted successfully.',
    // ... more messages
],
```

## 🚀 Usage Examples

### 1. Resource Configuration

```php
class CategoryResource extends Resource
{
    use HasTranslations;

    protected static ?string $navigationGroup = 'Store Management';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?string $modelLabel = 'Category';
    protected static ?string $pluralModelLabel = 'Categories';

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
        return __('app.resources.category.navigation_label');
    }

    /**
     * Get the translated model label
     */
    public static function getModelLabel(): string
    {
        return __('app.resources.category.label');
    }

    /**
     * Get the translated plural model label
     */
    public static function getPluralModelLabel(): string
    {
        return __('app.resources.category.plural_label');
    }
}
```

### 2. Form Fields

```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make(__('app.forms.category.information'))
                ->description(__('app.forms.category.information_description'))
                ->schema([
                    Forms\Components\TextInput::make('en.name')
                        ->label(__('app.forms.category.name_en'))
                        ->placeholder(__('app.forms.category.enter_name_en'))
                        ->required(),
                    Forms\Components\Textarea::make('en.description')
                        ->label(__('app.forms.category.description_en'))
                        ->placeholder(__('app.forms.category.enter_description_en')),
                ]),
        ]);
}
```

### 3. Table Columns

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('translated_name')
                ->label(__('app.columns.translated_name'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('slug')
                ->label(__('app.columns.slug'))
                ->copyable()
                ->copyMessage(__('app.messages.slug_copied')),
            Tables\Columns\TextColumn::make('product_count')
                ->label(__('app.columns.product_count'))
                ->counts('products'),
        ]);
}
```

### 4. Actions

```php
Tables\Actions\ActionGroup::make([
    Tables\Actions\ViewAction::make()
        ->label(__('app.actions.view'))
        ->icon('heroicon-o-eye'),
    Tables\Actions\EditAction::make()
        ->label(__('app.actions.edit'))
        ->icon('heroicon-o-pencil'),
    Tables\Actions\DeleteAction::make()
        ->label(__('app.actions.delete'))
        ->icon('heroicon-o-trash')
        ->color('danger'),
])
```

### 5. Filters

```php
public static function table(Table $table): Table
{
    return $table
        ->filters([
            Tables\Filters\Filter::make('created_at')
                ->label(__('app.filters.created_at'))
                ->form([
                    Forms\Components\DatePicker::make('created_from')
                        ->label(__('app.filters.created_from')),
                    Forms\Components\DatePicker::make('created_until')
                        ->label(__('app.filters.created_until'))
                ])
        ]);
}
```

### 6. Using the HasTranslations Trait

```php
class CategoryResource extends Resource
{
    use HasTranslations;

    public function someMethod()
    {
        // Get translation keys
        $navigationKey = $this->getNavigationGroupKey('store_management');
        $resourceKey = $this->getResourceKey('category', 'label');
        $formKey = $this->getFormKey('category', 'name_en');
        $columnKey = $this->getColumnKey('translated_name');
        $messageKey = $this->getMessageKey('category_created');

        // Get translated text directly
        $navigationLabel = $this->getResourceLabel('category', 'navigation_label');
        $formLabel = $this->getFormLabel('category', 'name_en');
        $columnLabel = $this->getColumnLabel('translated_name');
        $message = $this->getMessage('category_created');

        // Check current locale
        $locale = $this->getCurrentLocale(); // 'en' or 'ar'
        $isArabic = $this->isArabic(); // true/false
        $isEnglish = $this->isEnglish(); // true/false

        // Get RTL support
        $direction = $this->getTextDirection(); // 'rtl' or 'ltr'
        $rtlAttributes = $this->getRtlAttributes(); // ['dir' => 'rtl'] or []
    }
}
```

## 🌐 RTL Support for Arabic

### Form Fields

```php
Forms\Components\TextInput::make('ar.name')
    ->label(__('app.forms.category.name_ar'))
    ->placeholder(__('app.forms.category.enter_name_ar'))
    ->extraAttributes(['dir' => 'rtl']) // RTL support
    ->required(),
```

### Using the Trait

```php
Forms\Components\TextInput::make('ar.name')
    ->label(__('app.forms.category.name_ar'))
    ->placeholder(__('app.forms.category.enter_name_ar'))
    ->extraAttributes($this->getRtlAttributes()) // Automatic RTL
    ->required(),
```

## 📋 Best Practices

### 1. Consistent Naming

-   Use descriptive, hierarchical keys
-   Follow the pattern: `section.resource.field`
-   Keep keys lowercase with underscores

### 2. Organization

-   Group related translations together
-   Use comments to separate sections
-   Keep translations close to where they're used

### 3. Reusability

-   Create common translation keys for repeated elements
-   Use the `HasTranslations` trait for helper methods
-   Avoid hardcoding text in your resources

### 4. Maintenance

-   Keep English and Arabic translations in sync
-   Use translation keys instead of hardcoded strings
-   Document any special translation requirements

### 5. Testing

-   Test both languages thoroughly
-   Verify RTL layout for Arabic
-   Check that all dynamic content is translated

## 🔄 Adding New Resources

When creating new resources, follow this pattern:

1. **Add translation keys** to `lang/en/app.php` and `lang/ar/app.php`
2. **Use the HasTranslations trait** in your resource
3. **Reference translation keys** instead of hardcoded strings
4. **Test both languages** to ensure proper translation

### Example for a New Product Resource

```php
// lang/en/app.php
'resources' => [
    'product' => [
        'label' => 'Product',
        'plural_label' => 'Products',
        'navigation_label' => 'Products',
        'navigation_group' => 'navigation.store_management',
        'navigation_icon' => 'heroicon-o-cube',
        'navigation_sort' => 2,
    ],
],

// lang/ar/app.php
'resources' => [
    'product' => [
        'label' => 'المنتج',
        'plural_label' => 'المنتجات',
        'navigation_label' => 'المنتجات',
        'navigation_group' => 'navigation.store_management',
        'navigation_icon' => 'heroicon-o-cube',
        'navigation_sort' => 2,
    ],
],

// app/Filament/Resources/ProductResource.php
class ProductResource extends Resource
{
    use HasTranslations;

    protected static ?string $navigationGroup = 'navigation.store_management';
    protected static ?string $navigationLabel = 'resources.product.navigation_label';
    protected static ?string $modelLabel = 'resources.product.label';
    protected static ?string $pluralModelLabel = 'resources.product.plural_label';
}
```

## 🎯 Key Benefits

1. **Consistent UI**: All text is properly translated
2. **Maintainable**: Centralized translation management
3. **Scalable**: Easy to add new languages
4. **RTL Support**: Proper Arabic layout support
5. **Best Practices**: Follows Filament and Laravel conventions
6. **Developer Friendly**: Helper methods and clear structure

## 🔧 Troubleshooting

### Common Issues

1. **Translation keys showing as raw text**

    - **Problem**: `resources.category.plural_label` shows instead of "Categories"
    - **Solution**: Use `__('app.resources.category.plural_label')` instead of raw keys

2. **Translations not updating when switching languages**

    - **Problem**: Language switch doesn't change the text
    - **Solution**: Make sure you're using `__()` helper function, not raw strings

3. **Missing translations**

    - **Problem**: Some text shows as translation keys
    - **Solution**: Add missing keys to both `lang/en/app.php` and `lang/ar/app.php`

4. **RTL not working for Arabic**
    - **Problem**: Arabic text doesn't display right-to-left
    - **Solution**: Add `->extraAttributes(['dir' => 'rtl'])` to Arabic form fields

### Debugging Tips

-   Use `dd(__('app.key'))` to test if a translation key works
-   Check browser console for any JavaScript errors
-   Verify that both language files have the same key structure
-   Clear Laravel cache: `php artisan cache:clear`

This setup provides a solid foundation for a fully translated Filament admin panel that supports both English and Arabic with proper RTL support.
