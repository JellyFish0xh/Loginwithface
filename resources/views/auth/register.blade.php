
<x-guest-layout>
    <form id="registration-form" method="POST" action="{{ secure_url(route('register')) }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Webcam Preview -->
        <div class="mt-4">
            <video id="webcam-preview" autoplay playsinline></video>
        </div>

        <!-- Hidden Input for Image Data -->
        <input type="hidden" id="image-data" name="image_data">

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-4">
            <button style="background-color: white;border-radius: 50px; width: 200px ;height: 50px" class="ms-4" type="button" id="register-and-save-snapshot">Register</button>
        </div>
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Login') }}
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

    // JavaScript to capture snapshot and submit form when button is clicked
    document.getElementById('register-and-save-snapshot').addEventListener('click', function () {
        var video = document.getElementById('webcam-preview');
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');

        // Set canvas dimensions to match video dimensions
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw video frame on canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert canvas to base64 image data
        var imageData = canvas.toDataURL('image/png');

        // Set the base64 image data in the hidden input field
        document.getElementById('image-data').value = imageData;

        // Submit the registration form
        document.getElementById('registration-form').submit();
    });
</script>
