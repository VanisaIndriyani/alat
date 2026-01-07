@extends('layouts.app')

@section('content')
@php($setting = \App\Models\AppSetting::first())
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 class="h4 fw-semibold mb-0">{{ $setting?->school_name ?? 'Dashboard' }}</h1>
        <div class="text-muted small">Sistem Peminjaman Alat — {{ $setting?->department_name ?? 'Bengkel' }}</div>
    </div>
    <div class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</div>
</div>

@if($lateLoans->count())
<div class="alert alert-danger border-0">
    <div class="d-flex align-items-center gap-2 mb-2"><i class='bx bxs-error-circle'></i><strong>Peringatan Keterlambatan ({{ $lateLoans->count() }})</strong></div>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th>Siswa / NIS</th>
                    <th>Rencana Kembali</th>
                    <th>Telat</th>
                    <th>Denda</th>
                </tr>
            </thead>
            <tbody>
            @foreach($lateLoans as $l)
                <tr>
                    <td>{{ $l['student_name'] }}<div class="text-muted small">{{ $l['student_nis'] }}</div></td>
                    <td>{{ \Illuminate\Support\Carbon::parse($l['planned_return_at'])->format('d/m/Y') }}</td>
                    <td><span class="badge bg-danger-subtle text-danger">{{ $l['days_late'] }} Hari</span></td>
                    <td>Rp {{ number_format($l['fine'],0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="row g-3">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small">Total Alat</div>
                    <div class="fs-4 fw-bold">{{ $total }}</div>
                </div>
                <i class='bx bx-wrench fs-1 text-secondary'></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small">Dipinjam</div>
                    <div class="fs-4 fw-bold">{{ $loaned }}</div>
                </div>
                <i class='bx bx-cart-alt fs-1 text-warning'></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small">Tersedia</div>
                    <div class="fs-4 fw-bold">{{ $available }}</div>
                </div>
                <i class='bx bx-check-circle fs-1 text-success'></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small">Rusak</div>
                    <div class="fs-4 fw-bold">{{ $damaged }}</div>
                </div>
                <i class='bx bx-error fs-1 text-danger'></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card border-0 bg-light">
            <div class="card-body d-flex gap-2 flex-wrap">
                <a class="btn btn-outline-primary" href="{{ route('loans') }}"><i class='bx bx-calendar me-1'></i> Peminjaman</a>
                <a class="btn btn-outline-secondary" href="{{ route('returns') }}"><i class='bx bx-undo me-1'></i> Pengembalian</a>
                <a class="btn btn-outline-success" href="{{ route('equipment') }}"><i class='bx bx-wrench me-1'></i> Data Peralatan</a>
                <a class="btn btn-outline-dark" href="{{ route('reports') }}"><i class='bx bx-file me-1'></i> Laporan</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2"><i class='bx bx-line-chart'></i> Trafik Peminjaman</div>
                <div class="d-flex gap-2 mb-3">
                    <button id="btnWeekly" class="badge bg-secondary border-0">Mingguan</button>
                    <button id="btnMonthly" class="badge bg-light text-secondary border">Bulanan</button>
                </div>
             <div style="height:260px;">
    <canvas id="loanChart"></canvas>
</div>

                <div class="text-muted small mt-2">Statistik jumlah transaksi peminjaman.</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-success">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2"><i class='bx bx-dollar-circle text-success fs-5'></i> Keuangan Denda</div>
                <div class="display-6 fw-semibold text-success">Rp {{ number_format($fineTotal,0,',','.') }}</div>
                <div class="text-muted small mt-2">Total akumulasi masuk. Denda diterapkan Rp 1.000/hari keterlambatan per alat.</div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2"><i class='bx bx-time-five'></i> Riwayat Terakhir</div>
                    <span class="badge bg-light text-secondary">{{ $recentLoans->count() }} Data</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Transaksi</th>
                                <th>Siswa</th>
                                <th>Alat</th>
                                <th>Keperluan</th>
                                <th>Tgl Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLoans as $loan)
                                <tr>
                                    <td><a href="#">{{ 'TRX-' . now()->format('Ymd') . '-' . str_pad($loan->id,3,'0',STR_PAD_LEFT) }}</a></td>
                                    <td>{{ $loan->student_name }}</td>
                                    <td>{{ $loan->equipment?->name }}</td>
                                    <td>{{ $loan->purpose }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($loan->borrowed_at)->format('d M Y') }}</td>
                                    <td>
                                        @if(is_null($loan->returned_at))
                                            <span class="badge bg-warning text-dark">Aktif</span>
                                        @else
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const weekly = {!! json_encode($chartWeek) !!};
    const monthly = {!! json_encode($chartMonth) !!};
    const ctx = document.getElementById('loanChart').getContext('2d');
    let current = 'week';
    let chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: weekly.labels,
            datasets: [{
                label: 'Peminjaman',
                data: weekly.series,
                borderColor: '#0f4aa6',
                backgroundColor: 'rgba(15,74,166,.1)',
                tension: .3,
                fill: true
            }]
        },
        options: {responsive: true, maintainAspectRatio: false, scales: {y: {beginAtZero: true}}}
    });
    document.getElementById('btnWeekly').addEventListener('click', () => {
        chart.data.labels = weekly.labels; chart.data.datasets[0].data = weekly.series; chart.update();
        document.getElementById('btnWeekly').className = 'badge bg-secondary border-0';
        document.getElementById('btnMonthly').className = 'badge bg-light text-secondary border';
    });
    document.getElementById('btnMonthly').addEventListener('click', () => {
        chart.data.labels = monthly.labels; chart.data.datasets[0].data = monthly.series; chart.update();
        document.getElementById('btnMonthly').className = 'badge bg-secondary border-0';
        document.getElementById('btnWeekly').className = 'badge bg-light text-secondary border';
    });
</script>
@endsection