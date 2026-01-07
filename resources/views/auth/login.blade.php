<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    @php($setting = \App\Models\AppSetting::first())

    <style>
        :root {
            --primary: {{ $setting?->theme_primary ?? '#0b3a82' }};
            --primary-dark: #072a5e;
        }

        body {
            background:
                radial-gradient(600px 300px at 10% 10%, rgba(255,255,255,.15), transparent),
                linear-gradient(180deg, var(--primary), var(--primary-dark));
        }

        .login-card {
            border-radius: 1rem;
            overflow: hidden;
            animation: fadeUp .6s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            background: linear-gradient(180deg, var(--primary), var(--primary-dark));
        }

        .logo-wrap {
            width: 72px;
            height: 72px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            box-shadow: 0 8px 24px rgba(0,0,0,.25);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem rgba(11,58,130,.15);
        }

        .btn-theme {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #fff;
            padding: .6rem;
            font-weight: 500;
        }

        .btn-theme:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
    </style>
</head>

<body>
<div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
    <div class="col-12 col-md-7 col-lg-4">
        <div class="card login-card shadow-lg border-0">
            <div class="login-header text-white text-center p-4">
                <div class="logo-wrap mb-3">
                    <img src="{{ $setting?->logo_path ? asset($setting->logo_path) : asset('img/logo.png') }}"
                         alt="Logo" style="width:48px;height:48px;object-fit:contain;">
                </div>

                <div class="fw-semibold">
                    {{ $setting?->school_name ?? 'SMK Nasional Dawarblandong' }}
                </div>
                <div class="small opacity-75">Sistem Peminjaman Alat</div>
                <span class="badge bg-light text-dark mt-2">
                    {{ $setting?->department_name ?? 'TEKNIK KOMPUTER & JARINGAN' }}
                </span>
            </div>

            <div class="card-body p-4">
                <h2 class="h5 text-center mb-3">Login Administrator</h2>

                @if ($errors->any())
                    <div class="alert alert-danger small text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class='bx bx-user'></i></span>
                            <input type="text" name="username" value="{{ old('username') }}"
                                   class="form-control" placeholder="Masukkan username" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class='bx bx-lock-alt'></i></span>
                            <input type="password" name="password"
                                   class="form-control" placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-theme w-100">
                        <i class='bx bx-log-in me-1'></i> Masuk Aplikasi
                    </button>
                </form>

                <div class="text-center text-white-50 small mt-4">
                    {{ $setting?->footer_text ?? '© 2026 Tim IT SMK Nasional Dawarblandong' }}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
