<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Warga;
use App\Exports\PembayaranExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        return view('admin.laporan.index');
    }

    public function pembayaran(Request $request)
    {
        $query = Pembayaran::with(['warga', 'tagihan']);

        if ($request->filled('bulan')) {
            $query->whereHas('tagihan', fn($q) => $q->where('bulan', $request->bulan));
        }

        if ($request->filled('tahun')) {
            $query->whereHas('tagihan', fn($q) => $q->where('tahun', $request->tahun));
        }

        $pembayaran = $query->latest()->get();

        return view('admin.laporan.pembayaran', compact('pembayaran'));
    }

    public function tagihan(Request $request)
    {
        $query = Tagihan::withCount([
            'pembayaran as lunas_count'        => fn($q) => $q->where('status', 'lunas'),
            'pembayaran as belum_lunas_count'  => fn($q) => $q->where('status', 'belum_lunas'),
        ]);

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $tagihan = $query->orderByDesc('tahun')->orderByDesc('bulan')->get();

        return view('admin.laporan.tagihan', compact('tagihan'));
    }

    public function warga()
    {
        $warga = Warga::with('user')
            ->withCount([
                'pembayaran as lunas_count'       => fn($q) => $q->where('status', 'lunas'),
                'pembayaran as belum_lunas_count' => fn($q) => $q->where('status', 'belum_lunas'),
            ])
            ->orderBy('nama')
            ->get();

        return view('admin.laporan.warga', compact('warga'));
    }

    public function exportPdf(Request $request)
    {
        $query = Pembayaran::with(['warga', 'tagihan']);

        if ($request->filled('bulan')) {
            $query->whereHas('tagihan', fn($q) => $q->where('bulan', $request->bulan));
        }

        if ($request->filled('tahun')) {
            $query->whereHas('tagihan', fn($q) => $q->where('tahun', $request->tahun));
        }

        $pembayaran = $query->latest()->get();
        $filter     = $request->only(['bulan', 'tahun']);

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('pembayaran', 'filter'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-pembayaran-' . now()->format('Ymd') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filter = $request->only(['bulan', 'tahun']);
        return Excel::download(new PembayaranExport($filter), 'laporan-pembayaran-' . now()->format('Ymd') . '.xlsx');
    }
}
