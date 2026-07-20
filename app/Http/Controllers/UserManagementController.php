<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;


class UserManagementController extends Controller
{
   public function index(): View
    {
        // Ambil data user aktif (yang tidak di-soft delete)
        $activeUsers = User::withoutTrashed()
            ->with('roles')
            ->orderBy('name')
            ->paginate(10, ['*'], 'active_page');

        // Ambil data user nonaktif (yang sudah di-soft delete / trashed)
        $inactiveUsers = User::onlyTrashed()
            ->with('roles')
            ->orderBy('name')
            ->paginate(10, ['*'], 'inactive_page');

        return view('user-management.index', [
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'title' => 'Pengaturan',
        ]);
    }

    public function create(): View
    {
        $orgCodes = \App\Models\SalesRecord::query()->select('ORG_CODE')->distinct()->orderBy('ORG_CODE')->pluck('ORG_CODE');

        return view('user-management.create', [
            'orgCodes' => $orgCodes,
            'title' => 'Kelola User',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255', 'regex:/^[A-Z][a-zA-Z]*(\s[A-Z][a-zA-Z]*)*$/'],
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'role' => 'required|in:admin,user',
            'org_code' => 'required_if:role,user|nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'org_code' => $validated['role'] === 'user' ? $validated['org_code'] : null,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('user-management.index')->with('success', "Akun {$user->name} berhasil dibuat sebagai " . ($validated['role'] === 'admin' ? 'Admin' : 'Karyawan'));
    }

    /**
     * Khusus buat nge-set/ganti ORG_CODE user yang udah ada (penting buat
     * akun lama yang dibuat sebelum fitur ORG_CODE ada).
     */
    public function updateOrgCode(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'org_code' => 'nullable|string|max:255',
        ]);

        $user->update(['org_code' => $validated['org_code']]);

        return redirect()->route('user-management.index')->with('success', "ORG_CODE {$user->name} berhasil diupdate");
    }

    /**
     * Nonaktifkan akun (soft delete) — buat karyawan resign. Gak bisa login
     * lagi, tapi riwayat log-nya tetap aman/nyambung ke nama mereka.
     */
    public function deactivate(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('user-management.index')->with('error', 'Gak bisa nonaktifin akun sendiri');
        }

        $user->delete(); // soft delete

        return redirect()->route('user-management.index')->with('success', "Akun {$user->name} berhasil dinonaktifkan");
    }

    public function reactivate(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('user-management.index')->with('success', "Akun {$user->name} berhasil diaktifkan lagi");
    }
}
