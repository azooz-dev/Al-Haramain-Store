<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
  public function run(): void
  {
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $this->createResourcePermissions();
    $this->createWidgetPermissions();

    $this->command->info('All permissions created successfully!');
    $this->command->info('Total permissions: ' . Permission::where('guard_name', 'admin')->count());
  }

  private function createResourcePermissions(): void
  {
    $resources = [
      'category',
      'product',
      'order',
      'offer',
      'coupon',
      'review',
      'favorite',
      'role',
      'admin'
    ];

    $actions = [
      'view',
      'view_any',
      'create',
      'update',
      'restore',
      'restore_any',
      'replicate',
      'reorder',
      'delete',
      'delete_any',
      'force_delete',
      'force_delete_any',
    ];

    foreach ($resources as $resource) {
      foreach ($actions as $action) {
        Permission::firstOrCreate([
          'name' => "{$action}_{$resource}",
          'guard_name' => 'admin',
        ]);
      }
    }
  }

  private function createWidgetPermissions(): void
  {
    $widgets = [
      'kpi_stats_widget',
      'order_status_widget',
      'revenue_overview_widget',
      'top_products_widget',
      'customer_analytics_widget',
      'recent_orders_widget',
      'review_analytics_widget',
    ];

    foreach ($widgets as $widget) {
      Permission::firstOrCreate([
        'name' => "widget_{$widget}",
        'guard_name' => 'admin',
      ]);
    }
  }
}
