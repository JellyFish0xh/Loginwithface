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
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'image_data' => ['required'], // Assuming the image data is sent as 'image_data'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Auth::login($user);
        $imageData = $request->input('image_data');

        $imageBinary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

        $filename = \auth()->user()->getAuthIdentifier() . '.' . \auth()->user()->name . '.png';

        $publicPath = public_path('Python/images/' . $filename);
        file_put_contents($publicPath, $imageBinary);
        // Fire the Registered event
        event(new Registered($user));
        Auth::logout();
        exec('python ' . public_path('Python/Simple.py') . ' ' . public_path('Python/images/'), $output, $return_var);
        if ($return_var === 0) {
            return redirect()->intended(route('login', absolute: false));
        }else{
            $request->session()->flash('error', 'No Face Detected Please Try Again.');
            return redirect()->intended(route('register', absolute: false));
        }

    }

}
