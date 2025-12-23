<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\UserEditRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function create()
    {
        $authUser = Auth::user();

        if ($authUser->active === 'A') {
            return redirect()->back();
        } 
        if ($authUser->type === 'N') {
            return redirect()->back();
        }
        return Inertia::render('Users/Create');
    }

    public function register(AuthRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'cpf' => $request->cpf,
            'active' === 'S'
        ]);
    }

    public function index()
    {
        $authUser = Auth::user();
        if ($authUser->active === 'N') {
            return redirect()->back();
        }
        
        $userType = auth()->user()->type;
        if ($userType === 'A') {
            return redirect()->back();
        } 
        return Inertia::render('Users/Index', [
            'users' => User::all(),
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        $authUser = Auth::user();

        if ($authUser->active === 'N') {
            return redirect()->back();
        }

        if ($authUser->type === 'A') {
            return redirect()->back();
        }

        return Inertia::render('Users/Show', [
            'user' => $user,
        ]);
    }

    public function update(UserEditRequest $request, $id)
{
    $user = User::findOrFail($id);

    if ($user->type === 'A') {
        return redirect()->back();
    }

    $user->update([
        'name' => $request->name,
        'type' => $request->type,
        'active' => $request->active
    ]);
}
}
