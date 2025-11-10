<?php
include '../config/config.php';

// === Fungsi Rail Fence ===
function railFenceDecrypt($cipher, $key) {
    $rail = array_fill(0, $key, array_fill(0, strlen($cipher), '\n'));
    $dirDown = null;
    $row = 0; $col = 0;

    // Tandai posisi zigzag
    for ($i = 0; $i < strlen($cipher); $i++) {
        if ($row == 0) $dirDown = true;
        if ($row == $key - 1) $dirDown = false;
        $rail[$row][$col++] = '*';
        $row += $dirDown ? 1 : -1;
    }

    // Masukkan karakter cipher ke posisi bintang
    $index = 0;
    for ($i = 0; $i < $key; $i++) {
        for ($j = 0; $j < strlen($cipher); $j++) {
            if ($rail[$i][$j] == '*' && $index < strlen($cipher)) {
                $rail[$i][$j] = $cipher[$index++];
            }
        }
    }

    // Baca zigzag lagi untuk ambil plaintext
    $result = '';
    $row = 0; $col = 0;
    for ($i = 0; $i < strlen($cipher); $i++) {
        if ($row == 0) $dirDown = true;
        if ($row == $key - 1) $dirDown = false;
        $result .= $rail[$row][$col++];
        $row += $dirDown ? 1 : -1;
    }
    return $result;
}

// === Fungsi XOR ===
function xorDecrypt($text, $key) {
    $output = '';
    for ($i = 0; $i < strlen($text); $i++) {
        $output .= chr(ord($text[$i]) ^ ord($key[$i % strlen($key)]));
    }
    return $output;
}

$hasilDekripsi = "";
$judulDipilih = "";

// Ambil semua data untuk dropdown
$data = mysqli_query($conn, "SELECT id, judul FROM teks_super ORDER BY id DESC");

if (isset($_POST['id_teks'])) {
    $id = $_POST['id_teks'];
    $get = mysqli_query($conn, "SELECT * FROM teks_super WHERE id = $id");
    $row = mysqli_fetch_assoc($get);
    $judulDipilih = $row['judul'];

    $cipher_hex = $row['hasil_enkripsi'];
    $cipher = hex2bin($cipher_hex);

    $xorKey = "K";
    $railKey = 3;

    // 1️⃣ XOR balik
    $afterXor = xorDecrypt($cipher, $xorKey);

    // 2️⃣ Rail Fence balik
    $hasilDekripsi = railFenceDecrypt($afterXor, $railKey);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dekripsi Teks Super</title>
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
select, button {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 10px;
  margin-bottom: 15px;
}
button {
  background: #0066ff;
  color: white;
  border: none;
  cursor: pointer;
  font-size: 16px;
}
button:hover { background: #004ecc; }
.result {
  background: #f2f5ff;
  padding: 15px;
  border-radius: 10px;
  font-family: monospace;
  word-wrap: break-word;
}
.loading {
  text-align: center;
  color: #666;
  font-style: italic;
}
</style>
</head>
<body>
<div class="container">
<h2>🔓 Dekripsi Teks Super</h2>
<form method="POST" action="">
  <select name="id_teks" required>
    <option value="">-- Pilih Data untuk Dekripsi --</option>
    <?php while ($r = mysqli_fetch_assoc($data)): ?>
      <option value="<?= $r['id'] ?>" <?= ($r['id']==($_POST['id_teks']??''))?'selected':'' ?>>
        <?= $r['id']." - ".htmlspecialchars($r['judul']) ?>
      </option>
    <?php endwhile; ?>
  </select>
  <button type="submit">Dekripsi Sekarang</button>
</form>

<?php if ($hasilDekripsi): ?>
  <div class="result">
    <strong>Judul:</strong> <?= htmlspecialchars($judulDipilih) ?><br><br>
    <strong>Hasil Dekripsi:</strong><br><?= htmlspecialchars($hasilDekripsi) ?>
  </div>
<?php elseif (isset($_POST['id_teks'])): ?>
  <div class="loading">⏳ Memproses dekripsi...</div>
<?php endif; ?>

<a href="../app/index.php" style="display:block;margin-top:20px;text-align:center;">← Kembali</a>
</div>
</body>
</html>
