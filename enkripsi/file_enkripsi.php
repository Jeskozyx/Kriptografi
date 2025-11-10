<?php
include '../config/config.php';

// 🧩 Validasi input
if (empty($_FILES['file']['tmp_name']) || empty($_POST['kunci'])) {
    echo "❌ Gagal melakukan enkripsi. Pastikan file dan kunci sudah diisi.";
    exit;
}

$file  = $_FILES['file'];
$kunci = $_POST['kunci'];
$judul = isset($_POST['judul']) ? trim($_POST['judul']) : '';
if ($judul === '') {
    // fallback agar kolom tidak NULL/empty; gunakan nama file tanpa ekstensi atau teks default
    $judul = 'Tanpa Judul';
}

// 📁 Tentukan folder output
$target_dir = "../hasil_enkripsi/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// 📁 Simpan salinan file asli untuk pencatatan
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

// 🐍 Jalankan Python untuk enkripsi
$python = escapeshellcmd("python");
$script = escapeshellarg(realpath(__DIR__ . "/../python/enkripsi_file.py"));
$arg_in = escapeshellarg($path_asli);      // input dari file asli yang disimpan
$arg_out = escapeshellarg($path_enkripsi); // hasil enkripsi
$arg_key = escapeshellarg($kunci);
$command = "$python $script $arg_in $arg_out $arg_key 2>&1";

// 🧠 Jalankan enkripsi
$output = shell_exec($command);

// 🧾 Cek hasil
if (!file_exists($path_enkripsi)) {
    echo "<h3>❌ Gagal melakukan enkripsi.</h3>";
    echo "<pre><b>Command:</b> $command\n\n<b>Output Python:</b>\n$output</pre>";
    exit;
}

// 💾 Simpan info ke database (lengkap: judul, nama_file, path_asli, path_enkripsi, kunci)
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
