<?php
include '../config/config.php';

// === Fungsi Rail Fence ===
function railFenceEncrypt($text, $key) {
    $rail = array_fill(0, $key, []);
    $dirDown = false;
    $row = 0;

    for ($i = 0; $i < strlen($text); $i++) {
        $rail[$row][] = $text[$i];
        if ($row == 0 || $row == $key - 1) $dirDown = !$dirDown;
        $row += $dirDown ? 1 : -1;
    }

    $result = "";
    foreach ($rail as $r) $result .= implode("", $r);
    return $result;
}

// === Fungsi XOR ===
function xorEncrypt($text, $key) {
    $output = '';
    for ($i = 0; $i < strlen($text); $i++) {
        $output .= chr(ord($text[$i]) ^ ord($key[$i % strlen($key)]));
    }
    return $output;
}

$hasil_enkripsi = "";

if (isset($_POST['teks']) && isset($_POST['judul'])) {
    $judul = $_POST['judul'];
    $teks = $_POST['teks'];
    $railKey = 3;
    $xorKey = "K";

    $rfEncrypted = railFenceEncrypt($teks, $railKey);
    $finalCipher = xorEncrypt($rfEncrypted, $xorKey);
    $hasil_enkripsi = bin2hex($finalCipher);

    $query = "INSERT INTO teks_super (judul, hasil_enkripsi) VALUES ('$judul', '$hasil_enkripsi')";
    mysqli_query($conn, $query);

    if (!is_dir("../hasil_enkripsi")) mkdir("../hasil_enkripsi");
    $filename = "../hasil_enkripsi/" . time() . "_$judul.txt";
    file_put_contents($filename, $hasil_enkripsi);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Enkripsi</title>
<style>
body {
  font-family: "Poppins", sans-serif;
  background: #f6f8fa;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}
.container {
  background: #fff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
  width: 500px;
}
.result {
  background: #f2f5ff;
  padding: 15px;
  border-radius: 10px;
  font-family: monospace;
  word-wrap: break-word;
}
.btn {
  display: block;
  text-align: center;
  margin-top: 20px;
  background: #0066ff;
  color: white;
  text-decoration: none;
  padding: 10px;
  border-radius: 10px;
}
</style>
</head>
<body>
<div class="container">
<h2>✅ Enkripsi Berhasil</h2>
<p><strong>Judul:</strong> <?= htmlspecialchars($judul) ?></p>
<!-- <p><strong>Hasil Enkripsi (Hex):</strong></p>
<div class="result"><?= $hasil_enkripsi ?></div> -->
<a href="../app/index.php" class="btn">← Kembali</a>
</div>
</body>
</html>
