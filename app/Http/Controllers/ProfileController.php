<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function profile(): View
    {
        return view('profile.show', ['title' => 'Profile']);
    }

    public function update_profile(Request $request): RedirectResponse
    {
        $user = User::findOrFail(Auth::id());

        // Diperbaiki: sebelumnya field Nama/Email di form cuma pakai
        // "placeholder" (teks bayangan, bukan value beneran), jadi kalau
        // user gak ngetik ulang, field-nya kekirim KOSONG dan update
        // di-skip diam-diam (kelihatan kayak gak ngapa-ngapain). Sekarang
        // field udah keisi value asli, dan validasi 'required' mastiin
        // gak ada yang kekirim kosong.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'images' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $newName = 'profile_image_' . $user->id . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $newName);
            $user->image = $newName;
        }

        $user->save();

        return redirect('/profile')->with('success', 'Profile berhasil diupdate');
    }
}
