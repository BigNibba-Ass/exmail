<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return inertia('Admin/User/Index', [
            'users' => User::all()
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

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function handleBlockAttempt(User $user)
    {
        $user->update(['is_blocked' => !$user->is_blocked]);
        return redirect()->back();
    }

    public function destroy($id)
    {
    }
}
