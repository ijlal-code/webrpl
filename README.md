<div align="center">

# MANSIP  
### *(Manajemen Arsip Kantor Desa)*

<br>

<p align="center">
   <img src="LogUnsulbar.png" alt="Logo" width="300"/>
 </p>

<br>

<h2>Ainun Ijlal</h2>
<h2>D0223038</h2>

<br>

<h4>Framework Web Based</h4>
<h4>2025</h4>

</div>

## Role dan Fitur-fiturnya

### Admin
- Mengelola pengguna (CRUD User)
- Melihat semua arsip

### Sekretaris Desa
- Upload arsip baru
- Mengelola kategori arsip
- Melihat dan mengedit arsip yang diunggah
- Menghapus arsip

### Kepala Desa
- Melihat daftar arsip
- Mencetak / mengunduh arsip (PDF / DOCX)

## Tabel-tabel database beserta field dan tipe datanya

### Tabel `users`
| Field       | Tipe Data    | Keterangan                  |
|-------------|--------------|-----------------------------|
| id          | BIGINT       | Primary Key                 |
| name        | VARCHAR(255) | Nama Pengguna               |
| email       | VARCHAR(255) | Email (unik)                |
| password    | VARCHAR(255) | Password                    |
| role        | ENUM         | Admin, Sekretaris, Kepala Desa |
| created_at  | TIMESTAMP    | Timestamp dibuat            |
| updated_at  | TIMESTAMP    | Timestamp diperbarui        |

### Tabel `kategori_arsip`
| Field         | Tipe Data    | Keterangan            |
|---------------|--------------|-----------------------|
| id            | BIGINT       | Primary Key           |
| nama_kategori | VARCHAR(255) | Nama Kategori Arsip   |
| created_at    | TIMESTAMP    | Timestamp dibuat      |
| updated_at    | TIMESTAMP    | Timestamp diperbarui  |

### Tabel `arsip`
| Field         | Tipe Data    | Keterangan                       |
|---------------|--------------|----------------------------------|
| id            | BIGINT       | Primary Key                      |
| judul_arsip   | VARCHAR(255) | Nama arsip / judul               |
| file_arsip    | VARCHAR(255) | Nama file yang diupload          |
| tanggal_upload| DATE         | Tanggal pengarsipan              |
| kategori_id   | BIGINT       | Foreign Key ke `kategori_arsip` |
| user_id       | BIGINT       | Foreign Key ke `users`           |
| created_at    | TIMESTAMP    | Timestamp dibuat                 |
| updated_at    | TIMESTAMP    | Timestamp diperbarui             |

### Tabel `profiles` (One-to-One)
| Field      | Tipe Data    | Keterangan                   |
|------------|--------------|------------------------------|
| id         | BIGINT       | Primary Key                  |
| user_id    | BIGINT       | Foreign Key ke `users`       |
| alamat     | String       | Alamat pengguna              |
| no_hp      | String       | Nomor HP pengguna            |
| created_at | TIMESTAMP    | Timestamp dibuat             |
| updated_at | TIMESTAMP    | Timestamp diperbarui         |

### Tabel `arsip_user` (Many-to-Many - Pivot)
| Field      | Tipe Data    | Keterangan                           |
|------------|--------------|--------------------------------------|
| id         | BIGINT       | Primary Key                          |
| arsip_id   | BIGINT       | Foreign Key ke `arsip`               |
| user_id    | BIGINT       | Foreign Key ke `users`               |
| created_at | TIMESTAMP    | Timestamp dibuat                     |
| updated_at | TIMESTAMP    | Timestamp diperbarui                 |

## Jenis relasi dan tabel yang berelasi

- **One-to-Many:** `users` → `arsip`  
- **One-to-Many:** `kategori_arsip` → `arsip`  
- **One-to-One:** `users` ↔ `profiles`  
- **Many-to-Many:** `arsip` ↔ `users` melalui `arsip_user`


