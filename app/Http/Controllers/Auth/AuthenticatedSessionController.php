<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {

        $request->authenticate();
        $request->session()->regenerate();
        $user_id = \auth()->user()->getAuthIdentifier();
        $output = [];
        $return_var = 0;
        $imageData = $request->input('image_data');
        $imageBinary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
        $filename = uniqid() . '_' . time() . '.' . 'png';
        $publicPath = public_path('Python/images/temp/' . $filename);
        file_put_contents($publicPath, $imageBinary);
        exec('python ' . public_path('Python/main.py') . ' ' . $publicPath. ' '. $user_id, $output, $return_var);
        if ($return_var === 0) {
                if ($output[0]==='True')
                {
                    return redirect()->intended(route('dashboard', absolute: false));
                }
                else
                {
                    Auth::logout();
                    $request->session()->flash('error', 'An error occurred. Please log in again.');
                    return redirect()->intended(route('login', absolute: false));
                }
        } else {
            Auth::logout();
            return response()->json(['error' => 'Failed to execute Python script']);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
