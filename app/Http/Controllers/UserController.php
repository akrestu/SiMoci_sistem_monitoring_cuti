<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by name
        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by email
        if ($request->has('email') && !empty($request->email)) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $users = $query->paginate(10);
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('admin');
        
        return view('users.index', compact('users', 'isAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('admin');
        
        // Jika user adalah admin, tampilkan semua peran
        // Jika bukan admin, hanya tampilkan peran non-admin
        if ($isAdmin) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', '!=', 'admin')->get();
        }
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users|alpha_dash',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        // Periksa apakah user mencoba membuat user dengan peran admin, tapi dia bukan admin
        $currentUser = Auth::user();
        $adminRole = Role::where('name', 'admin')->first();
        if (!$currentUser->hasRole('admin') && $request->role == $adminRole->id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk membuat pengguna dengan peran admin.')
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);
        
        // Assign role
        $user->roles()->sync([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('admin');
        return view('users.show', compact('user', 'isAdmin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $currentUser = Auth::user();
        // Pengguna biasa hanya bisa mengedit akunnya sendiri
        if (!$currentUser->hasRole('admin') && Auth::id() != $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda hanya dapat mengedit akun Anda sendiri.');
        }

        $isAdmin = $currentUser->hasRole('admin');
        
        // Jika user adalah admin, tampilkan semua peran
        // Jika bukan admin, hanya tampilkan peran non-admin
        if ($isAdmin) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', '!=', 'admin')->get();
        }
        
        $userRole = $user->roles->first();
        
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();
        // Pengguna biasa hanya bisa mengedit akunnya sendiri
        if (!$currentUser->hasRole('admin') && Auth::id() != $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda hanya dapat mengedit akun Anda sendiri.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->getKey()),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('users')->ignore($user->getKey()),
            ],
            'role' => 'required|exists:roles,id',
        ]);

        // Periksa apakah user mencoba mengubah peran menjadi admin, tapi dia bukan admin
        $adminRole = Role::where('name', 'admin')->first();
        if (!$currentUser->hasRole('admin') && $request->role == $adminRole->id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengubah peran menjadi admin.')
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        
        // Update role
        $user->roles()->sync([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();
        // Check if current user is admin
        if (!$currentUser->hasRole('admin')) {
            return redirect()->route('users.index')
                ->with('error', 'Hanya admin yang dapat menghapus pengguna.');
        }
        
        // Prevent deleting self
        if (Auth::id() == $user->getKey()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Delete multiple users at once.
     */
    public function massDelete(Request $request)
    {
        $currentUser = Auth::user();
        // Check if current user is admin
        if (!$currentUser->hasRole('admin')) {
            return redirect()->route('users.index')
                ->with('error', 'Hanya admin yang dapat menghapus pengguna.');
        }
        
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;
        
        // Prevent deleting self
        if (in_array(Auth::id(), $userIds)) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        User::whereIn('id', $userIds)->delete();

        return redirect()->route('users.index')
            ->with('success', count($userIds) . ' pengguna berhasil dihapus.');
    }
} 