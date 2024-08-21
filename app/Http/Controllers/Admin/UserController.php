<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchRequest;
use App\Http\Requests\Admin\Users\UserRequest;
use App\Http\Requests\Admin\Users\UserUpdateRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index(SearchRequest $request)
    {

        return inertia('Admin/User/Index', [
            'users' => User::when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })->get()
        ]);
    }

    public function create()
    {
    }

    public function store(UserRequest $request)
    {
        User::create($request->validated());
        return redirect()->back();
    }

    public function show($id)
    {
    }

    public function edit(User $user)
    {
        return inertia('Admin/User/Edit', ['user' => $user]);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->validated());
        return redirect()->route('admin.users.index');
    }

    public function handleBlockAttempt(User $user)
    {
        if($user->is_admin) return redirect()->back();
        $user->update(['is_blocked' => !$user->is_blocked]);
        return redirect()->back();
    }

    public function handleAdminAttempt(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);
        return redirect()->back();
    }

    public function destroy($id)
    {
    }
}
