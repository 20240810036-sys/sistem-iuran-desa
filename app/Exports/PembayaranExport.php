<?php

namespace App\Exports;

use App\Models\Pembayaran;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembayaranExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected array $filter;
    protected int $rowNumber = 0;

    public function __construct(array $filter = [])
    {
        $this->filter = $filter;
    }

    public function title(): string
    {
        return 'Laporan Pembayaran';
    }

    public function query()
    {
        $query = Pembayaran::with(['warga', 'tagihan'])->latest();

        if (! empty($this->filter['bulan'])) {
            $query->whereHas('tagihan', fn($q) => $q->where('bulan', $this->filter['bulan']));
        }

        if (! empty($this->filter['tahun'])) {
            $query->whereHas('tagihan', fn($q) => $q->where('tahun', $this->filter['tahun']));
        }

        return $query;
    }

    public function headings(): array
    {
        return ['No', 'Nama Warga', 'Alamat', 'Tagihan', 'Tanggal Bayar', 'Jumlah Bayar', 'Status'];
    }

    public function map($pembayaran): array
    {
        $bulanArr = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $this->rowNumber++;
        return [
            $this->rowNumber,
            $pembayaran->warga->nama ?? '-',
            $pembayaran->warga->alamat ?? '-',
            ($bulanArr[$pembayaran->tagihan->bulan] ?? '-') . ' ' . ($pembayaran->tagihan->tahun ?? ''),
            $pembayaran->tanggal_bayar->format('d/m/Y'),
            'Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.'),
            $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Lunas',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']], 'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]],
        ];
    }
}
