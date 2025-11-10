<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'], $_POST['key'])) {
    $file = $_FILES['file'];
    $key = $_POST['key'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("❌ Upload gagal. Error code: " . $file['error']);
    }

    $data = file_get_contents($file['tmp_name']);

    // Pastikan data tidak kosong
    if ($data === '' || $data === false) {
        die("❌ Gagal membaca file atau file kosong.");
    }
    
    // Pastikan libsodium ada
    if (!function_exists('sodium_crypto_stream_xor')) {
        die("❌ Error: Ekstensi PHP 'libsodium' tidak diaktifkan di server.");
    }

    // Buat nonce dan key 32 byte
    $nonce = random_bytes(SODIUM_CRYPTO_STREAM_NONCEBYTES);
    $key32 = sodium_crypto_generichash($key, '', SODIUM_CRYPTO_STREAM_KEYBYTES);

    // Enkripsi (Salsa20 stream XOR)
    $ciphertext = sodium_crypto_stream_xor($data, $nonce, $key32);

    // Simpan dalam format JSON
    $payload = [
        'filename' => $file['name'],
        'mime' => mime_content_type($file['tmp_name']),
        'nonce' => base64_encode($nonce),
        'ciphertext' => base64_encode($ciphertext),
        'created_at' => date('c'),
        'tool' => 'Salsa20-PHP-v1'
    ];

    $json = json_encode($payload, JSON_PRETTY_PRINT);

    $outName = $file['name'] . '.enc';
    $tempFile = tempnam(sys_get_temp_dir(), 'enc');
    file_put_contents($tempFile, $json);

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . basename($outName) . '"');
    header('Content-Length: ' . filesize($tempFile));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    readfile($tempFile);
    unlink($tempFile); // Hapus file sementara
    exit;
}
?>