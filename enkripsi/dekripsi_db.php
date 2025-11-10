<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'], $_POST['key'])) {
    $file = $_FILES['file'];
    $key = $_POST['key'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("❌ Upload gagal. Error code: " . $file['error']);
    }

    $raw = file_get_contents($file['tmp_name']);
    $payload = json_decode($raw, true);

    if (!$payload || !isset($payload['nonce'], $payload['ciphertext'])) {
        die("❌ File bukan format .enc yang valid atau file korup.");
    }
    
    // Pastikan libsodium ada
    if (!function_exists('sodium_crypto_stream_xor')) {
        die("❌ Error: Ekstensi PHP 'libsodium' tidak diaktifkan di server.");
    }

    $nonce = base64_decode($payload['nonce']);
    $ciphertext = base64_decode($payload['ciphertext']);
    $key32 = sodium_crypto_generichash($key, '', SODIUM_CRYPTO_STREAM_KEYBYTES);

    // Dekripsi
    $plaintext = sodium_crypto_stream_xor($ciphertext, $nonce, $key32);

    // Pastikan hasil tidak kosong
    if ($plaintext === '' || $plaintext === false) {
        die("❌ Dekripsi gagal. Kemungkinan besar kunci salah.");
    }

    // Gunakan nama asli dari metadata
    $outName = $payload['filename'] ?? ('decrypted_' . $file['name']);
    if (str_ends_with($outName, '.enc')) {
        $outName = substr($outName, 0, -4);
    }
    
    $tempFile = tempnam(sys_get_temp_dir(), 'dec');
    file_put_contents($tempFile, $plaintext);

    header('Content-Type: ' . ($payload['mime'] ?? 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . basename($outName) . '"');
    header('Content-Length: ' . filesize($tempFile));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    readfile($tempFile);
    unlink($tempFile); // Hapus file sementara
    exit;
}
?>