# Sistem Informasi Inventaris & Logistik Gudang ISP
### PT Gayuh Media Informatika (GMI)

Aplikasi manajemen pergudangan dan logistik telekomunikasi internet service provider (ISP) berbasis **Laravel 11**, dirancang untuk menangani seluruh siklus operasional inventaris perangkat aktif (ONT, Router, Switch), material optik pasif (Kabel Dropcore, Patchcord, ODP), alat ukur (Splicer, OTDR), pelacakan Serial Number/MAC Address, retur teknisi, serta karantina barang rusak dan klaim garansi RMA.

---

## 🌟 Fitur Utama Sistem

1. **Dashboard KPI & Analitik Inventaris**:
   - Total valuasi aset gudang aktif dan nilai kerugian barang karantina.
   - Peringatan dini stok minimum (*low stock alerts*).
   - Monitoring cepat unit berstatus *Available*, *Issued*, dan *Quarantined*.
2. **Katalog Produk & Material Jaringan**:
   - Pelacakan SKU, Barcode, Kategori ISP, Lokasi Rak, dan Satuan Ukur.
   - Opsi pelacakan Serial Number & MAC Address per unit perangkat aktif.
3. **Penerimaan Barang Masuk (Inbound / Pengadaan Vendor)**:
   - Pencatatan PO, Surat Jalan Vendor, Kurir, dan harga beli satuan.
   - Input Serial Number secara fleksibel (koma / baris baru) dengan registrasi otomatis ke buku besar `ProductSerial`.
4. **Pengeluaran Barang ke Teknisi Lapangan (Outbound Dispatch)**:
   - Pencatatan SPK / Tiket PSB / Maintenance, nama teknisi penerima, dan divisi.
   - Alokasi unit Serial Number spesifik berstatus `AVAILABLE`.
   - Cetak langsung **Surat Bukti Pengeluaran Barang (Surat Jalan)** resmi ber-kop perusahaan.
5. **Retur Barang & Inspeksi Quality Control (QC)**:
   - Penerimaan barang sisa material atau dismantle dari pelanggan.
   - Inspeksi QC per unit: status `GOOD` otomatis restock kembali ke gudang, status `DAMAGED` otomatis masuk ke Area Karantina.
   - Cetak langsung **Berita Acara Pengembalian Barang**.
6. **Area Karantina & Penanganan Barang Rusak (RMA / Scrapped)**:
   - Pencatatan kategori kerusakan fisik/teknis (tersambar petir, port jebol, laser drop, mati total).
   - Unggah bukti foto kerusakan fisik.
   - Eksekusi tindakan lanjutan: **Klaim Garansi (RMA) Vendor**, **Pemusnahan (Scrapped)** dengan kalkulasi nilai kerugian aset, atau **Perbaikan Internal**.
7. **Audit Stok Opname Fisik**:
   - Penyesuaian saldo fisik riil dengan saldo sistem dan pencatatan selisih mutasi opname otomatis.
8. **Buku Besar Mutasi Stok Terpadu (Immutable Stock Ledger)**:
   - Riwayat mutasi mendalam per SKU, tipe transaksi, operator penanggung jawab, dan saldo berjalan.
9. **Kelola Pengguna (User Management)**:
   - Manajemen staf gudang, Superadmin, penugasan role, dan reset password.
10. **Kelola Data Master Lengkap**:
    - Manajemen Satuan Ukur (Pcs, Meter, Roll, Set, dll).
    - Manajemen Kategori Barang (ONT, Router, Dropcore, Pasif FO, dll).
    - Manajemen Lokasi Rak Gudang (RAK-A1, Area Drum Kabel, dll).
    - Manajemen Supplier & Distributor Mitra.
11. **Ekspor Laporan**:
    - Ekspor data produk, transaksi pengeluaran, retur, dan karantina ke format Microsoft Excel (.xlsx).

---

## 🚀 Panduan Instalasi Cepat

Panduan instalasi lengkap dan mendalam tersedia di:
👉 **[PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md)**

### Ringkasan Perintah:

```bash
# 1. Clone repositori
git clone https://github.com/garxpuji/stock-gudang.git
cd stock-gudang

# 2. Salin environment & atur database
copy .env.example .env

# 3. Install dependensi backend & frontend
composer install
npm install
npm run build

# 4. Generate application key
php artisan key:generate

# 5. Jalankan migrasi dan seeder akun
php artisan migrate --seed

# 6. Hubungkan symbolic link storage
php artisan storage:link

# 7. Jalankan server lokal
php artisan serve --port=8001
```

Akses aplikasi di: **`http://127.0.0.1:8001`**

---

## 🔑 Kredensial Akun Default

| Email | Password | Role | Nama Lengkap |
| :--- | :--- | :--- | :--- |
| `admin@gayuhmedia.id` | `password` | **Admin Gudang** | Budi Santoso |
| `tegar@gayuhmedia.id` | `password` | **Superadmin** | Tegar Pratama (Principal NOC) |
| `gudang@gayuhmedia.id` | `password` | **Admin Gudang** | Aris Munandar |

---

## 🧪 Pengujian Otomatis (Testing)

Proyek ini dilengkapi dengan 29 test case otomatis (197 assertions) yang memverifikasi integritas seluruh controller, actions, validasi, dan alur end-to-end gudang:

```bash
php vendor/bin/phpunit
```

Hasil uji: **100% PASSED** (0 Failures, 0 Errors).

---

## 📄 Lisensi
Hak Cipta &copy; 2026 PT Gayuh Media Informatika. Seluruh hak cipta dilindungi undang-undang.
