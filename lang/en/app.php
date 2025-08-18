<?php

return [
  // Navigation Groups
  'navigation' => [
    'store_management' => 'Store Management',
    'user_management' => 'User Management',
    'content_management' => 'Content Management',
    'system' => 'System',
  ],

  // Resource Labels
  'resources' => [
    'category' => [
      'label' => 'Category',
      'plural_label' => 'Categories',
      'navigation_label' => 'Categories',
      'navigation_group' => 'Store Management',
      'navigation_icon' => 'heroicon-o-squares-2x2',
      'navigation_sort' => 1,
    ],
    'product' => [
      'label' => 'Product',
      'plural_label' => 'Products',
      'navigation_label' => 'Products',
      'navigation_group' => 'Store Management',
      'navigation_icon' => 'heroicon-o-cube',
      'navigation_sort' => 2,
    ],
    'order' => [
      'label' => 'Order',
      'plural_label' => 'Orders',
      'navigation_label' => 'Orders',
      'navigation_group' => 'Store Management',
      'navigation_icon' => 'heroicon-o-shopping-cart',
      'navigation_sort' => 3,
    ],
    'user' => [
      'label' => 'User',
      'plural_label' => 'Users',
      'navigation_label' => 'Users',
      'navigation_group' => 'User Management',
      'navigation_icon' => 'heroicon-o-users',
      'navigation_sort' => 1,
    ],
    'review' => [
      'label' => 'Review',
      'plural_label' => 'Reviews',
      'navigation_label' => 'Reviews',
      'navigation_group' => 'Content Management',
      'navigation_icon' => 'heroicon-o-star',
      'navigation_sort' => 1,
    ],
    'offer' => [
      'label' => 'Offer',
      'plural_label' => 'Offers',
      'navigation_label' => 'Offers',
      'navigation_group' => 'Store Management',
      'navigation_icon' => 'heroicon-o-gift',
      'navigation_sort' => 4,
    ],
    'coupon' => [
      'label' => 'Coupon',
      'plural_label' => 'Coupons',
      'navigation_label' => 'Coupons',
      'navigation_group' => 'Store Management',
      'navigation_icon' => 'heroicon-o-ticket',
      'navigation_sort' => 5,
    ],
    'payment' => [
      'label' => 'Payment',
      'plural_label' => 'Payments',
      'navigation_label' => 'Payments',
      'navigation_group' => 'Store Management',
      'navigation_icon' => 'heroicon-o-credit-card',
      'navigation_sort' => 6,
    ],
    'favorite' => [
      'label' => 'Favorite',
      'plural_label' => 'Favorites',
      'navigation_label' => 'Favorites',
      'navigation_group' => 'User Management',
      'navigation_icon' => 'heroicon-o-heart',
      'navigation_sort' => 2,
    ],
  ],

  // Form Sections and Fields
  'forms' => [
    'category' => [
      'information' => 'Category Information',
      'information_description' => 'Enter the basic details for this category',
      'image' => 'Category Image',
      'image_description' => 'Upload a representative image for this category',
      'translations' => 'Translations',
      'english' => 'English',
      'arabic' => 'Arabic',
      'name_en' => 'Name (EN)',
      'name_ar' => 'Name (AR)',
      'description_en' => 'Description (EN)',
      'description_ar' => 'Description (AR)',
      'enter_name_en' => 'Enter category name (English)',
      'enter_name_ar' => 'أدخل اسم التصنيف',
      'enter_description_en' => 'Describe this category in English...',
      'enter_description_ar' => 'صِف هذا التصنيف باللغة العربية...',
      'upload_image_help' => 'Upload a high-quality image (max 2MB). Recommended size: 800x450px',
    ],
  ],

  // Table Columns
  'columns' => [
    'translated_name' => 'Category Name',
    'translated_description' => 'Description',
    'product_count' => 'Products',
    'slug' => 'Slug',
    'image' => 'Image',
    'status' => 'Status',
    'price' => 'Price',
    'quantity' => 'Quantity',
    'total' => 'Total',
    'user' => 'User',
    'category' => 'Category',
    'order' => 'Order',
    'review' => 'Review',
    'offer' => 'Offer',
    'coupon' => 'Coupon',
    'payment' => 'Payment',
    'favorite' => 'Favorite',
    'created_at' => 'Created',
    'updated_at' => 'Updated',
  ],

  // Messages
  'messages' => [
    'slug_copied' => 'Slug copied!',
    'category_created' => 'Category created successfully.',
    'category_updated' => 'Category updated successfully.',
    'category_deleted' => 'Category deleted successfully.',
    'translation_saved' => 'Translation saved successfully.',
    'image_uploaded' => 'Image uploaded successfully.',
    'no_products_in_category' => 'No products in this category.',
    'category_has_products' => 'This category contains products and cannot be deleted.',
    'confirm_delete_heading' => 'Delete Category',
    'confirm_delete_description' => 'Are you sure you want to delete ":name"? This action cannot be undone.',
    'confirm_delete_bulk_heading' => 'Delete Selected Categories',
    'confirm_delete_bulk_description' => 'Are you sure you want to delete the selected categories? This action cannot be undone.',
  ],

  // Status Labels
  'status' => [
    'active' => 'Active',
    'inactive' => 'Inactive',
    'draft' => 'Draft',
    'published' => 'Published',
    'pending' => 'Pending',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'archived' => 'Archived',
    'deleted' => 'Deleted',
  ],

  // Actions
  'actions' => [
    'view_products' => 'View Products',
    'manage_translations' => 'Manage Translations',
    'generate_slug' => 'Generate Slug',
    'preview_category' => 'Preview Category',
    'export_categories' => 'Export Categories',
    'import_categories' => 'Import Categories',
    'delete' => 'Delete',
    'cancel' => 'Cancel',
  ],

  // Filters
  'filters' => [
    'has_products' => 'Has Products',
    'no_products' => 'No Products',
    'created_today' => 'Created Today',
    'created_this_week' => 'Created This Week',
    'created_this_month' => 'Created This Month',
    'updated_recently' => 'Updated Recently',
    'created_at' => 'Created Date',
    'created_from' => 'Created From',
    'created_until' => 'Created Until',
  ],

  // Placeholders
  'placeholders' => [
    'search_categories' => 'Search categories...',
    'select_category' => 'Select a category...',
    'enter_category_name' => 'Enter category name...',
    'enter_category_description' => 'Enter category description...',
  ],

  // Tooltips
  'tooltips' => [
    'view_category_details' => 'View category details',
    'edit_category' => 'Edit category',
    'delete_category' => 'Delete category',
    'copy_slug' => 'Copy slug to clipboard',
    'preview_image' => 'Preview image',
    'download_image' => 'Download image',
    'view_products_in_category' => 'View products in this category',
    'manage_category_translations' => 'Manage category translations',
  ],
];
