<?php
// delete_artikel.php

// Include file koneksi ke database
include 'connection.php';

// Ambil ID artikel dari data POST
$id_artikel = $_POST['id_artikel'];

// Query untuk mendapatkan path gambar yang terkait dengan artikel
$query_get_gambar = "SELECT gambar FROM artikel WHERE id_artikel = $id_artikel";
$result_get_gambar = mysqli_query($koneksi, $query_get_gambar);

if (mysqli_num_rows($result_get_gambar) > 0) {
    $row = mysqli_fetch_assoc($result_get_gambar);
    $gambar_path = $row['gambar'];

    // Hapus gambar dari direktori jika gambar tersedia
    if ($gambar_path && file_exists($gambar_path)) {
        unlink($gambar_path);
    }
}

// Query untuk menghapus artikel berdasarkan ID
$query = "DELETE FROM artikel WHERE id_artikel = $id_artikel";

if (mysqli_query($koneksi, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete article']);
}
?>
