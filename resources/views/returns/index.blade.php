@extends('layouts.app')

@section('content')
<h5 class="mb-3">Pengembalian Alat</h5>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="text-center text-muted mb-2">Scan QR Code atau ketik ID Alat (misal: TL-002)</div>
        <form class="input-group" method="POST" action="{{ route('returns.check') }}">
            @csrf
            <span class="input-group-text"><i class='bx bx-barcode'></i></span>
            <input type="text" name="query" class="form-control" placeholder="Scan QR Code atau ketik ID Alat (misal: TL-002)..." value="{{ old('query') }}">
            <button class="btn btn-primary"><i class='bx bx-search'></i> Cek Status</button>
        </form>
        @error('query')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
        @if(session('info'))<div class="alert alert-info mt-2">{{ session('info') }}</div>@endif
    </div>
    </div>

@isset($loan)
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-2"><i class='bx bx-info-circle'></i> Status Peminjaman</div>
        <div class="row g-3">
            <div class="col-12 col-md-6"><strong>Alat</strong><div>{{ $equipment->name }} <span class="text-muted">({{ $equipment->code }})</span></div></div>
            <div class="col-12 col-md-6"><strong>Peminjam</strong><div>{{ $loan->student_name }} / {{ $loan->student_nis }}</div></div>
            <div class="col-12 col-md-6"><strong>Tgl Pinjam</strong><div>{{ \Illuminate\Support\Carbon::parse($loan->borrowed_at)->format('d/m/Y') }}</div></div>
            <div class="col-12 col-md-6"><strong>Rencana Kembali</strong><div>{{ \Illuminate\Support\Carbon::parse($loan->planned_return_at)->format('d/m/Y') }}</div></div>
            <div class="col-12 col-md-6"><strong>Telat</strong><div>@if($daysLate>0)<span class="badge bg-danger">{{ $daysLate }} Hari</span>@else <span class="badge bg-success">Tidak Telat</span>@endif</div></div>
            <div class="col-12 col-md-6"><strong>Denda</strong><div>Rp {{ number_format($fine,0,',','.') }}</div></div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-2"><i class='bx bx-check-shield'></i> Proses Pengembalian</div>
        <form method="POST" action="{{ route('returns.process') }}" class="row g-3">
            @csrf
            <input type="hidden" name="loan_id" value="{{ $loan->id }}">
            <div class="col-12">
                <label class="form-label">Kondisi Alat</label>
                <div class="d-flex gap-3">
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="condition" value="good" checked>
                        <span class="form-check-label">Baik</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="condition" value="minor_damage">
                        <span class="form-check-label">Rusak Ringan (denda admin)</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="condition" value="total_damage">
                        <span class="form-check-label">Rusak Total (ganti alat)</span>
                    </label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Denda Tambahan (opsional)</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input class="form-control" type="number" name="additional_fine" placeholder="0">
                </div>
                <div class="form-text">Jika telat, denda dasar otomatis Rp 1.000/hari.</div>
            </div>
            <div class="col-12">
                <button class="btn btn-success"><i class='bx bx-check-circle'></i> Selesaikan Pengembalian</button>
            </div>
        </form>
    </div>
</div>
@endisset

@if(session('return_success'))
@php($s = session('return_success'))
<div class="card border-success mt-3">
    <div class="card-body bg-success bg-opacity-10">
        <div class="d-flex align-items-center gap-2 mb-2"><i class='bx bx-check-circle text-success fs-4'></i> <strong>Pengembalian Berhasil!</strong></div>
        <div class="text-muted mb-3">Stok alat telah diperbarui.</div>
        <div class="d-flex gap-3">
            <span class="badge bg-light text-danger border"><i class='bx bx-time-five me-1'></i> Keterlambatan {{ $s['daysLate'] }} Hari</span>
            <span class="badge bg-light text-success border"><i class='bx bx-dollar-circle me-1'></i> Total Denda Rp {{ number_format($s['totalFine'],0,',','.') }}</span>
        </div>
    </div>
</div>
@endif
@endsection