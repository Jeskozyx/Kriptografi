<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kripto";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi untuk hash password dengan Argon2
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID);
}

// Fungsi untuk verifikasi password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>