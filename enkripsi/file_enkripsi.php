<?php
include '../config/config.php';

// 🧩 Validasi input
if (empty($_FILES['file']['tmp_name']) || empty($_POST['kunci'])) {
    echo "❌ Gagal melakukan enkripsi. Pastikan file dan kunci sudah diisi.";
    exit;
}

$file  = $_FILES['file'];
$kunci = $_POST['kunci']; // Kunci yang sangat panjang
$judul = isset($_POST['judul']) ? trim($_POST['judul']) : '';
if ($judul === '') {
    $judul = 'Tanpa Judul';
}

// 📁 Tentukan folder output
$target_dir = "../hasil_enkripsi/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// 📁 Simpan salinan file asli
$upload_asli_dir = "../uploads_asli/";
if (!is_dir($upload_asli_dir)) {
    mkdir($upload_asli_dir, 0777, true);
}

$nama_file = basename($file['name']);
$path_asli = $upload_asli_dir . $nama_file;
if (!move_uploaded_file($file['tmp_name'], $path_asli)) {
    echo "❌ Gagal menyimpan file asli.";
    exit;
}

$path_enkripsi = $target_dir . "enc_" . $nama_file;

// --- PERBAIKAN: MENANGANI KUNCI PANJANG ---

// 1. Buat file temporary untuk menampung kunci
$temp_key_file = tempnam(sys_get_temp_dir(), 'key_enc_');

// 2. Tulis isi kunci ke file tersebut
if (file_put_contents($temp_key_file, $kunci) === false) {
    echo "❌ Gagal menulis kunci ke file sementara.";
    exit;
}

// 3. Siapkan Argument untuk Python
$python = escapeshellcmd("python");
$script = escapeshellarg(realpath(__DIR__ . "/../python/enkripsi_file.py"));
$arg_in = escapeshellarg($path_asli);      
$arg_out = escapeshellarg($path_enkripsi); 
$arg_key_path = escapeshellarg($temp_key_file); // Kirim PATH file kunci, bukan isinya

// 4. Perintah sekarang mengirim path file kunci
$command = "$python $script $arg_in $arg_out $arg_key_path 2>&1";

// 🧠 Jalankan enkripsi
$output = shell_exec($command);

// 5. Hapus file kunci sementara (Wajib demi keamanan & kebersihan)
if (file_exists($temp_key_file)) {
    unlink($temp_key_file);
}

// --- END PERBAIKAN ---

// 🧾 Cek hasil
if (!file_exists($path_enkripsi)) {
    echo "<h3>❌ Gagal melakukan enkripsi.</h3>";
    echo "<pre><b>Command:</b> $command\n\n<b>Output Python:</b>\n$output</pre>";
    exit;
}

// 💾 Simpan info ke database
// Catatan: Kita simpan kunci asli ke database. 
// PERINGATAN: Jika kuncinya sangat besar, pastikan kolom 'kunci' di database bertipe TEXT atau LONGTEXT.
$stmt = $conn->prepare("INSERT INTO file_rahasia (judul, nama_file, path_asli, path_enkripsi, kunci) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo "❌ Gagal menyiapkan query: " . htmlspecialchars($conn->error);
    exit;
}
$stmt->bind_param("sssss", $judul, $nama_file, $path_asli, $path_enkripsi, $kunci);
$stmt->execute();
$stmt->close();
$conn->close();

// ✅ Tampilkan hasil
$relative_output = str_replace(realpath(__DIR__ . '/../'), '../', realpath($path_enkripsi));
echo "<h3>✅ File berhasil dienkripsi!</h3>";
echo "<a href='$relative_output' download>⬇️ Download file terenkripsi</a><br><br>";
echo "<a href='../app/index.php'>⬅️ Kembali ke beranda</a>";
?>