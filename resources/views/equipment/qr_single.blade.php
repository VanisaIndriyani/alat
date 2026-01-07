<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR {{ $eq->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>@media print { .no-print { display:none; } }</style>
</head>
<body class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">QR Code Alat</h5>
        <button class="btn btn-primary no-print" onclick="window.print()"><i class='bx bx-printer'></i> Print</button>
    </div>
    <div class="border rounded p-3 text-center" style="max-width:220px">
        <div id="qr" class="mx-auto"></div>
        <div class="small mt-2">{{ $eq->name }}</div>
        <div class="fw-semibold">{{ $eq->code ?? ('EQ-' . str_pad($eq->id,4,'0',STR_PAD_LEFT)) }}</div>
    </div>
    <script>
        new QRCode(document.getElementById('qr'), { text: "{{ $eq->code ?? ('EQ-' . str_pad($eq->id,4,'0',STR_PAD_LEFT)) }}", width: 180, height: 180 });
    </script>
</body>
</html>