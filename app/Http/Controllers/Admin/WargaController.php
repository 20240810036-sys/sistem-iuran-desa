<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warga\StoreWargaRequest;
use App\Http\Requests\Warga\UpdateWargaRequest;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WargaController extends Controller
{
    public function index()
    {
        $warga = Warga::with('user')->latest()->paginate(10);
        return view('admin.warga.index', compact('warga'));
    }

    public function create()
    {
        return view('admin.warga.create');
    }

    public function store(StoreWargaRequest $request)
    {
        DB::transaction(function () use ($request) {
            // Buat akun User untuk warga
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'warga',
            ]);

            // Buat data warga
            Warga::create([
                'user_id' => $user->id,
                'nama'    => $request->nama,
                'alamat'  => $request->alamat,
                'no_hp'   => $request->no_hp,
            ]);
        });

        return redirect()->route('admin.warga.index')
            ->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function show(Warga $warga)
    {
        $warga->load(['pembayaran.tagihan']);
        return view('admin.warga.show', compact('warga'));
    }

    public function edit(Warga $warga)
    {
        return view('admin.warga.edit', compact('warga'));
    }

    public function update(UpdateWargaRequest $request, Warga $warga)
    {
        DB::transaction(function () use ($request, $warga) {
            $warga->update([
                'nama'   => $request->nama,
                'alamat' => $request->alamat,
                'no_hp'  => $request->no_hp,
            ]);

            // Update nama user juga
            $warga->user->update(['name' => $request->nama]);

            // Update email jika berubah
            if ($request->email !== $warga->user->email) {
                $warga->user->update(['email' => $request->email]);
            }

            // Update password jika diisi
            if ($request->filled('password')) {
                $warga->user->update(['password' => Hash::make($request->password)]);
            }
        });

        return redirect()->route('admin.warga.index')
            ->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(Warga $warga)
    {
        DB::transaction(function () use ($warga) {
            $userId = $warga->user_id;
            $warga->delete();
            User::destroy($userId);
        });

        return redirect()->route('admin.warga.index')
            ->with('success', 'Data warga berhasil dihapus.');
    }
}
