<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'IsSuperAdmin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereIn('role', [UserRoleEnum::Admin->value,UserRoleEnum::superAdmin->value])->paginate(5);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('images/users', 's3');
        }
        User::create($data);
        return redirect()->route('users.index')->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));



    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, User $user)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image) {
                Storage::disk('s3')->delete($user->image);
            }
            $data['image'] = $request->file('image')->storePublicly('images/users', 's3');
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->image) {
            Storage::disk('s3')->delete($user->image);
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Admin deleted successfully.');

    }
}
