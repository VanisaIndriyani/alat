@extends('layouts.app')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2"><i class='bx bx-barcode'></i> Input Alat (Scan / Cari)</div>
                    <span class="text-muted small">Tekan Enter setelah scan/ketik</span>
                </div>
                <form id="searchForm" class="input-group mb-3" method="POST" action="{{ route('loans.search') }}">
                    @csrf
                    <span class="input-group-text"><i class='bx bx-search'></i></span>
                    <input id="queryInput" type="text" name="query" class="form-control" placeholder="Scan Barcode atau ketik nama alat atau kode (TL-XXX)..." value="{{ $query }}">
                    <button class="btn btn-outline-secondary"><i class='bx bx-send'></i> Enter</button>
                    <button type="button" class="btn btn-outline-primary" title="Kamera" data-bs-toggle="modal" data-bs-target="#modalCamera"><i class='bx bx-camera'></i> Kamera</button>
                </form>
                @if(!empty($results))
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Kode</th><th>Nama Alat</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                                @forelse($results as $r)
                                    <tr>
                                        <td>{{ $r->code }}</td>
                                        <td>{{ $r->name }}</td>
                                        <td>@if($r->status==='available')<span class="badge bg-success">Tersedia</span>@elseif($r->status==='loaned')<span class="badge bg-warning text-dark">Dipinjam</span>@else<span class="badge bg-danger">Rusak</span>@endif</td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('loans.cart.add') }}">
                                                @csrf
                                                <input type="hidden" name="equipment_id" value="{{ $r->id }}">
                                                <button class="btn btn-sm btn-primary"><i class='bx bx-plus'></i> Tambah</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">Tidak ada alat tersedia untuk kata kunci tersebut.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2"><i class='bx bx-id-card'></i> Identitas Peminjam</div>
                    <span class="badge bg-warning text-dark">Denda: Rp 1.000/hari</span>
                </div>
                <form method="POST" action="{{ route('loans.process') }}" class="mt-3 row g-3">
                    @csrf
                    <div class="col-12">
                        <label class="form-label">Petugas</label>
                        @if(count($staff) === 1)
                            <input type="hidden" name="staff_id" value="{{ $staff[0]->id }}">
                            <div class="form-control bg-light">{{ $staff[0]->name }}{{ $staff[0]->position? ' ('.$staff[0]->position.')':'' }}</div>
                        @elseif(count($staff) > 1)
                            <select class="form-select" name="staff_id">
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}{{ $s->position? ' ('.$s->position.')':'' }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-warning small">Akun petugas Anda belum tertaut pada data petugas. Hubungi admin untuk membuatkannya di Pengaturan.</div>
                        @endif
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">NIS / NIP Peminjam</label>
                        <div class="input-group">
                            <input class="form-control" name="borrower_nis" id="borrowerNis" placeholder="Masukkan Nomor Induk" required>
                            <button type="button" class="btn btn-outline-secondary" id="btnFindNis" title="Cari"><i class='bx bx-search'></i></button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Nama Peminjam</label>
                        <input class="form-control" name="borrower_name" id="borrowerName" placeholder="Nama Lengkap (otomatis)" required readonly>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Rencana Kembali</label>
                        <input type="date" name="planned_return" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Keperluan</label>
                        <input class="form-control" name="purpose" placeholder="Cth: Praktik Jaringan" required>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" {{ count($cart) ? '' : 'disabled' }}><i class='bx bx-check-circle'></i> Proses Peminjaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2"><div class="d-flex align-items-center gap-2"><i class='bx bx-cart-alt'></i> Keranjang</div><span class="badge bg-light text-secondary">{{ count($cart) }} Item</span></div>
                @if(count($cart))
                    <ul class="list-group mb-3">
                        @foreach($cart as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $item['name'] }}</div>
                                    <div class="text-muted small">{{ $item['code'] }}</div>
                                </div>
                                <form method="POST" action="{{ route('loans.cart.remove') }}">
                                    @csrf
                                    <input type="hidden" name="equipment_id" value="{{ $item['id'] }}">
                                    <button class="btn btn-sm btn-outline-danger"><i class='bx bx-trash'></i></button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                    <div class="d-flex justify-content-between">
                        <div class="text-muted small">Total item: {{ count($cart) }}</div>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('equipment.qr.print') }}" target="_blank"><i class='bx bx-qr'></i> Cetak QR</a>
                    </div>
                @else
                    <div class="text-muted small">Belum ada alat dipilih. Scan atau cari alat di kolom kiri.</div>
                @endif
                @if(session('cart_msg'))<div class="alert alert-info mt-2">{{ session('cart_msg') }}</div>@endif
                @error('cart')<div class="alert alert-danger mt-2">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
@if(session('loan_success'))
<div class="alert alert-success mt-3">{{ session('loan_success') }}</div>
@endif
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
  async function fetchBorrower(nis){
    if(!nis) return;
    try {
      const res = await fetch(`{{ route('settings.student.find') }}?nis=${encodeURIComponent(nis)}`);
      const data = await res.json();
      if(data.found){ document.getElementById('borrowerName').value = data.name; }
    } catch(e){}
  }
  const nisInput = document.getElementById('borrowerNis');
  let timer;
  nisInput.addEventListener('input', function(){
    clearTimeout(timer);
    timer = setTimeout(()=>fetchBorrower(nisInput.value.trim()), 400);
  });
  document.getElementById('btnFindNis').addEventListener('click', ()=>fetchBorrower(nisInput.value.trim()));

  let qrScanner;
  const camModal = document.getElementById('modalCamera');
  camModal.addEventListener('shown.bs.modal', () => {
    const el = document.getElementById('qrReader');
    qrScanner = new Html5Qrcode(el.id);
    Html5Qrcode.getCameras().then(cams => {
      const camId = cams && cams.length ? cams[0].id : null;
      qrScanner.start(camId, { fps: 10, qrbox: 250 }, text => {
        document.getElementById('queryInput').value = text;
        document.getElementById('searchForm').submit();
      }, () => {});
    }).catch(() => {});
  });
  camModal.addEventListener('hidden.bs.modal', () => {
    if(qrScanner){ qrScanner.stop().then(()=>qrScanner.clear()); }
  });
</script>
@endsection
<!-- Modal Kamera -->
<div class="modal fade" id="modalCamera" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class='bx bx-camera'></i> Scan QR</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div id="qrReader" style="width:100%"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
    </div>
  </div>
</div>