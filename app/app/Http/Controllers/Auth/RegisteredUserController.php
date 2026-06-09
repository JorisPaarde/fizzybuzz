<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'in:restaurant,cafe,hotel,catering,other'],
            'region' => ['required', 'string', 'max:255'],
            'purchase_size' => ['required', 'in:small,medium,large'],
            'employees_count' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'privacy_acknowledged' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'region' => $request->region,
            'purchase_size' => $request->purchase_size,
            'employees_count' => $request->employees_count,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
