<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cetak Laporan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>@media print { .no-print{display:none} }</style>
</head>
<body class="p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-3">
      <img src="{{ asset('img/logo.png') }}" alt="Logo" width="48" height="48" class="rounded-circle">
      <div>
        <div class="fw-semibold">SMK Nasional Dawarblandong Mojokerto</div>
        <div class="text-muted small">Sistem Peminjaman Alat</div>
      </div>
    </div>
    <button class="btn btn-primary no-print" onclick="window.print()">Print</button>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="border rounded p-2"><div class="text-muted small">Total Transaksi</div><div class="fw-semibold">{{ count($loans) }}</div></div></div>
    <div class="col-6 col-md-3"><div class="border rounded p-2"><div class="text-muted small">Aktif</div><div class="fw-semibold">{{ collect($loans)->where('status','active')->count() }}</div></div></div>
    <div class="col-6 col-md-3"><div class="border rounded p-2"><div class="text-muted small">Selesai</div><div class="fw-semibold">{{ collect($loans)->where('status','returned')->count() }}</div></div></div>
    <div class="col-6 col-md-3"><div class="border rounded p-2"><div class="text-muted small">Total Denda</div><div class="fw-semibold text-danger">Rp {{ number_format(collect($loans)->sum('fine_amount'),0,',','.') }}</div></div></div>
  </div>

  <div class="table-responsive">
    <table class="table table-sm table-bordered">
      <thead class="table-light"><tr><th>NO TRANSAKSI</th><th>TGL PINJAM</th><th>PEMINJAM</th><th>KEPERLUAN</th><th>ALAT</th><th>STATUS</th><th>DENDA</th></tr></thead>
      <tbody>
        @foreach($loans as $l)
          <tr>
            <td>{{ 'TRX-'.\Illuminate\Support\Carbon::parse($l->borrowed_at)->format('Ymd').'-'.str_pad($l->id,3,'0',STR_PAD_LEFT) }}</td>
            <td>{{ \Illuminate\Support\Carbon::parse($l->borrowed_at)->format('d/m/Y') }}</td>
            <td>{{ $l->student_name }} ({{ $l->student_nis }})</td>
            <td>{{ $l->purpose }}</td>
            <td>{{ $l->equipment?->name }}</td>
            <td>{{ $l->status==='returned'?'Selesai':'Aktif' }}</td>
            <td>Rp {{ number_format($l->fine_amount,0,',','.') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4 d-flex justify-content-end">
    <div class="text-center" style="width:220px">
      <div class="mb-5">Petugas</div>
      <div class="border-top pt-2">________________________</div>
    </div>
  </div>
</body>
</html>