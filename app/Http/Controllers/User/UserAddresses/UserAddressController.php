<?php

namespace App\Http\Controllers\User\UserAddresses;

use function App\Helpers\showAll;
use function App\Helpers\showOne;
use App\Http\Controllers\Controller;
use function App\Helpers\showMessage;
use App\Models\User\UserAddresses\Address;

use App\Services\User\UserAddresses\UserAddressService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\User\UserAddress\UserAddressStoreRequest;
use App\Http\Requests\User\UserAddress\UserAddressUpdateRequest;

class UserAddressController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private UserAddressService $userAddressService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(int $userId)
    {
        if ($this->authorize('view', Address::class)) {
            $addresses = $this->userAddressService->getAllUserAddresses($userId);

            return showAll($addresses, "User Addresses", 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserAddressStoreRequest $request, int $userId)
    {
        if ($this->authorize('create', Address::class)) {
            $data = $request->validated();

            $address = $this->userAddressService->storeUserAddress($data, $userId);

            return showOne($address, "User Address", 201);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserAddressUpdateRequest $request, int $userId, int $addressId)
    {
        if ($this->authorize('update', Address::class)) {
            $data = $request->validated();

            $updatedAddress = $this->userAddressService->updateUserAddress($data, $userId, $addressId);

            return showOne($updatedAddress, "User Address", 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $userId, int $addressId)
    {
        if ($this->authorize('delete', Address::class)) {
            return $this->userAddressService->deleteUserAddress($userId, $addressId);
        }
    }
}
