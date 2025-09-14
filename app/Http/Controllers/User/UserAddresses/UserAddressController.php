<?php

namespace App\Http\Controllers\User\UserAddresses;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserAddress\UserAddressStoreRequest;
use App\Http\Requests\User\UserAddress\UserAddressUpdateRequest;
use App\Services\User\UserAddresses\UserAddressService;
use Illuminate\Http\Request;

use function App\Helpers\showAll;
use function App\Helpers\showMessage;
use function App\Helpers\showOne;

class UserAddressController extends Controller
{
    public function __construct(private UserAddressService $userAddressService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(int $userId)
    {
        $addresses = $this->userAddressService->getAllUserAddresses($userId);

        return showAll($addresses, "User Addresses", 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $userId)
    {
        dd("test");
        $data = $request->validated();

        $address = $this->userAddressService->storeUserAddress($data, $userId);

        return showOne($address, "User Address", 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserAddressUpdateRequest $request, int $userId, int $addressId)
    {
        $data = $request->validated();

        $updatedAddress = $this->userAddressService->updateUserAddress($data, $userId, $addressId);

        return showOne($updatedAddress, "User Address", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $userId, int $addressId)
    {
        return $this->userAddressService->deleteUserAddress($userId, $addressId);
    }
}
