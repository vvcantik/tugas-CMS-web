<?php
session_start();
include 'connection.php';

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $username = $_GET['username'];
    $password = $_GET['password'];

    // Query untuk mencari user di database
    $sql = "SELECT * FROM penulis WHERE username = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika user ditemukan
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php"); // Redirect ke halaman utama setelah login
            exit();
        } else {
            echo "<script>alert('Invalid username or password');</script>";
            echo "<script>window.location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password');</script>";
        echo "<script>window.location.href = 'login.php';</script>";
    }
}
?>
