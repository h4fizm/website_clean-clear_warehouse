<?php

namespace App\Http\Controllers;

// --- KUMPULKAN SEMUA IMPORT DI ATAS ---
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;      // PENAMBAHAN: Untuk logging error
use Illuminate\Validation\Rule;          // PERBAIKAN: Import class 'Rule' yang hilang
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;   // PENAMBAHAN: Type hint untuk redirect
use Illuminate\Http\JsonResponse;       // PENAMBAHAN: Type hint untuk JSON response
use Illuminate\View\View;                 // PENAMBAHAN: Type hint untuk view

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function index(): View
    {
        return view('dashboard_page.auth.login');
    }

    /**
     * Menangani proses autentikasi.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'], // Cukup 'email', ini sudah termasuk validasi format (rfc)
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan halaman form registrasi.
     */
    public function register(): View
    {
        // Logika ini sudah baik, tidak perlu diubah
        $roles = Role::whereNotIn('name', ['Manager'])->get();
        return view('dashboard_page.auth.register', compact('roles'));
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $user->assignRole($validatedData['role']);

        } catch (\Exception $e) {
            Log::error('Gagal registrasi user: ' . $e->getMessage()); // PENAMBAHAN: Log error jika gagal

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat registrasi.'], 500);
            }
            return back()->with('error', 'Terjadi kesalahan pada server. Coba lagi nanti.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil! Anda akan diarahkan ke halaman login.'
            ]);
        }

        return redirect('/login')->with('success', 'Registrasi berhasil! Silahkan login.');
    }

    /**
     * Menampilkan halaman profil pengguna.
     */
    public function showProfile(): View
    {
        $user = Auth::user();
        $roles = Role::all();
        return view('dashboard_page.auth.profil', compact('user', 'roles'));
    }

    /**
     * Memperbarui profil pengguna.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        // --- VALIDASI DENGAN PESAN CUSTOM ---
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email', // Dihapus ':rfc,dns' untuk menghindari masalah di lokal
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'role' => 'required|exists:roles,name',
                'password' => 'nullable|string|min:8|confirmed',
            ],
            [
                // --- KUMPULAN PESAN ERROR SPESIFIK ---
                'name.required' => 'Nama pengguna tidak boleh kosong.',
                'email.required' => 'Email tidak boleh kosong.',
                'email.email' => 'Format email yang Anda masukkan tidak valid.',
                'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
                'role.required' => 'Anda harus memilih role.',
                'password.min' => 'Password baru minimal harus 8 karakter.',
                'password.confirmed' => 'Password dan Konfirmasi Password tidak cocok.',
            ]
        );

        try {
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            // Hanya update password jika field password diisi
            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->save();
            $user->syncRoles($validatedData['role']);

        } catch (\Exception $e) {
            Log::error('Gagal update profil user: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat memperbarui profil.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!'
        ]);
    }

    /**
     * Menangani proses logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}