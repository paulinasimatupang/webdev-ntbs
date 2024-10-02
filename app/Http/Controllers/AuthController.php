<?php

namespace App\Http\Controllers;

use DB;
use DateTime;
use DateInterval;
use DatePeriod;
use Validator, Hash;
use JWTAuth;
use Redirect;
use Auth;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;
use App\Http\Requests;
use Tymon\JWTAuth\Exceptions\JWTException;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\Log;

use App\Entities\Merchant;
use App\Entities\User;
use App\Entities\Role;

/**
 * Class AuthController.
 *
 * @package namespace App\Http\Controllers;
 */
class AuthController extends Controller
{

    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $check = User::where('username', $request->username)
                ->orWhere('email', $request->email)
                ->first();
            if ($check) {
                return response()->json([
                    'status' => false,
                    'error' => 'Username already used'
                ], 403);
            }

            $user = User::create([
                'role_id' => $request->role_id,
                'username' => $request->username,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Thanks for signing up.',
                'data' => $user
            ], 200);
        } catch (Exception $e) {
            // For rollback data if one data is error
            DB::rollBack();

            return response()->json([
                'status' => false,
                'error' => 'Something wrong!'
            ], 500);
        } catch (\Illuminate\Database\QueryException $ex) {
            // For rollback data if one data is error
            DB::rollBack();

            return response()->json([
                'status' => false,
                'error' => 'Something wrong!'
            ], 500);
        }
    }


    public function loginWithFingerprint(Request $request)
{
    $fingerPrint = $request->get('finger_print');
    Log::info("Fingerprint login attempt");

    if (empty($fingerPrint)) {
        Log::error("Fingerprint is missing");
        return response()->json(['status' => false, 'message' => 'Fingerprint is required'], 400);
    }

    $user = User::where('finger_print', $fingerPrint)
                ->with('user_group.group', 'merchant.terminal')
                ->first();
    
    if (!$user) {
        Log::warning("Fingerprint not recognized for fingerprint: {$fingerPrint}");
        return response()->json(['status' => false, 'message' => 'Fingerprint not recognized'], 404);
    }

    try {
        $token = JWTAuth::fromUser($user);
        Log::info("Token generated successfully for user ID: {$user->id}");
    } catch (JWTException $e) {
        Log::error("Failed to generate token for user ID: {$user->id}, Error: " . $e->getMessage());
        return response()->json(['status' => false, 'message' => 'Could not create token'], 500);
    }

    Log::info("Fingerprint login successful for user ID: {$user->id}");

    if ($request->expectsJson()) {
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => $user,
        ], 200);
    }

    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'token' => $token,
        'data' => $user,
    ], 200);
}

    



    /**
     * Simpan data fingerprint user baru
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerFingerprint(Request $request)
    {
        $fingerPrint = $request->get('finger_print');
        $id = $request->get('id');

        Log::info("Registering fingerprint for user ID: {$id}");

        if (empty($fingerPrint) || empty($id)) {
            Log::error("Fingerprint or ID missing. Fingerprint: {$fingerPrint}, ID: {$id}");
            return response()->json(['status' => false, 'message' => 'All fields are required'], 400);
        }

        // Check if the fingerprint is already registered by another user
        $existingFingerprint = User::where('finger_print', $fingerPrint)
                                    ->where('id', '!=', $id) 
                                    ->first();

        if ($existingFingerprint) {
            Log::warning("Fingerprint already registered by another user.");
            return response()->json(['status' => false, 'message' => 'Fingerprint already registered by another user'], 409);
        }

        // Check if the fingerprint is already registered for the current user
        $existingUser = User::where('id', $id)
                            ->where('finger_print', $fingerPrint)
                            ->first();

        if ($existingUser) {
            Log::warning("Fingerprint already registered for user ID: {$id}");
            return response()->json(['status' => false, 'message' => 'Fingerprint already registered for this user'], 409);
        }

        // Now find the user and register the fingerprint if not registered before
        $user = User::find($id);
        if (!$user) {
            Log::error("User with ID {$id} not found.");
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        // Set the new fingerprint
        $user->finger_print = $fingerPrint;
        if ($user->save()) {
            Log::info("Fingerprint registered successfully for user ID: {$id}");
            return response()->json(['status' => true, 'message' => 'Fingerprint registered successfully'], 200);
        } else {
            Log::error("Failed to save fingerprint for user ID: {$id}");
            return response()->json(['status' => false, 'message' => 'Failed to register fingerprint'], 500);
        }
    }


    public function login(Request $request)
    {
        $user = $request->session()->get('user');
        // return response()->json($user,200);
        if ($user) {
            return Redirect::to('landing');
        } else {
            return view('sessions.signIn');
        }
    }

    /**
     * API Login, jika berhasil mengembalikan JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function doLogin(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $messages = [
            'username.required' => 'Username harus diisi.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password harus terdiri dari minimal 8 karakter.',
            'password.max' => 'Password tidak boleh lebih dari 20 karakter.',
            'password.regex' => 'Password harus mengandung huruf kapital, huruf kecil, serta angka.',
        ];

        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->messages()->first(),
                ], 400);
            } else {
                return Redirect::to('login')
                    ->with('error', $validator->messages())
                    ->withInput();
            }
        }

        $user = User::where('username', $credentials['username'])->first();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Username Tidak Terdaftar.',
                ], 404);
            } else {
                return Redirect::to('login')
                    ->with('error', 'Username not found.')
                    ->withInput();
            }
        }

        try {
            if (!Auth::attempt($credentials)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Incorrect password.',
                    ], 401);
                } else {
                    return Redirect::to('login')
                        ->with('error', 'Incorrect password.')
                        ->withInput();
                }
            }

            $token = JWTAuth::attempt($credentials);

            // Log the token
            Log::info('Generated Token: ' . $token);
        } catch (JWTException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to login, please try again.',
                ], 500);
            } else {
                return Redirect::to('login')
                    ->with('error', 'Failed to login, please try again.')
                    ->withInput();
            }
        }

        $user = User::where('username', $credentials['username'])
            ->with('user_group.group', 'merchant.terminal')
            ->first();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'data' => $user,
            ], 200);
        } else {
            $request->session()->put('user', $user);
            $request->session()->put('merchant', $user->merchant);
            return Redirect::to('landing');
        }
    }

    // public function doLogin(Request $request)
    // {
    //     $credentials = $request->only('username', 'password');

    //     $rules = [
    //         'username' => 'required',
    //         'password' => 'required',
    //     ];

    //     $validator = Validator::make($credentials, $rules);
    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'error' => $validator->messages()], 401);
    //     }

    //     try {
    //         if (!$token = JWTAuth::attempt($credentials)) {
    //             return response()->json(['status' => false, 'error' => 'We can’t find an account with these credentials.'], 404);
    //         }
    //     } catch (JWTException $e) {
    //         return response()->json(['status' => false, 'error' => 'Failed to login, please try again.'], 500);
    //     }

    //     $user = User::where('username', $credentials['username'])
    //                 ->with('user_group.group', 'merchant.terminal')
    //                 ->first();

    //     if ($user) {
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Login successful',
    //                 'token' => $token,
    //                 'data' => $user,
    //             ], 200);
    //         } else {
    //             return Redirect::to('landing');
    //         }
    //     }

    //     return response()->json(['status' => false, 'error' => 'User not found.'], 404);
    // }

    public function changePassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $request->id)->first();
            if ($user) {
                if (Hash::check($request->old_password, $user->password)) {
                    $user->password = bcrypt($request->new_password);
                    $user->save();

                    DB::commit();

                    $response = [
                        'status' => true,
                        'message' => 'Password berhasil diubah.',
                    ];
                    return response()->json($response, 200);
                } else {
                    DB::rollBack();

                    $response = [
                        'status' => false,
                        'error' => 'Password lama salah. Mohon isi dengan password yang benar untuk mengubah password baru.',
                    ];

                    return response()->json($response, 400);
                }
            } else {
                DB::rollBack();

                $response = [
                    'status' => false,
                    'error' => 'User tidak ditemukan.',
                ];

                return response()->json($response, 404);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Data tidak ditemukan.',
                'details' => $e->getMessage()
            ];
            return response()->json($response, 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Validasi gagal.',
                'details' => $e->errors()
            ];
            return response()->json($response, 422);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Kesalahan query database.',
                'details' => $e->getMessage()
            ];
            return response()->json($response, 500);
        } catch (\PDOException $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Kesalahan koneksi database.',
                'details' => $e->getMessage()
            ];
            return response()->json($response, 500);
        } catch (Exception $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Terjadi kesalahan yang tidak terduga.',
                'exception' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function changePin(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'old_pin' => 'required|string|min:4|max:6',
                'new_pin' => 'required|string|min:4|max:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => 'Data input tidak valid.',
                    'details' => $validator->errors(),
                ], 422);
            }

            $merchant = Merchant::where('id', $request->id)->first();
            if ($merchant) {
                if ($request->old_pin == $merchant->pin) {
                    $merchant->pin = $request->new_pin;
                    $merchant->save();

                    DB::commit();

                    $response = [
                        'status' => true,
                        'message' => 'PIN berhasil diubah.',
                    ];
                    return response()->json($response, 200);
                } else {
                    DB::rollBack();

                    $response = [
                        'status' => false,
                        'error' => 'PIN lama salah. Mohon isi dengan PIN yang benar untuk mengubah PIN baru.',
                    ];

                    return response()->json($response, 400);
                }
            } else {
                DB::rollBack();

                $response = [
                    'status' => false,
                    'error' => 'Merchant tidak ditemukan.',
                ];

                return response()->json($response, 404);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Data tidak ditemukan.',
                'details' => $e->getMessage()
            ];
            return response()->json($response, 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Validasi gagal.',
                'details' => $e->errors()
            ];
            return response()->json($response, 422);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Kesalahan query database.',
                'details' => $e->getMessage()
            ];
            return response()->json($response, 500);
        } catch (\PDOException $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Kesalahan koneksi database.',
                'details' => $e->getMessage()
            ];
            return response()->json($response, 500);
        } catch (Exception $e) {
            DB::rollBack();
            $response = [
                'status' => false,
                'error' => 'Terjadi kesalahan yang tidak terduga.',
                'exception' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('user');
        return Redirect::to('login');
    }

    public function updateToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = Auth::user(); // Asumsi user sudah terautentikasi
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'FCM token successfully updated'], 200);
    }

    public function getPhoneByUsername(Request $request)
    {
        $username = $request->input('username');

        // Mencari user berdasarkan username
        $user = User::where('username', $username)->first();

        if ($user) {
            // Cari nomor telepon di tabel merchants (berdasarkan relasi user_id)
            $merchant = Merchant::where('user_id', $user->id)->first();

            if ($merchant && $merchant->phone) {
                return response()->json([
                    'status' => 'success',
                    'phone' => $merchant->phone
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Nomor telepon tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    public function resetPassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'new_password' => 'required|string',
        ]);

        // Cari user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Jika user tidak ditemukan
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username tidak ditemukan.',
            ], 404);
        }

        // Ubah password user
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diperbarui.',
        ], 200);
    }

     // Fungsi untuk menyimpan atau memperbarui FCM Token
     public function updateFCMToken(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|string',  // Pastikan user_id diterima sebagai string atau integer
        'fcm_token' => 'required|string', // FCM Token yang dikirim dari MyFirebaseMessagingService.kt
    ]);
    Log::info("Received data: user_id => " . $request->user_id . ", fcm_token => " . $request->fcm_token);


    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->messages()->first(),
        ], 400);
    }

    try {
        // Cari user berdasarkan ID yang dikirim
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Simpan atau perbarui FCM token di database
        $user->fcm_token = $request->fcm_token;
        $user->save();

        // Simpan FCM token juga di merchant jika user memiliki merchant
        if ($user->merchant) {
            $merchant = $user->merchant;
            $merchant->fcm_token = $request->fcm_token;
            $merchant->save();
        }

        Log::info("FCM token updated for user: " . $user->id);

        return response()->json([
            'status' => true,
            'message' => 'FCM token updated successfully',
        ], 200);

    } catch (\Exception $e) {
        Log::error("Failed to update FCM token: " . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'Failed to update FCM token',
        ], 500);
    }
}
}
