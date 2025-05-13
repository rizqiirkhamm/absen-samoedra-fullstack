<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showSettingsForm()
    {
        return view('admin.account-settings');
    }

    public function updateName(Request $request)
    {
        $request->validate([
            'new_name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->name = $request->new_name;
        $user->save();

        return back()->with('success', 'Nama berhasil diperbarui!');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'new_email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'new_email_confirmation' => ['required', 'same:new_email'],
        ], [
            'new_email.unique' => 'Email ini sudah digunakan oleh user lain.',
            'new_email.email' => 'Format email tidak valid.',
            'new_email_confirmation.same' => 'Konfirmasi email tidak cocok.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            $user->email = $request->new_email;
            $user->save();

            return redirect()->back()->with('success', 'Email berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui email. Silakan coba lagi.');
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ], [
            'current_password.required' => 'Password saat ini harus diisi.',
            'new_password.required' => 'Password baru harus diisi.',
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password_confirmation.same' => 'Konfirmasi password baru tidak cocok.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->back()->with('success', 'Password berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui password. Silakan coba lagi.');
        }
    }
}