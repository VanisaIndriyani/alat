@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2"><i class='bx bx-file'></i> Laporan & Rekapitulasi</div>
    <div class="d-flex gap-2">
        <a class="btn btn-success" href="{{ route('reports.export', ['month'=>$month,'start'=>$start,'end'=>$end,'status'=>$status]) }}"><i class='bx bx-spreadsheet'></i> Export Excel</a>
        <a class="btn btn-primary" href="{{ route('reports.print', ['month'=>$month,'start'=>$start,'end'=>$end,'status'=>$status]) }}" target="_blank"><i class='bx bx-printer'></i> Cetak PDF</a>
    </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('reports') }}" class="row g-3 align-items-center">
      <div class="col-12">
        <div class="d-flex align-items-center gap-2 text-muted mb-2"><i class='bx bx-calendar'></i> Pilih Bulan (Otomatis set tanggal)</div>
      </div>
      <div class="col-12 col-md-3">
        <input type="month" class="form-control" name="month" value="{{ $month }}">
      </div>
      <div class="col-12 col-md-3">
        <input type="date" class="form-control" name="start" value="{{ $start }}" placeholder="Tanggal Awal">
      </div>
      <div class="col-12 col-md-3">
        <input type="date" class="form-control" name="end" value="{{ $end }}" placeholder="Tanggal Akhir">
      </div>
      <div class="col-12 col-md-2">
        <select name="status" class="form-select">
          <option value="all" {{ $status==='all'?'selected':'' }}>Semua Status</option>
          <option value="active" {{ $status==='active'?'selected':'' }}>Aktif</option>
          <option value="returned" {{ $status==='returned'?'selected':'' }}>Selesai</option>
        </select>
      </div>
      <div class="col-12 col-md-auto d-flex gap-2 justify-content-md-end align-items-center">
        <button class="btn btn-outline-secondary" type="submit"><i class='bx bx-filter'></i></button>
        <a class="btn btn-outline-danger" href="{{ route('reports') }}" title="Reset"><i class='bx bx-refresh'></i></a>
      </div>
    </form>
  </div>
</div>

<div class="table-responsive">
  <table class="table align-middle table-hover">
    <thead>
      <tr>
        <th>NO TRANSAKSI</th>
        <th>TGL PINJAM</th>
        <th>PEMINJAM</th>
        <th>KEPERLUAN</th>
        <th>JML ALAT</th>
        <th>STATUS</th>
        <th>DENDA</th>
      </tr>
    </thead>
    <tbody>
      @forelse($loans as $l)
        <tr>
          <td>{{ 'TRX-'.\Illuminate\Support\Carbon::parse($l->borrowed_at)->format('Ymd').'-'.str_pad($l->id,3,'0',STR_PAD_LEFT) }}</td>
          <td>{{ \Illuminate\Support\Carbon::parse($l->borrowed_at)->format('d/m/Y') }}</td>
          <td>{{ $l->student_name }}<div class="text-muted small">{{ $l->student_nis }}</div></td>
          <td>{{ Str::limit($l->purpose, 28) }}</td>
          <td>1 Item</td>
          <td>
            @if($l->status==='returned')<span class="badge bg-success">Selesai</span>@else<span class="badge bg-warning text-dark">Aktif</span>@endif
          </td>
          <td class="text-danger">Rp {{ number_format($l->fine_amount,0,',','.') }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-muted">Tidak ada data.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="row g-3 mt-2">
  <div class="col-12 col-md-3">
    <div class="card shadow-sm"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="text-muted small">Total Transaksi</div><div class="fs-5 fw-semibold">{{ $loans->count() }}</div></div><i class='bx bx-list-ul fs-2 text-secondary'></i></div></div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card shadow-sm"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="text-muted small">Aktif</div><div class="fs-5 fw-semibold">{{ $loans->where('status','active')->count() }}</div></div><i class='bx bx-time-five fs-2 text-warning'></i></div></div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card shadow-sm"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="text-muted small">Selesai</div><div class="fs-5 fw-semibold">{{ $loans->where('status','returned')->count() }}</div></div><i class='bx bx-check-circle fs-2 text-success'></i></div></div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card shadow-sm"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="text-muted small">Total Denda</div><div class="fs-5 fw-semibold text-danger">Rp {{ number_format($loans->sum('fine_amount'),0,',','.') }}</div></div><i class='bx bx-dollar-circle fs-2 text-danger'></i></div></div>
  </div>
</div>
@endsection