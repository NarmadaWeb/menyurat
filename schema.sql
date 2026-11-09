CREATE DATABASE IF NOT EXISTS menyurat;

USE menyurat;

CREATE TABLE peran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(50) NOT NULL
);

CREATE TABLE pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    peran_id INT,
    status ENUM('aktif', 'tidak_aktif') DEFAULT 'aktif',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (peran_id) REFERENCES peran(id)
);

CREATE TABLE profil_desa (
    id INT PRIMARY KEY DEFAULT 1,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    logo VARCHAR(255),
    nama_kades VARCHAR(100),
    nip_kades VARCHAR(50)
);

CREATE TABLE jenis_surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kode VARCHAR(10)
);

CREATE TABLE surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor VARCHAR(50) NOT NULL,
    tanggal_diterima DATE NOT NULL,
    pengirim VARCHAR(100) NOT NULL,
    perihal VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    path_file VARCHAR(255),
    status ENUM('baru', 'diproses', 'selesai') DEFAULT 'baru',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor VARCHAR(50) NOT NULL,
    tanggal_dikirim DATE NOT NULL,
    penerima VARCHAR(100) NOT NULL,
    perihal VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    path_file VARCHAR(255),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE disposisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surat_masuk_id INT,
    penerima_id INT,
    instruksi TEXT,
    batas_waktu DATE,
    catatan TEXT,
    status ENUM('tertunda', 'selesai') DEFAULT 'tertunda',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (surat_masuk_id) REFERENCES surat_masuk(id) ON DELETE CASCADE,
    FOREIGN KEY (penerima_id) REFERENCES pengguna(id)
);

CREATE TABLE pengajuan_warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_registrasi VARCHAR(50) NOT NULL UNIQUE,
    nik VARCHAR(20) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    jenis_surat_id INT,
    deskripsi TEXT,
    status ENUM('baru', 'diproses', 'selesai', 'ditolak') DEFAULT 'baru',
    file_hasil VARCHAR(255),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jenis_surat_id) REFERENCES jenis_surat(id)
);

CREATE TABLE lampiran_pengajuan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pengajuan_id INT,
    path_file VARCHAR(255),
    tipe_file VARCHAR(50),
    FOREIGN KEY (pengajuan_id) REFERENCES pengajuan_warga(id) ON DELETE CASCADE
);

CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pengguna_id INT,
    aksi VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    alamat_ip VARCHAR(45),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna(id)
);

-- Insert default roles
INSERT INTO peran (nama) VALUES ('Admin'), ('Kades'), ('Staf');

-- Insert default letter types (jenis_surat)
INSERT INTO jenis_surat (nama, kode) VALUES 
('Surat Keterangan Kematian', 'SKM'),
('Surat Keterangan Usaha', 'SKU'),
('Surat Keterangan Domisili', 'SKD'),
('Surat Keterangan Tidak Mampu', 'SKTM'),
('Surat Keterangan Belum Menikah', 'SKBM'),
('Surat Pengantar SKCK', 'SKCK'),
('Surat Izin Keramaian', 'SKIK'),
('Surat Keterangan Pindah', 'SKP'),
('Surat Keterangan Kelakuan Baik', 'SKKB'),
('Surat Domisili Lembaga', 'SKDL');

-- Insert default admin user (password: admin123)
INSERT INTO pengguna (username, password, nama_lengkap, peran_id) VALUES ('admin', '$2y$12$yc6Zd/0MFYKY6dHKCT4sg.bfw9M2g/6XEM.dUjo.qgTI/9qKLPnMS', 'Administrator', 1);

-- Insert default village profile
INSERT INTO profil_desa (nama, alamat, telepon, email, nama_kades) VALUES ('Desa Pendua', 'Jl. Raya Pendua No. 01, Kayangan, Kabupaten Lombok Utara, NTB 83353', '081234567890', 'kontak@pendua.desa.id', 'Abu Bakar, S.Adm');

CREATE TABLE penduduk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(20) NOT NULL UNIQUE,
    no_kk VARCHAR(20),
    nama_lengkap VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    agama VARCHAR(30) DEFAULT 'Islam',
    pendidikan VARCHAR(50),
    pekerjaan VARCHAR(100),
    status_perkawinan VARCHAR(50) DEFAULT 'Belum Kawin',
    kewarganegaraan VARCHAR(10) DEFAULT 'WNI',
    alamat TEXT,
    rt VARCHAR(5) DEFAULT '001',
    rw VARCHAR(5) DEFAULT '001',
    telepon VARCHAR(20),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample penduduk
INSERT INTO penduduk (nik, no_kk, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, pendidikan, pekerjaan, status_perkawinan, kewarganegaraan, alamat, rt, rw, telepon) VALUES
('5208010101900001', '5208010101900000', 'Ahmad Dahlan', 'Laki-laki', 'Lombok Utara', '1990-01-01', 'Islam', 'S1', 'Wiraswasta', 'Kawin', 'WNI', 'Dusun Karang Baru, RT 01 RW 01, Desa Pendua', '001', '001', '081234567891'),
('5208014505950002', '5208010101900000', 'Siti Fatimah', 'Perempuan', 'Mataram', '1995-05-15', 'Islam', 'SMA/Sederajat', 'Mengurus Rumah Tangga', 'Kawin', 'WNI', 'Dusun Karang Baru, RT 01 RW 01, Desa Pendua', '001', '001', '081234567892'),
('5208011212000003', '5208011212000000', 'Budi Santoso', 'Laki-laki', 'Lombok Utara', '2000-12-12', 'Islam', 'D3', 'Karyawan Swasta', 'Belum Kawin', 'WNI', 'Dusun Dasan Makmur, RT 02 RW 01, Desa Pendua', '002', '001', '081234567893');
