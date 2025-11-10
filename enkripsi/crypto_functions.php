<?php
// crypto_functions.php

function lsb_hide_message($image_path, $secret_message) {
    $python_script = __DIR__ . '/../python/lsb_tool.py';
    $output_dir = __DIR__ . '/uploads/';
    
    // Pastikan direktori upload ada
    if (!is_dir($output_dir)) {
        mkdir($output_dir, 0777, true);
    }
    
    // Escape path dan pesan untuk keamanan
    $image_path_escaped = escapeshellarg($image_path);
    $secret_message_escaped = escapeshellarg($secret_message);
    $output_dir_escaped = escapeshellarg($output_dir);
    
    // Perintah Python
    $command = "python " . escapeshellarg($python_script) . " encode " . $image_path_escaped . " " . $secret_message_escaped . " " . $output_dir_escaped . " 2>&1";
    
    // Eksekusi perintah
    $output = shell_exec($command);
    
    // Debug: Tampilkan output untuk troubleshooting
    error_log("LSB Hide Output: " . $output);
    
    // Parse hasil
    if (strpos($output, 'SUCCESS:') !== false) {
        // Ekstrak path dari output
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
    
    return [
        'status' => 'gagal',
        'message' => $output ?: 'Unknown error occurred'
    ];
}

function lsb_extract_message($image_path) {
    $python_script = __DIR__ . '/../python/lsb_tool.py';
    
    // Escape path untuk keamanan
    $image_path_escaped = escapeshellarg($image_path);
    
    // Perintah Python
    $command = "python " . escapeshellarg($python_script) . " decode " . $image_path_escaped . " 2>&1";
    
    // Eksekusi perintah
    $output = shell_exec($command);
    
    // Debug: Tampilkan output untuk troubleshooting
    error_log("LSB Extract Output: " . $output);
    
    // Parse hasil
    if (strpos($output, 'EXTRACTED:') !== false) {
        preg_match('/EXTRACTED:(.+?)$/m', $output, $matches);
        if (isset($matches[1])) {
            return trim($matches[1]);
        }
    }
    
    return "Gagal mengekstrak pesan: " . $output;
}

// Fungsi enkripsi IDEA (jika diperlukan)
function encrypt_idea($message, $key = null) {
    // Implementasi enkripsi IDEA di PHP
    // Untuk sementara, return pesan asli
    return $message;
}

function decrypt_idea($encrypted_message, $key = null) {
    // Implementasi dekripsi IDEA di PHP  
    // Untuk sementara, return pesan asli
    return $encrypted_message;
}
?>