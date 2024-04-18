<x-guest-layout>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form id="login-form" method="POST" action="{{secure_url(route('login'))  }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>
        <!-- Webcam Preview -->
        <div class="mt-4">
            <video id="webcam-preview" autoplay playsinline></video>
        </div>

        <!-- Hidden Input for Image Data -->
        <input type="hidden" id="image-data" name="image_data">

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-4">
            <button style="background-color: white;border-radius: 50px; width: 200px ;height: 50px" class="ms-4" type="button" id="register-and-save-snapshot">Login</button>
        </div>

        <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('register') }}">
                    {{ __('New Account') }}
                </a>
        </div>
    </form>
</x-guest-layout>
<script>
    // JavaScript to access webcam and display preview
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function (stream) {
            var video = document.getElementById('webcam-preview');
            video.srcObject = stream;
            video.play();
        })
        .catch(function (err) {
            console.error('Error accessing webcam: ', err);
        });

    document.getElementById('register-and-save-snapshot').addEventListener('click', function () {
        var video = document.getElementById('webcam-preview');
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert canvas to base64 image data
        var imageData = canvas.toDataURL('image/png');

        // Set the base64 image data in the hidden input field
        document.getElementById('image-data').value = imageData;

        // Submit the registration form
        document.getElementById('login-form').submit();
    });
</script>
