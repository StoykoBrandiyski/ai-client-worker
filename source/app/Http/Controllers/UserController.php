<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\DTOs\UserDTO;
use App\Exceptions\NoSuchException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userManager; // As requested in point 3 (Inject the UserManager)

    public function __construct(UserService $userManager)
    {
        $this->userManager = $userManager;
    }

    // Show Register Form
    public function create()
    {
        return view('auth.register');
    }

    // Process Registration
    public function store(StoreUserRequest $request)
    {
        $dto = new UserDTO(
            $request->username, $request->email, $request->password
        );

        $user = $this->userManager->createUser($dto);
        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Account created successfully!');
    }

    // Show Login Form
    public function login()
    {
        return view('auth.login');
    }

    // Process Login
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    // Process Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // Show Edit User Form
    public function editPage()
    {
        $user = Auth::user();
        return view('auth.edit', compact('user'));
    }

    // Process Edit User
    public function storeEditUser(UpdateUserRequest $request)
    {
        try {
            $data = $request->validated();
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $this->userManager->updateUser(Auth::id(), $data);

            return redirect('/dashboard')->with('success', 'Profile updated successfully!');
        } catch (NoSuchException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Required by standard CRUD but mapped to custom names in your prompt 
    public function update(UpdateUserRequest $request, $id) {}
    public function destroy($id) {}
}