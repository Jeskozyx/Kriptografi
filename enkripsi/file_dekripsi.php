<?php
include '../config/config.php';

// 🧩 Validasi input kunci
$kunci = isset($_POST['kunci']) ? $_POST['kunci'] : '';
if (empty($kunci)) {
    echo "❌ Gagal melakukan dekripsi. Kunci wajib diisi.";
    exit;
}

// Tentukan sumber file terenkripsi
$path_enkripsi = null;

// Mode 1: File diupload langsung
if (isset($_FILES['file_terenkripsi']) && isset($_FILES['file_terenkripsi']['tmp_name']) && $_FILES['file_terenkripsi']['error'] === 0) {
    $upload_dir = "../hasil_enkripsi/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $uploaded_name = basename($_FILES['file_terenkripsi']['name']);
    $dest_path = $upload_dir . $uploaded_name;
    if (!move_uploaded_file($_FILES['file_terenkripsi']['tmp_name'], $dest_path)) {
        echo "❌ Gagal mengunggah file terenkripsi.";
        exit;
    }

    $path_enkripsi = realpath($dest_path);
}

// Mode 2: Berdasarkan pilihan dropdown (ID)
if (!$path_enkripsi && !empty($_POST['id_judul'])) {
    $id_terpilih = (int) $_POST['id_judul'];
    $result = $conn->query("SELECT * FROM file_rahasia WHERE id = $id_terpilih LIMIT 1");
    if (!$result || $result->num_rows == 0) {
        echo "❌ File dengan ID tersebut tidak ditemukan di database.";
        exit;
    }
    $data = $result->fetch_assoc();
    $path_enkripsi = realpath($data['path_enkripsi']);
}

// Tutup koneksi DB
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

// Validasi akhir
if (!$path_enkripsi || !file_exists($path_enkripsi)) {
    echo "❌ File terenkripsi tidak ditemukan.";
    exit;
}

// 📄 Buat path hasil dekripsi
$basename = basename($path_enkripsi);
$target_dir = "../hasil_dekripsi/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}
$path_dekripsi = $target_dir . "dec_" . preg_replace('/^enc_/', '', $basename);


// --- PERBAIKAN: MENANGANI KUNCI PANJANG ---

// 1. Buat file temporary untuk menampung kunci
$temp_key_file = tempnam(sys_get_temp_dir(), 'key_dec_');

// 2. Tulis kunci ke file tersebut
if (file_put_contents($temp_key_file, $kunci) === false) {
    echo "❌ Gagal menulis kunci ke file sementara.";
    exit;
}

// 3. Setup Command Python
$python = escapeshellcmd("python");
$script = escapeshellarg(realpath(__DIR__ . "/../python/dekripsi_file.py"));
$arg_in = escapeshellarg($path_enkripsi);
$arg_out = escapeshellarg($path_dekripsi);
$arg_key_path = escapeshellarg($temp_key_file); // Kirim PATH file, bukan isinya

// Gabungkan command
$command = "$python $script $arg_in $arg_out $arg_key_path 2>&1";

// 🧠 Jalankan command
$output = shell_exec($command);

// 4. Hapus file kunci sementara (Cleanup)
if (file_exists($temp_key_file)) {
    unlink($temp_key_file);
}

// --- END PERBAIKAN ---


// 🧾 Cek hasil
if (!file_exists($path_dekripsi)) {
    echo "<h3>❌ Gagal melakukan dekripsi.</h3>";
    echo "<pre><b>Command:</b> $command\n\n<b>Output Python:</b>\n" . htmlspecialchars($output) . "</pre>";
} else {
    // Tampilkan hasil
    $relative_output = str_replace(realpath(__DIR__ . '/../'), '../', $path_dekripsi);
    echo "<h3>✅ File berhasil didekripsi!</h3>";
    echo "<a href='$relative_output' download>⬇️ Download hasil dekripsi</a><br><br>";
    echo "<a href='../app/index.php'>⬅️ Kembali ke beranda</a>";
}
?>