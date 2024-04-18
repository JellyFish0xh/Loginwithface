<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\FaceFeature;
use App\Models\User;
use Flasher\Prime\Notification\NotificationInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'image_data' => ['required', 'string'], // Ensure image data is provided
        ]);


        if ($validator->fails()) {
            return redirect()->route('register')->withErrors($validator)->withInput();
        }

        $imageData = $request->input('image_data');
        $imageBinary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

        // Validate image data
        if (!$imageBinary) {
            toastr('Invalid image data provided.',NotificationInterface::ERROR);
            return redirect()->route('register');
        }

        $filename = uniqid() . '.png';
        $publicPath = public_path('Python/images/' . $filename);
        file_put_contents($publicPath, $imageBinary);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        exec('python ' . public_path('Python/Simple.py') . ' ' . $publicPath. ' '. $user->id, $output, $return_var);
        event(new Registered($user));



        if ($return_var !== 0) {
            toastr('An error occurred while processing the image.',NotificationInterface::ERROR);
            return redirect()->route('register');
        }

        if ($output == "Camera Not Working !") {
            toastr('Please allow camera access.',NotificationInterface::INFO);
            return redirect()->route('register');
        }

        // Assuming $output contains face encoding string


        toastr('Registration successful. Please log in.',NotificationInterface::SUCCESS);
        return redirect()->route('login');
    }

}
