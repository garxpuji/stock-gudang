<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Halaman Tidak Ditemukan - Stock Gudang GMI</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { max-width: 460px; width: 100%; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; text-align: center; }
        .code { display: inline-block; font-size: 13px; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 4px 12px; border-radius: 9999px; margin-bottom: 16px; }
        h1 { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        p { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 24px; }
        .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; }
        .btn-primary { background: #059669; color: #fff; }
        .btn-primary:hover { background: #047857; }
        .brand { margin-top: 24px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <main class="card">
        <span class="code">Kode Error 404</span>
        <h1>Halaman Tidak Ditemukan</h1>
        <p>Tautan yang Anda tuju tidak tersedia, sudah dipindahkan, atau data transaksi yang diminta tidak ditemukan.</p>
        <div class="actions">
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>
        <div class="brand">Sistem Logistik &amp; Inventaris Gudang &bull; PT Gayuh Media Informatika</div>
    </main>
</body>
</html>
