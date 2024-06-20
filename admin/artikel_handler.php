<?php
session_start();
include 'connection.php'; // Sesuaikan dengan file koneksi Anda

// Pastikan form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $judul = $_POST['Judul'];
    $penulis = $_POST['penulis'];
    $kategori = $_POST['kategori'];
    $isi = $_POST['isi'];

    // Handle file upload
    $gambar = null;
    if ($_FILES['gambar']['size'] > 0) {
        $target_dir = "gambar/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File bukan gambar.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "File sudah ada.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["gambar"]["size"] > 500000) {
            echo "Ukuran file terlalu besar.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            echo "Hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "File tidak diunggah.";
        } else {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;
            } else {
                echo "Terjadi kesalahan saat mengunggah file.";
                $gambar = null;
            }
        }
    }

    // Insert artikel into database
    $query = "INSERT INTO artikel (tanggal, judul, penulis, kategori, isi, gambar) 
              VALUES ('$tanggal', '$judul', '$penulis', '$kategori', '$isi', '$gambar')";

    if (mysqli_query($koneksi, $query)) {
        echo "Artikel berhasil disimpan.";
        // Redirect or do something else after successful save
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
} else {
    echo "Invalid request.";
}
?>
