<?php

namespace App\Http\Controllers;

use App\Models\AiPhoto;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AiPhotoController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $latestPhoto = AiPhoto::where('user_id', $userId)
            ->where('status', 'completed')
            ->latest()
            ->first();

        $historyPhotos = AiPhoto::where('user_id', $userId)
            ->where('status', 'completed')
            ->latest()
            ->paginate(12);

        return view('user.ai_photo', compact('historyPhotos', 'latestPhoto'));
    }

    public function testUploadSession(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'tool_type' => 'required|in:restore,colorize,remove_blur,clean_damage',
        ]);

        $file = $request->file('photo');

        $path = $file->store('ai_photos/original', 'public');

        $aiPhoto = AiPhoto::create([
            'user_id' => auth()->id(),
            'tool_type' => $request->tool_type ?? 'restore',
            'original_path' => $path,
            'enhanced_path' => $path,
            'status' => 'completed',
        ]);

        return back()->with('latestPhoto', $aiPhoto)
            ->with('success', 'Test image uploaded successfully! Please API token provided to process the image.');
    }

    /**
     * Process AI Photo
     */
    public function process(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'tool_type' => 'required|in:restore,colorize,remove_blur,clean_damage',
        ]);

        try {
            $uploadedFile = $request->file('photo');

            $imageData = base64_encode(file_get_contents($uploadedFile->getRealPath()));
            $mimeType = $uploadedFile->getMimeType();
            $base64Image = 'data:'.$mimeType.';base64,'.$imageData;

            $result = $this->callReplicateApi($base64Image, $request->tool_type);

            if ($result['success']) {

                $originalPath = $uploadedFile->store('ai_photos/original', 'public');

                $enhancedImageUrl = $result['url'];
                $imageContent = Http::get($enhancedImageUrl)->body();
                $enhancedFileName = 'ai_photos/enhanced/'.uniqid().'.jpg';
                Storage::disk('public')->put($enhancedFileName, $imageContent);

                $aiPhoto = AiPhoto::create([
                    'user_id' => auth()->id(),
                    'tool_type' => $request->tool_type,
                    'original_path' => $originalPath,
                    'enhanced_path' => $enhancedFileName,
                    'status' => 'completed',
                ]);

                return back()->with('latestPhoto', $aiPhoto)->with('success', 'Photo processed successfully! You can download it now.');
            } else {
                // return back()->with('error', 'AI processing failed: '.$result['error']);
                return back()->with('error', 'Photo processing failed! Contact Admin');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'System Error: '.$e->getMessage());
        }
    }

    /**
     * Replicate API Function with Error Catching
     */
    private function callReplicateApi($base64Image, $toolType)
    {
        $setting = Setting::first();
        $apiToken = $setting?->image_process_api ?? env('IMAGE_PROCESS_API');

        if (! $apiToken) {
            return ['success' => false, 'error' => 'Photo processing failed! Contact Admin'];
        }

        $modelConfig = match ($toolType) {
            'restore' => [
                // Model: tencentarc/gfpgan (Face Restoration)
                'version' => '92832341b37d999b15872900756e0644376be135164f5a5cd4efd0a3fdef49f1',
                'input' => [
                    'img' => $base64Image,
                    'scale' => 2,
                ],
            ],
            'colorize' => [
                // Model: arielreplicate/deoldify_image (Colorize) - Updated Hash
                'version' => '0da600fab0c45a66211339f1c16b71345d22f26ef5fea3dca1bb90bb5711e950',
                'input' => [
                    'input_image' => $base64Image,
                ],
            ],
            'remove_blur' => [
                // Model: nightmareai/real-esrgan (Upscale & Blur Removal) - Updated Hash
                'version' => '42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b',
                'input' => [
                    'image' => $base64Image,
                    'face_enhance' => true,
                ],
            ],
            'clean_damage' => [
                // Model: microsoft/bringing-old-photos-back-to-life (Scratch Removal)
                'version' => 'c75dbb13271a3981a8b98b2f293b679469e5d4e13e00661a3577382229554030',
                'input' => [
                    'image' => $base64Image,
                    'with_scratch' => true,
                    'HR' => true,
                ],
            ],
        };

        $apiUrl = 'https://api.replicate.com/v1/predictions';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiToken,
            'Content-Type' => 'application/json',
            'Prefer' => 'wait',
        ])->post($apiUrl, [
            'version' => $modelConfig['version'],
            'input' => $modelConfig['input'],
        ]);

        if (! $response->successful()) {
            $errorBody = $response->json();
            $msg = $errorBody['detail'] ?? $response->body();
            $errorMessage = is_array($msg) ? json_encode($msg) : $msg;

            return ['success' => false, 'error' => "Tool [{$toolType}] failed: HTTP ".$response->status().' - '.$errorMessage];
        }

        $data = $response->json();

        if (isset($data['status']) && $data['status'] === 'succeeded') {
            $url = is_array($data['output']) ? $data['output'][0] : $data['output'];

            return ['success' => true, 'url' => $url];
        }

        $predictionUrl = $data['urls']['get'] ?? null;
        if ($predictionUrl) {
            for ($i = 0; $i < 20; $i++) {
                sleep(2);
                $checkResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiToken,
                ])->get($predictionUrl);

                $checkData = $checkResponse->json();
                $status = $checkData['status'] ?? '';

                if ($status === 'succeeded') {
                    $url = is_array($checkData['output']) ? $checkData['output'][0] : $checkData['output'];

                    return ['success' => true, 'url' => $url];
                }

                if ($status === 'failed' || $status === 'canceled') {
                    return ['success' => false, 'error' => "Tool [{$toolType}] prediction failed: ".($checkData['error'] ?? 'Unknown Error')];
                }
            }
        }

        return ['success' => false, 'error' => "Tool [{$toolType}] Request timed out."];
    }

    /**
     * Download the enhanced image
     */
    public function download($id)
    {
        $photo = AiPhoto::where('user_id', auth()->id())->findOrFail($id);

        if (! $photo->enhanced_path) {
            return back()->with('error', 'Image is not ready for download yet.');
        }

        $filePath = storage_path('app/public/'.$photo->enhanced_path);

        if (! file_exists($filePath)) {
            return back()->with('error', 'File not found on the server.');
        }

        $fileName = 'Meivoo_'.$photo->tool_type.'_'.time().'.jpg';

        return response()->download($filePath, $fileName);
    }
}
