<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->check_user_can_login()) {
            return redirect('/')->with('flash_warning', 'Your Account is not available to use!');
        } else {
            return view('home');
        }
    }

    /**
     * Check the user can login
     */
    protected function check_user_can_login()
    {
        // check the user can login
        if (! Auth::user()->can('manage-users')) {
            Auth::logout();
            return true;
        }

        return false;
    }
}
