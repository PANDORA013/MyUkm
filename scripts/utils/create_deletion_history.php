<?php

// Script untuk menambah riwayat penghapusan user

// Tentukan data user yang sudah dihapus
$userId = 153;
$userName = "Admin website";
$userNim = "admin001";
$userEmail = "-";
$userRole = "admin_website";
$adminId = 1; // ID admin yang melakukan penghapusan

// Koneksi ke database (ganti dengan konfigurasi database Anda)
$host = "localhost";
$dbname = "myukm"; // Sesuaikan dengan nama database Anda
$username = "root"; // Sesuaikan dengan username database Anda
$password = ""; // Sesuaikan dengan password database Anda

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== MENAMBAHKAN RIWAYAT PENGHAPUSAN ===" . PHP_EOL;
    
    // Nonaktifkan foreign key checks sementara
    $conn->exec('SET FOREIGN_KEY_CHECKS=0');
    
    // Siapkan dan jalankan query untuk menambah riwayat penghapusan
    $sql = "INSERT INTO user_deletion_histories (user_id, user_name, user_nim, user_email, user_role, reason, deleted_by, created_at, updated_at) 
            VALUES (:user_id, :user_name, :user_nim, :user_email, :user_role, :reason, :deleted_by, NOW(), NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':user_name', $userName);
    $stmt->bindParam(':user_nim', $userNim);
    $stmt->bindParam(':user_email', $userEmail);
    $stmt->bindParam(':user_role', $userRole);
    $reason = "Dihapus secara manual dari phpMyAdmin";
    $stmt->bindParam(':reason', $reason);
    $stmt->bindParam(':deleted_by', $adminId);
    
    $stmt->execute();
    
    echo "Riwayat penghapusan berhasil dicatat untuk user: $userName ($userNim)" . PHP_EOL;
    echo "Waktu penghapusan: " . date('Y-m-d H:i:s') . PHP_EOL;
    
    // Aktifkan kembali foreign key checks
    $conn->exec('SET FOREIGN_KEY_CHECKS=1');
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

$conn = null;
