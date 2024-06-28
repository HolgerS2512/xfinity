<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class UserController extends Controller
{
    public function profile()
    {
        return Auth::user();
    }
}