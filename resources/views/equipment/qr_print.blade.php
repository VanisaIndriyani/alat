<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Semua QR Peralatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        .qr-box { width: 160px; }
        @media print { .no-print { display:none; } }
    </style>
</head>
<body class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Cetak Semua QR Peralatan</h5>
        <button class="btn btn-primary no-print" onclick="window.print()"><i class='bx bx-printer'></i> Print</button>
    </div>
    <div class="row g-3">
        @foreach($equipments as $eq)
            <div class="col-6 col-md-3 col-lg-2">
                <div class="border rounded p-2 text-center">
                    <div id="qr-{{ $eq->id }}" class="mx-auto qr-box"></div>
                    <div class="small mt-2">{{ $eq->name }}</div>
                    <div class="fw-semibold">{{ $eq->code ?? ('EQ-' . str_pad($eq->id,4,'0',STR_PAD_LEFT)) }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        @foreach($equipments as $eq)
            new QRCode(document.getElementById('qr-{{ $eq->id }}'), {
                text: "{{ $eq->code ?? ('EQ-' . str_pad($eq->id,4,'0',STR_PAD_LEFT)) }}",
                width: 150,
                height: 150
            });
        @endforeach
    </script>
</body>
</html>