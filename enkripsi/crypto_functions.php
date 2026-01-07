<?php
// crypto_functions.php

function lsb_hide_message($image_path, $secret_message) {
    $python_script = __DIR__ . '/../python/lsb_tool.py';
    $output_dir = rtrim(__DIR__ . '/uploads', '/\\'); 
    
    if (!is_dir($output_dir)) {
        mkdir($output_dir, 0777, true);
    }
    
    if (trim($secret_message) === '') {
        return ['status' => 'gagal', 'message' => 'Pesan rahasia tidak boleh kosong.'];
    }

    // --- PERUBAHAN UTAMA DI SINI ---
    
    // 1. Buat file temporary untuk menampung pesan panjang
    $temp_msg_file = tempnam(sys_get_temp_dir(), 'stego_msg_');
    
    // 2. Tulis pesan rahasia ke file tersebut (Apa adanya, termasuk enter/spasi)
    if (file_put_contents($temp_msg_file, $secret_message) === false) {
        return ['status' => 'gagal', 'message' => 'Gagal menulis pesan ke file sementara.'];
    }

    // 3. Escape path script dan path file
    $script_escaped = escapeshellarg($python_script);
    $image_path_escaped = escapeshellarg($image_path);
    $msg_file_path_escaped = escapeshellarg($temp_msg_file); // Kirim PATH file, bukan isinya
    $output_dir_escaped = escapeshellarg($output_dir);
    
    // 4. Perintah Python sekarang menerima path file pesan
    $command = "python $script_escaped encode $image_path_escaped $msg_file_path_escaped $output_dir_escaped 2>&1";
    
    // Eksekusi
    error_log("LSB Command: " . $command);
    $output = shell_exec($command);
    error_log("LSB Output: " . $output);
    
    // 5. Hapus file temporary pesan setelah selesai (Cleanup)
    if (file_exists($temp_msg_file)) {
        unlink($temp_msg_file);
    }
    
    // --- END PERUBAHAN ---

    // Parse hasil (Tetap sama)
    if (strpos($output, 'SUCCESS:') !== false) {
        preg_match('/SUCCESS:(.+?)$/m', $output, $matches);
        if (isset($matches[1])) {
            $full_path = trim($matches[1]);
            $filename = basename($full_path);
            return [
                'status' => 'sukses',
                'file' => 'uploads/' . $filename,
                'filename' => $filename
            ];
        }
    }
    
    // Handle error kapasitas dari Python
    if (strpos($output, 'Kapasitas maks') !== false) {
        return [
            'status' => 'gagal',
            'message' => 'Gambar terlalu kecil untuk pesan sepanjang ini. Gunakan gambar resolusi lebih tinggi.'
        ];
    }

    return [
        'status' => 'gagal',
        'message' => $output ? trim($output) : 'Terjadi kesalahan tidak diketahui.'
    ];
}

function lsb_extract_message($image_path) {
    // Fungsi ekstrak tetap sama, karena outputnya dicetak ke stdout (layar)
    // Python sudah handle print pesan panjang
    $python_script = __DIR__ . '/../python/lsb_tool.py';
    $script_escaped = escapeshellarg($python_script);
    $image_path_escaped = escapeshellarg($image_path);
    
    $command = "python $script_escaped decode $image_path_escaped 2>&1";
    $output = shell_exec($command);
    
    if (strpos($output, 'EXTRACTED:') !== false) {
        // Ambil string setelah "EXTRACTED:"
        // Kita gunakan substring karena pesan mungkin mengandung karakter aneh/newline
        $prefix = "EXTRACTED:";
        $pos = strpos($output, $prefix);
        if ($pos !== false) {
            return substr($output, $pos + strlen($prefix));
        }
    }
    
    return "Gagal mengekstrak pesan. Debug: " . $output;
}

// Fungsi dummy lain tetap...
function encrypt_idea($message, $key = null) { return $message; }
function decrypt_idea($encrypted_message, $key = null) { return $encrypted_message; }
?>