Database Design – Sistem Manajemen Material & Pemusnahan (UPP Material)

Versi: 4.1
Tanggal: 10 Oktober 2025
Status: Final

1. Latar Belakang

Sistem ini dikembangkan untuk mengelola pergerakan material di lingkungan organisasi yang terdiri dari Pusat, SA/Wilayah, dan Plant (SPBE/BPT).
Tujuannya adalah agar seluruh stok material dapat dilacak secara real-time, setiap transaksi terekam, dan proses pemusnahan material (UPP Material) dapat dikelola secara transparan dari pengajuan hingga persetujuan akhir.

2. Tujuan Pengembangan

Menghadirkan database terstruktur yang mendukung alur distribusi material dari pusat ke daerah (wilayah/plant).

Memastikan pencatatan stok selalu akurat dan terintegrasi.

Menyediakan mekanisme audit yang kuat melalui tabel transaction_log.

Memfasilitasi alur resmi pemusnahan material (UPP Material) dengan tahapan pengajuan → verifikasi → persetujuan → pengurangan stok otomatis.

3. Arsitektur Data Umum

Database terdiri dari 8 tabel utama dan relasi antar-entitas yang saling terhubung:

regions → lokasi tingkat atas (pusat & wilayah)

plant → lokasi operasional (SPBE/BPT)

item → daftar material

destination_sales → referensi tujuan pengiriman/sales

initial_stock → catatan input awal stok

current_stock → saldo stok terkini

destruction_submission → pengajuan pemusnahan material

transaction_log → log aktivitas stok harian (ledger utama)

4. Hubungan Antar Tabel

regions 1 ↔ N plant → setiap wilayah memiliki banyak plant.

item 1 ↔ N current_stock → satu item bisa berada di banyak lokasi.

item 1 ↔ N transaction_log → setiap transaksi melibatkan satu jenis item.

destruction_submission 1 ↔ 1 transaction_log → satu pengajuan pemusnahan menghasilkan satu log transaksi final.

destination_sales 1 ↔ N transaction_log → digunakan saat transaksi tipe Transaksi Sales.

5. Struktur Tabel dan Atribut
   5.1. Tabel regions

Mendefinisikan semua lokasi regional termasuk Pusat dan SA/Wilayah.

Atribut Tipe Data Keterangan
region_id INT (PK) ID unik untuk setiap Region
nama_regions VARCHAR(50) Nama Region/SA (misal: "Pusat", "SA Jambi", dll)
5.2. Tabel plant

Menyimpan data lokasi operasional seperti SPBE dan BPT yang berada di bawah wilayah tertentu.

Atribut Tipe Data Keterangan
plant_id INT (PK) ID unik Plant
region_id INT (FK) Terhubung ke regions.region_id
nama_plant VARCHAR(100) Nama lengkap Plant
kode_plant VARCHAR(20) Kode unik Plant
kategori_plant ENUM('SPBE', 'BPT') Jenis plant
provinsi VARCHAR(50) Provinsi lokasi plant
kabupaten VARCHAR(50) Kabupaten/kota lokasi plant
5.3. Tabel item

Menampung daftar semua material yang dikelola sistem.

Atribut Tipe Data Keterangan
item_id INT (PK) ID unik material
nama_material VARCHAR(100) Nama material
kode_material VARCHAR(20) Kode unik material
kategori_material ENUM('Baik', 'Baru', 'Rusak', 'Afkir') Kondisi material
5.4. Tabel destination_sales ✅ (Revisi Lengkap)

Menentukan tujuan pengiriman material dalam aktivitas sales atau penyaluran ke pihak tertentu.

Atribut Tipe Data Keterangan
destination_id INT (PK) ID tujuan
nama_tujuan VARCHAR(50) Jenis tujuan pengiriman
keterangan VARCHAR(100) (Opsional) Keterangan tambahan

Contoh Data Awal:

ID nama_tujuan Keterangan
1 Vendor UPP Tujuan pengiriman ke vendor UPP
2 Sales Agen Pengiriman ke agen resmi
3 Sales SPBE Distribusi ke SPBE
4 Sales BPT Distribusi ke BPT
5.5. Tabel initial_stock

Mencatat input awal stok yang dimasukkan hanya oleh Pusat sebagai stok dasar sistem.

Atribut Tipe Data Keterangan
initial_stock_id INT (PK) ID stok awal
item_id INT (FK) Material terkait
quantity DECIMAL(10,2) Jumlah awal
tanggal_masuk DATE Tanggal input stok awal
5.6. Tabel current_stock

Menyimpan saldo stok real-time untuk setiap lokasi dan material.

Atribut Tipe Data Keterangan
stock_id INT (PK) ID stok unik
lokasi_type ENUM('REGION', 'PLANT') Menentukan jenis lokasi pemilik stok
lokasi_id INT (FK) ID dari lokasi (region/plant)
item_id INT (FK) Material terkait
current_quantity DECIMAL(10,2) Saldo stok terakhir
5.7. Tabel destruction_submission

Mengatur seluruh proses pengajuan pemusnahan material (UPP Material) di Pusat.

Atribut Tipe Data Keterangan
submission_id INT (PK) ID pengajuan
no_surat VARCHAR(50) Nomor surat resmi pengajuan
tanggal_pengajuan DATE Tanggal pengajuan
tahapan VARCHAR(50) Status tahapan proses (misal: "Pengajuan Awal")
penanggung_jawab VARCHAR(100) Nama penanggung jawab
item_id INT (FK) Material yang diajukan untuk dimusnahkan
kuantitas_diajukan DECIMAL(10,2) Jumlah material yang diajukan
aktivitas_pemusnahan VARCHAR(255) Deskripsi aktivitas pemusnahan
keterangan_pengajuan TEXT Catatan tambahan
status_pengajuan ENUM('PROSES', 'DONE', 'DITOLAK') Status pengajuan
transaction_log_id BIGINT (FK, NULL) Diisi otomatis saat status = DONE
5.8. Tabel transaction_log ✅ (Revisi Lengkap)

Tabel inti yang mencatat setiap pergerakan stok untuk keperluan audit, laporan, dan rekonsiliasi.

Atribut Tipe Data Keterangan
log_id BIGINT (PK) ID log unik
tanggal_transaksi DATETIME Waktu kejadian transaksi
item_id INT (FK) Material yang diproses
tipe_pergerakan ENUM('Penerimaan', 'Penyaluran', 'Transaksi Sales', 'Pemusnahan') Jenis aktivitas transaksi
kuantitas DECIMAL(10,2) Jumlah stok yang bergerak
stok_akhir_sebelum DECIMAL(10,2) Saldo sebelum transaksi
stok_akhir_sesudah DECIMAL(10,2) Saldo setelah transaksi
lokasi_type_actor ENUM('REGION', 'PLANT') Jenis lokasi asal transaksi
lokasi_actor_id INT (FK) ID lokasi asal
lokasi_tujuan_id INT (NULL) ID lokasi tujuan (jika relevan)
destination_sales_id INT (FK, NULL) Tujuan pengiriman sales
submission_id INT (FK, NULL) ID pengajuan pemusnahan
keterangan VARCHAR(100) (Opsional) Catatan tambahan (misal nomor surat) 6. Alur Skenario Lengkap
Skenario 1 – Input Stok Awal di Pusat

Admin pusat menginput stok awal melalui tabel initial_stock.

Data tersebut otomatis menambah saldo pada current_stock di lokasi REGION = Pusat.

Skenario 2 – Penyaluran Material ke Wilayah

Pusat menyalurkan material ke SA Jambi.

Sistem membuat log di transaction_log dengan tipe = Penyaluran.

current_stock pusat berkurang, dan stok wilayah Jambi bertambah.

Skenario 3 – Penerimaan di Plant

SA Jambi mengirim stok ke Plant “SPBE Jambi Utama”.

Dua log transaksi terbentuk:

SA Jambi → tipe Penyaluran

SPBE Jambi Utama → tipe Penerimaan

current_stock masing-masing lokasi diperbarui otomatis.

Skenario 4 – Transaksi Sales

SPBE melakukan penjualan ke Sales SPBE.

Sistem membuat transaction_log dengan tipe Transaksi Sales dan destination_sales_id = “Sales SPBE”.

current_stock di SPBE berkurang sesuai jumlah penjualan.

Skenario 5 – Pengajuan Pemusnahan (UPP Material)

Penanggung jawab pusat mengajukan pemusnahan Tabung LPG Afkir.

Data disimpan di destruction_submission dengan status = PROSES.

Setelah disetujui, status berubah menjadi DONE, sistem otomatis:

Membuat log di transaction_log (tipe = Pemusnahan)

Mengurangi stok di current_stock

Menautkan transaction_log_id ke pengajuan tersebut

Hasil akhir:

Semua aktivitas terekam.

Audit dapat dilakukan dengan mudah menggunakan data transaction_log.

7. Kesimpulan

Desain database ini memberikan:

Integritas data tinggi antar proses logistik dan administrasi.

Konsistensi stok real-time di semua level organisasi.

Keterlacakan penuh (traceability) dari awal stok hingga pemusnahan akhir.
