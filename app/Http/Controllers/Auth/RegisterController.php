<?php

namespace App\Http\Controllers\Auth;

use App\Concerns\StructuredLogger;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use StructuredLogger;

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'agency_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = DB::transaction(function () use ($validated) {
                $agency = Agency::create([
                    'name' => $validated['agency_name'],
                    'slug' => Str::slug($validated['agency_name']).'-'.uniqid(),
                    'email' => $validated['email'],
                    'status' => 'active',
                    'subscription_plan' => 'free',
                    'subscription_status' => 'active',
                    'is_active' => true,
                ]);

                return User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'agency_id' => $agency->id,
                    'role' => 'owner',
                    'is_active' => true,
                    'is_approved' => true,
                ]);
            });

            Auth::login($user);

            $this->logAuth('registration', [
                'user_id' => $user->id,
                'agency_id' => $user->agency_id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Welcome! Your agency has been created.');
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'email' => $validated['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }
    }
}
