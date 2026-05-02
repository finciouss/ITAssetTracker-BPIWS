<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Public self-registration is disabled.
     * New accounts are created by an Admin via /users/create.
     */
    public function showRegistrationForm()
    {
        abort(404);
    }

    public function register(Request $request)
    {
        abort(404);
    }
}
