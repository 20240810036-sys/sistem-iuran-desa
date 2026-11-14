<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; border-bottom: 2px solid #2563EB; padding-bottom: 10px; margin-bottom: 16px; }
        .header h2 { margin: 0; font-size: 15px; color: #2563EB; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        .filter-info { margin-bottom: 12px; font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #2563EB; color: #fff;
            padding: 6px 8px; text-align: left; font-size: 10px;
        }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tfoot td { padding: 6px 8px; font-weight: bold; border-top: 2px solid #2563EB; }
        .badge-lunas { background: #d1fae5; color: #065f46; padding: 2px 6px; border-radius: 4px; }
        .badge-belum { background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; }
        .footer { margin-top: 24px; text-align: right; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pembayaran Iuran Sampah Desa</h2>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        @if(!empty($filter['bulan']) || !empty($filter['tahun']))
        <p>
            Filter:
            @if(!empty($filter['bulan']))
            Bulan {{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$filter['bulan']] }}
            @endif
            @if(!empty($filter['tahun']))
            Tahun {{ $filter['tahun'] }}
            @endif
        </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">#</th>
                <th width="22%">Nama Warga</th>
                <th width="25%">Alamat</th>
                <th width="16%">Tagihan</th>
                <th width="13%">Tgl Bayar</th>
                <th width="12%">Jumlah</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($pembayaran as $i => $p)
            @php if($p->status === 'lunas') $total += $p->jumlah_bayar; @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->warga->nama ?? '-' }}</td>
                <td>{{ $p->warga->alamat ?? '-' }}</td>
                <td>{{ $p->tagihan->label ?? '-' }}</td>
                <td>{{ $p->tanggal_bayar->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                <td>
                    @if($p->status === 'lunas')
                        <span class="badge-lunas">Lunas</span>
                    @else
                        <span class="badge-belum">Belum</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; padding:12px;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        @if($pembayaran->count())
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right;">Total Terkumpul (Lunas):</td>
                <td colspan="2">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        Sistem Informasi Pengelolaan Iuran Sampah Desa &bull; {{ now()->format('Y') }}
    </div>
</body>
</html>
