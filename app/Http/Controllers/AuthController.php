<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $auth;

    // Inject Firebase Auth dari Library Kreait
    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    // --- REGISTER ---
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed', // Pastikan ada input name="password_confirmation" di view
            'name' => 'required'
        ]);

        try {
            // 1. Buat User di Firebase Authentication
            $userProperties = [
                'email' => $request->email,
                'emailVerified' => false,
                'password' => $request->password,
                'displayName' => $request->name,
            ];
            
            $createdUser = $this->auth->createUser($userProperties);

            // 2. Simpan juga di Database Lokal (SQLite) agar fitur History Laravel tetap jalan
            // Kita gunakan 'updateOrCreate' agar tidak error kalau user sudah ada
            $localUser = User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'password' => Hash::make($request->password), // Password lokal hanya formalitas
                    'email_verified_at' => now()
                ]
            );

            // 3. Login otomatis ke Laravel
            Auth::login($localUser);

            return redirect()->route('home');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Firebase Register Error: ' . $e->getMessage()]);
        }
    }

    // --- LOGIN ---
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // 1. Cek Password ke Firebase menggunakan REST API (karena Admin SDK tidak bisa login user biasa)
            $apiKey = env('FIREBASE_API_KEY');
            $response = Http::post("https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key={$apiKey}", [
                'email' => $request->email,
                'password' => $request->password,
                'returnSecureToken' => true,
            ]);

            if ($response->failed()) {
                return back()->withErrors(['email' => 'Email atau Password salah (Cek Firebase).']);
            }

            // 2. Jika Firebase bilang OK, cari user di Database Lokal
            $localUser = User::where('email', $request->email)->first();

            // Jika di lokal belum ada (misal user dibuat manual di Console), kita buatkan otomatis
            if (!$localUser) {
                $localUser = User::create([
                    'email' => $request->email,
                    'name' => 'User Firebase', // Nama default
                    'password' => Hash::make($request->password)
                ]);
            }

            // 3. Login ke Sesi Laravel
            Auth::login($localUser);

            return redirect()->route('home');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Login Error: ' . $e->getMessage()]);
        }
    }

    // --- LOGOUT ---
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}