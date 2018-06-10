<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function show(User $user)
    {
        return view('user.show',compact('user'));
    }
    public function edit(User $user)
    {
        return view('user.edit');
    }
}
