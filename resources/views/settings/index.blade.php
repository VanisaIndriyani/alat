@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3"><i class='bx bx-cog'></i> Pengaturan Aplikasi</div>

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
  <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-instansi" type="button">Instansi & Tema</button></li>
  <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-petugas" type="button">Data Petugas</button></li>
  <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-peminjam" type="button">Data Peminjam</button></li>
  <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-about" type="button">Tentang Aplikasi</button></li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="pills-instansi">
    <div class="card shadow-sm"><div class="card-body">
      <form method="POST" action="{{ route('settings.institution.save') }}" enctype="multipart/form-data" class="row g-3">
        @csrf
        <div class="col-12"><div class="fw-semibold">Profil Instansi</div></div>
        <div class="col-12 col-md-3">
          <label class="form-label">Logo Instansi</label>
          <div class="d-flex align-items-center gap-3">
            <input type="file" name="logo" class="form-control">
            <img src="{{ $setting?->logo_path ? asset($setting->logo_path) : asset('img/logo.png') }}" alt="Logo" class="rounded" style="width:56px;height:56px;object-fit:cover;">
          </div>
        </div>
        <div class="col-12 col-md-9"></div>
        <div class="col-12 col-md-6"><label class="form-label">Nama Instansi / Sekolah</label><input class="form-control" name="school_name" value="{{ $setting->school_name ?? '' }}"></div>
        <div class="col-12 col-md-6"><label class="form-label">Nama Jurusan / Bengkel</label><input class="form-control" name="department_name" value="{{ $setting->department_name ?? '' }}"></div>
        <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="address">{{ $setting->address ?? '' }}</textarea></div>
        <div class="col-12 col-md-6"><label class="form-label">Nama Kepala Bengkel</label><input class="form-control" name="head_name" value="{{ $setting->head_name ?? '' }}"></div>
        <div class="col-12 col-md-6"><label class="form-label">NIP Kepala Bengkel</label><input class="form-control" name="head_nip" value="{{ $setting->head_nip ?? '' }}"></div>
        <div class="col-12 col-md-6">
          <label class="form-label">Tema Warna Aplikasi (Sidebar)</label>
          <input type="color" class="form-control form-control-color" name="theme_primary" value="{{ $setting->theme_primary ?? '#0b3a82' }}" title="Pilih warna tema">
        </div>
        <div class="col-12"><label class="form-label">Teks Footer (Copyright)</label><input class="form-control" name="footer_text" value="{{ $setting->footer_text ?? '© 2026 Tim IT SMK Nasional Dawarblandong' }}"></div>
        <div class="col-12"><button class="btn btn-primary"><i class='bx bx-save'></i> Simpan</button></div>
      </form>
    </div></div>
  </div>

  <div class="tab-pane fade" id="pills-petugas">
    <div class="row g-3">
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-semibold">Daftar Petugas</div>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddStaff"><i class='bx bx-plus'></i> Tambah Petugas</button>
        </div>
        <div class="card shadow-sm"><div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead><tr><th>Nama</th><th>Jabatan</th><th>QR Tanda Tangan</th><th>Akun</th><th>Aksi</th></tr></thead>
              <tbody>
                @forelse($staff as $s)
                  <tr>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->position }}</td>
                    <td><div id="qr-staff-{{ $s->id }}"></div></td>
                    <td>
                      @if($s->user)
                        <span class="badge bg-success">Sudah Ada</span>
                        <div class="small text-muted">{{ $s->user->email }}</div>
                      @else
                        <span class="badge bg-warning text-dark">Belum Ada</span>
                        <div class="mt-2">
                          <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateAccount" data-id="{{ $s->id }}" data-name="{{ $s->name }}"><i class='bx bx-user-plus'></i> Buat Akun</button>
                        </div>
                      @endif
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditStaff" data-id="{{ $s->id }}" data-name="{{ $s->name }}" data-position="{{ $s->position }}"><i class='bx bx-edit'></i></button>
                      <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteStaff" data-id="{{ $s->id }}" data-name="{{ $s->name }}"><i class='bx bx-trash'></i></button>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-muted">Belum ada petugas.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div></div>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="pills-peminjam">
    <div class="d-flex align-items-center gap-3 mb-2">
      <span class="badge bg-light text-secondary">Siswa & Guru</span>
      <div class="ms-auto">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddStudent"><i class='bx bx-plus'></i> Tambah Peminjam</button>
      </div>
    </div>
    <div class="row g-3">
      <div class="col-12">
        <div class="card shadow-sm"><div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead><tr><th>NIS/NIP</th><th>Nama</th><th>Kelas</th><th>Tipe</th><th>Aksi</th></tr></thead>
              <tbody>
                @forelse($students as $st)
                  <tr>
                    <td>{{ $st->nis }}</td>
                    <td>{{ $st->name }}</td>
                    <td>{{ $st->class }}</td>
                    <td>{{ $st->type==='student'?'Siswa':'Guru' }}</td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditStudent" data-id="{{ $st->id }}" data-nis="{{ $st->nis }}" data-name="{{ $st->name }}" data-class="{{ $st->class }}" data-type="{{ $st->type }}"><i class='bx bx-edit'></i></button>
                      <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteStudent" data-id="{{ $st->id }}" data-name="{{ $st->name }}"><i class='bx bx-trash'></i></button>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-muted">Belum ada peminjam.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div></div>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="pills-about">
    <div class="card shadow-sm"><div class="card-body">
      <div class="fw-semibold mb-2">Tentang Aplikasi</div>
      <div class="text-muted">Sistem Peminjaman Alat — SMK Nasional Dawarblandong Mojokerto.</div>
    </div></div>
  </div>
</div>

<!-- Modals Staff -->
<div class="modal fade" id="modalAddStaff" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class='bx bx-plus'></i> Tambah Petugas</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" action="{{ route('settings.staff.store') }}">@csrf<div class="modal-body"><div class="mb-3"><label class="form-label">Nama Petugas</label><input class="form-control" name="name" required></div><div class="mb-3"><label class="form-label">Jabatan</label><input class="form-control" name="position"></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div></form></div></div></div>
<div class="modal fade" id="modalEditStaff" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class='bx bx-edit'></i> Edit Petugas</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" id="formEditStaff">@csrf<div class="modal-body"><div class="mb-3"><label class="form-label">Nama Petugas</label><input class="form-control" name="name" id="staffName" required></div><div class="mb-3"><label class="form-label">Jabatan</label><input class="form-control" name="position" id="staffPosition"></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div></form></div></div></div>
<div class="modal fade" id="modalDeleteStaff" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class='bx bx-trash'></i> Hapus Petugas</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" id="formDeleteStaff">@csrf<div class="modal-body">Yakin menghapus <strong id="delStaffName"></strong>?</div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger" type="submit">Hapus</button></div></form></div></div></div>

<div class="modal fade" id="modalCreateAccount" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class='bx bx-user-plus'></i> Buat Akun Petugas</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" id="formCreateAccount">@csrf<div class="modal-body"><div class="mb-2 small text-muted" id="createAccFor"></div><div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div><div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Buat Akun</button></div></form></div></div></div>

<!-- Modals Student -->
<div class="modal fade" id="modalAddStudent" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class='bx bx-plus'></i> Tambah Peminjam</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" action="{{ route('settings.student.store') }}">@csrf<div class="modal-body"><div class="row g-3"><div class="col-12 col-md-6"><label class="form-label">NIS/NIP</label><input class="form-control" name="nis" required></div><div class="col-12 col-md-6"><label class="form-label">Nama</label><input class="form-control" name="name" required></div><div class="col-12 col-md-6"><label class="form-label">Kelas</label><input class="form-control" name="class"></div><div class="col-12 col-md-6"><label class="form-label">Tipe</label><select name="type" class="form-select"><option value="student">Siswa</option><option value="teacher">Guru</option></select></div></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div></form></div></div></div>
<div class="modal fade" id="modalEditStudent" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class='bx bx-edit'></i> Edit Peminjam</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" id="formEditStudent">@csrf<div class="modal-body"><div class="row g-3"><div class="col-12 col-md-6"><label class="form-label">NIS/NIP</label><input class="form-control" name="nis" id="stNis" required></div><div class="col-12 col-md-6"><label class="form-label">Nama</label><input class="form-control" name="name" id="stName" required></div><div class="col-12 col-md-6"><label class="form-label">Kelas</label><input class="form-control" name="class" id="stClass"></div><div class="col-12 col-md-6"><label class="form-label">Tipe</label><select name="type" class="form-select" id="stType"><option value="student">Siswa</option><option value="teacher">Guru</option></select></div></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div></form></div></div></div>
<div class="modal fade" id="modalDeleteStudent" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class='bx bx-trash'></i> Hapus Peminjam</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" id="formDeleteStudent">@csrf<div class="modal-body">Yakin menghapus <strong id="delStudentName"></strong>?</div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger" type="submit">Hapus</button></div></form></div></div></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  @foreach($staff as $s)
    new QRCode(document.getElementById('qr-staff-{{ $s->id }}'), { text: "{{ $s->name }}", width: 64, height: 64 });
  @endforeach

  const editStaffModal = document.getElementById('modalEditStaff');
  editStaffModal.addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget;
    document.getElementById('staffName').value = b.getAttribute('data-name');
    document.getElementById('staffPosition').value = b.getAttribute('data-position') || '';
    document.getElementById('formEditStaff').action = `{{ url('/pengaturan/petugas') }}/${b.getAttribute('data-id')}`;
  });
  const deleteStaffModal = document.getElementById('modalDeleteStaff');
  deleteStaffModal.addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget;
    document.getElementById('delStaffName').innerText = b.getAttribute('data-name');
    document.getElementById('formDeleteStaff').action = `{{ url('/pengaturan/petugas') }}/${b.getAttribute('data-id')}/delete`;
  });

  const createAccModal = document.getElementById('modalCreateAccount');
  createAccModal.addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget;
    document.getElementById('createAccFor').innerText = b.getAttribute('data-name');
    document.getElementById('formCreateAccount').action = `{{ url('/pengaturan/petugas') }}/${b.getAttribute('data-id')}/account`;
  });

  const editStudentModal = document.getElementById('modalEditStudent');
  editStudentModal.addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget;
    document.getElementById('stNis').value = b.getAttribute('data-nis');
    document.getElementById('stName').value = b.getAttribute('data-name');
    document.getElementById('stClass').value = b.getAttribute('data-class') || '';
    document.getElementById('stType').value = b.getAttribute('data-type');
    document.getElementById('formEditStudent').action = `{{ url('/pengaturan/peminjam') }}/${b.getAttribute('data-id')}`;
  });
  const deleteStudentModal = document.getElementById('modalDeleteStudent');
  deleteStudentModal.addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget;
    document.getElementById('delStudentName').innerText = b.getAttribute('data-name');
    document.getElementById('formDeleteStudent').action = `{{ url('/pengaturan/peminjam') }}/${b.getAttribute('data-id')}/delete`;
  });
</script>
@endsection