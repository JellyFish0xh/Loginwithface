<?php

namespace App\Http\Controllers;

use http\Client\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SnapshotController extends Controller
{
        public function save(Request $request): JsonResponse
        {
            // Get the base64 image data from the request
            $imageData = $request->input('image_data');

            // Convert the base64 image data to binary
            $imageBinary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

            // Generate a unique filename
            $filename = 'snapshot_' . uniqid() . '.png';

            // Save the image to the public folder
            Storage::disk('public')->put($filename, $imageBinary);

            return response()->json(['success' => true]);
        }
}
