<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\DTOs\UserDTO;
use App\Exceptions\NoSuchException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    // Show Register Form
    public function show()
    {
        return view('welcome');
    }
}