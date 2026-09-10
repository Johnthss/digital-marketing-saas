<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    /**
     * Public landing page.
     */
    public function landing()
    {
        return view('public.landing');
    }

    /**
     * Pricing page.
     */
    public function pricing()
    {
        return view('public.pricing');
    }

    /**
     * Features page.
     */
    public function features()
    {
        return view('public.features');
    }

    /**
     * Documentation page.
     */
    public function docs()
    {
        return view('public.docs');
    }

    /**
     * Blog page.
     */
    public function blog()
    {
        return view('public.blog');
    }

    /**
     * Contact page.
     */
    public function contact()
    {
        return view('public.contact');
    }
}
