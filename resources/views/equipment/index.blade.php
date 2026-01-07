@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2"><i class='bx bx-wrench'></i> Data Peralatan</div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('equipment.qr.print') }}" target="_blank"><i class='bx bx-qr'></i> Cetak Semua QR</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd"><i class='bx bx-plus'></i> Tambah Alat</button>
    </div>
    </div>

<div class="d-flex justify-content-between align-items-center mb-2">
    <form class="input-group w-50" method="GET" action="{{ route('equipment') }}">
        <span class="input-group-text"><i class='bx bx-search'></i></span>
        <input class="form-control" name="q" value="{{ $q }}" placeholder="Cari alat atau ID...">
        <button class="btn btn-outline-secondary">Cari</button>
    </form>
    <div class="text-muted small">Total: {{ $equipments->count() }}</div>
 </div>

<div class="row g-3">
    @foreach($equipments as $eq)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
                @if($eq->image_path)
                    <img src="{{ asset($eq->image_path) }}" alt="{{ $eq->name }}" class="card-img-top" style="height:140px;object-fit:cover">
                @endif
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="fw-semibold">{{ $eq->name }}</div>
                            <div class="text-muted small">{{ $eq->code }}</div>
                        </div>
                        <div>
                            @if($eq->status==='available')<span class="badge bg-success">Tersedia</span>
                            @elseif($eq->status==='loaned')<span class="badge bg-warning text-dark">Dipinjam</span>
                            @else<span class="badge bg-danger">Rusak</span>@endif
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('equipment.qr.single', $eq->id) }}" target="_blank" title="Cetak QR"><i class='bx bx-qr'></i></a>
                        <button class="btn btn-sm btn-outline-primary" title="Edit" data-bs-toggle="modal" data-bs-target="#modalEdit" data-id="{{ $eq->id }}" data-name="{{ $eq->name }}" data-code="{{ $eq->code }}" data-status="{{ $eq->status }}" data-image="{{ $eq->image_path }}"><i class='bx bx-edit'></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalDelete" data-id="{{ $eq->id }}" data-name="{{ $eq->name }}"><i class='bx bx-trash'></i></button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class='bx bx-plus'></i> Tambah Alat</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" action="{{ route('equipment.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nama Alat</label><input class="form-control" name="name" required></div>
          <div class="mb-3"><label class="form-label">Kode (opsional)</label><input class="form-control" name="code" placeholder="TL-001"></div>
          <div class="mb-3"><label class="form-label">Gambar</label><input type="file" class="form-control" name="image"></div>
          <div class="mb-3"><label class="form-label">Status</label>
            <select class="form-select" name="status" required>
              <option value="available">Tersedia</option>
              <option value="loaned">Dipinjam</option>
              <option value="damaged">Rusak</option>
            </select>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
      </form>
    </div>
  </div>
 </div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class='bx bx-edit'></i> Edit Alat</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" id="formEdit" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nama Alat</label><input class="form-control" name="name" id="editName" required></div>
          <div class="mb-3"><label class="form-label">Kode</label><input class="form-control" name="code" id="editCode"></div>
          <div class="mb-3"><label class="form-label">Gambar</label><input type="file" class="form-control" name="image"><div class="mt-2"><img id="editPreview" src="" alt="Preview" style="height:80px;display:none"></div></div>
          <div class="mb-3"><label class="form-label">Status</label>
            <select class="form-select" name="status" id="editStatus" required>
              <option value="available">Tersedia</option>
              <option value="loaned">Dipinjam</option>
              <option value="damaged">Rusak</option>
            </select>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
      </form>
    </div>
  </div>
 </div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class='bx bx-trash'></i> Hapus Alat</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" id="formDelete">
        @csrf
        <div class="modal-body">
          <p>Yakin menghapus <strong id="delName"></strong>?</p>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger" type="submit">Hapus</button></div>
      </form>
    </div>
  </div>
 </div>

<script>
  const editModal = document.getElementById('modalEdit');
  editModal.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    const id = btn.getAttribute('data-id');
    document.getElementById('editName').value = btn.getAttribute('data-name');
    document.getElementById('editCode').value = btn.getAttribute('data-code') || '';
    document.getElementById('editStatus').value = btn.getAttribute('data-status');
    document.getElementById('formEdit').action = `{{ url('/peralatan') }}/${id}`;
    const img = btn.getAttribute('data-image');
    const prev = document.getElementById('editPreview');
    if(img){ prev.src = `{{ url('/') }}/${img}`; prev.style.display='inline-block'; } else { prev.style.display='none'; }
  });

  const deleteModal = document.getElementById('modalDelete');
  deleteModal.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    const id = btn.getAttribute('data-id');
    document.getElementById('delName').innerText = btn.getAttribute('data-name');
    document.getElementById('formDelete').action = `{{ url('/peralatan') }}/${id}/delete`;
  });
</script>
@endsection