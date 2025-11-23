<?php


namespace App\Repositories\Interface\User;

use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
  public function update(int $userId, array $data): User;

  public function delete(int $userId): User;

  // Widget-specific methods
  public function getUsersCountByDateRange(Carbon $start, Carbon $end): int;

  public function getUsersCountByDateRangeGrouped(Carbon $start, Carbon $end): Collection;

  public function getReturningCustomersCount(Carbon $start, Carbon $end): int;

  public function getReturningCustomersByDateGrouped(Carbon $start, Carbon $end): Collection;
}
