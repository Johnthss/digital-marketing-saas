<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function dashboard()
    {
        return ['message' => 'Dashboard API endpoint — coming soon'];
    }

    public function aiGenerate()
    {
        return ['message' => 'AI generate API endpoint — coming soon'];
    }

    public function agencySettings()
    {
        return ['message' => 'Agency settings API endpoint — coming soon'];
    }

    public function updateAgencySettings()
    {
        return ['message' => 'Update settings API endpoint — coming soon'];
    }

    public function team()
    {
        return ['message' => 'Team API endpoint — coming soon'];
    }

    public function billing()
    {
        return ['message' => 'Billing API endpoint — coming soon'];
    }

    // Resource stubs
    public function index()
    {
        return ['message' => 'Index'];
    }

    public function store()
    {
        return ['message' => 'Store'];
    }

    public function show()
    {
        return ['message' => 'Show'];
    }

    public function update()
    {
        return ['message' => 'Update'];
    }

    public function destroy()
    {
        return ['message' => 'Destroy'];
    }
}
