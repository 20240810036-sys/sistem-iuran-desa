<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tagihan\StoreTagihanRequest;
use App\Http\Requests\Tagihan\UpdateTagihanRequest;
use App\Models\Tagihan;

class TagihanController extends Controller
{
    public function index()
    {
        $tagihan = Tagihan::withCount('pembayaran')->latest()->paginate(10);
        return view('admin.tagihan.index', compact('tagihan'));
    }

    public function create()
    {
        return view('admin.tagihan.create');
    }

    public function store(StoreTagihanRequest $request)
    {
        Tagihan::create($request->validated());

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil ditambahkan.');
    }

    public function show(Tagihan $tagihan)
    {
        $tagihan->load(['pembayaran.warga']);
        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function edit(Tagihan $tagihan)
    {
        return view('admin.tagihan.edit', compact('tagihan'));
    }

    public function update(UpdateTagihanRequest $request, Tagihan $tagihan)
    {
        $tagihan->update($request->validated());

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Tagihan $tagihan)
    {
        $tagihan->delete();

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }
}
