<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function about()
    {
        return view('about');
    }

    public function brandingEnquiry()
    {
        return view('branding-enquiry', [
            'user' => auth()->user(),
        ]);
    }

    public function contact()
    {
        return view('contact');
    }

    public function login()
    {
        return view('login');
    }

    public function signup()
    {
        return view('signup');
    }

    public function forgotPassword()
    {
        return view('forgot-password');
    }
}
