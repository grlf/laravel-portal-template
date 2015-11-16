<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProfileController extends Controller {

    /**
     * View the currently logged in user's profile
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function viewProfile(Request $request)
    {
        return \Response::view('auth.profile.view', ['user' => $request->user()]);
    }
}