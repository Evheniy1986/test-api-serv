<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserShowRequest;
use App\Http\Resources\UserPaginatedCollection;
use App\Http\Resources\UserResource;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Tinify\Exception;
use Tinify\Tinify;

class UserController extends Controller
{
    public function index(UserIndexRequest $request)
    {
        $page = $request->query('page', 1);
        $count = $request->query('count', 6);

        $users = User::query()->paginate(intval($count))->withQueryString();

        if (!$users || $page > $users->lastPage()) {
            return response()->json(['success' => false, 'message' => 'Page not found'], 404);
        }

          return new UserPaginatedCollection($users);
    }


    public function show(UserShowRequest $request, $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
        ]);
    }


    public function edit(Request $request)
    {
        return view('store');
    }

    public function registerUser(UserRegisterRequest $request)
    {
        $token = $request->header('X-token');

        $storedToken = Token::query()->where('token', $token)->first();

        if (!$storedToken || $storedToken->expires_at < Carbon::now()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }


        $data = $request->validated();

        if (User::where('email', $data['email'])->orWhere('phone', $data['phone'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User with this phone or email already exists',
            ], 409);
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->processImage($request->file('photo'));
        }

        try {
            $storedToken->delete();
            $user = User::create($data);

            return response()->json([
                'success' => true,
                'message' => 'New user successfully registered',
                'user_id' => $user->id,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while registering the user: ' . $e->getMessage(),
            ], 500);
        }
    }


    private function processImage($image)
    {
        $apiKey = config('api.tiny_png_api_key');
        $name = pathinfo($image->hashName(), PATHINFO_FILENAME);

        try {
            $croppedImage = Image::read($image)->resize(70, 70)->toJpeg();
            $croppedPath = storage_path('app/public/users/cropped_' . $name . '.jpg');
            $croppedImage->save($croppedPath);

            $mimeType = mime_content_type($croppedPath);

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode('api:' . $apiKey),
                    'Content-Type' => $mimeType,
                ])
                ->send('POST', 'https://api.tinify.com/shrink', [
                    'body' => file_get_contents($croppedPath),
                ]);


            if ($response->failed()) {
                $errorMessage = $response->json()['message'] ?? 'Unknown error';
                throw new \Exception('Error from TinyPNG API: ' . $errorMessage);
            }

            $compressedImageUrl = $response->json()['output']['url'];

            $compressedImage = Http::withoutVerifying()->get($compressedImageUrl)->body();

            $optimizedPath = 'users/' . $name . '.jpg';
            Storage::disk('public')->put($optimizedPath, $compressedImage);

//            Tinify::setKey($apiKey);
//            $source = \Tinify\fromFile($croppedPath);
//            $optimizedPath = storage_path('app/public/users/' . $name . '.jpg');
//            $source->toFile($optimizedPath);

            Storage::disk('public')->delete('users/cropped_' . $name . '.jpg');

            return $optimizedPath;
        } catch (\Tinify\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
