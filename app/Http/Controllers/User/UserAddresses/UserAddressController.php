<?php

namespace App\Http\Controllers\User\UserAddresses;

use App\Models\User\User;
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
        $this->authorize('view', Address::class);

        $addresses = $this->userAddressService->getAllUserAddresses($userId);

        return showAll($addresses, "User Addresses", 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserAddressStoreRequest $request, int $userId)
    {
        $this->authorize('create', Address::class);

        $data = $request->validated();

        $address = $this->userAddressService->storeUserAddress($data, $userId);

        return showOne($address, "User Address", 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserAddressUpdateRequest $request, User $user, Address $address)
    {
        $this->authorize('update', $address);

        $data = $request->validated();

        $updatedAddress = $this->userAddressService->updateUserAddress($data, $user->id, $address->id);

        return showOne($updatedAddress, "User Address", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $userId, Address $address)
    {
        $this->authorize('delete', $address);

        return $this->userAddressService->deleteUserAddress($userId, $address->id);
    }
}
