<?php
session_start();
include 'admin/connection.php';

// Ambil id artikel dari parameter GET
$id_artikel = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query untuk mengambil data artikel berdasarkan id
$query = "SELECT * FROM artikel WHERE id_artikel = $id_artikel";
$result = mysqli_query($koneksi, $query);

// Cek apakah artikel ditemukan
if (mysqli_num_rows($result) > 0) {
    $artikel = mysqli_fetch_assoc($result);
    $judul = $artikel['judul'];
    $penulis = $artikel['penulis'];
    $kategori_id = $artikel['kategori'];
    $isi = $artikel['isi'];
    $gambar = $artikel['gambar'];
    $tanggal = date("F d, Y", strtotime($artikel['tanggal']));

    // Query untuk mendapatkan nama kategori
    $kategori_query = "SELECT nama_kategori FROM kategori WHERE id_kategori = $kategori_id";
    $kategori_result = mysqli_query($koneksi, $kategori_query);
    $kategori = mysqli_fetch_assoc($kategori_result)['nama_kategori'];
} else {
    // Jika artikel tidak ditemukan
    $judul = "Artikel tidak ditemukan";
    $penulis = "";
    $kategori = "";
    $isi = "";
    $gambar = "";
    $tanggal = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $judul ?></title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Vava's Journey</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Page content-->
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Post content-->
                <article>
                    <!-- Post header-->
                    <header class="mb-4">
                        <!-- Post title-->
                        <h1 class="fw-bolder mb-1"><?= $judul ?></h1>
                        <!-- Post meta content-->
                        <div class="text-muted fst-italic mb-2">Posted on <?= $tanggal ?> by <?= $penulis ?></div>
                        <!-- Post categories-->
                        <a class="badge bg-secondary text-decoration-none link-light" href="#!"><?= $kategori ?></a>
                    </header>
                    <!-- Preview image figure-->
                    <figure class="mb-4"><img class="img-fluid rounded" src="admin/<?= $gambar ?>" alt="<?= $judul ?>" /></figure>
                    <!-- Post content-->
                    <section class="mb-5">
                        <p class="fs-5 mb-4"><?= $isi ?></p>
                        <a href="javascript:history.back()" class="btn btn-primary">Back</a>
                    </section>
                </article>
            </div>
            <div class="col-lg-4">
                <!-- Categories widget-->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Artikel Terkait</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="list-group">
                                    <?php
                                    // Query untuk mendapatkan artikel terkait dengan kategori yang sama
                                    $related_query = "SELECT * FROM artikel WHERE kategori = $kategori_id AND id_artikel != $id_artikel LIMIT 5";
                                    $related_result = mysqli_query($koneksi, $related_query);
                                    if (mysqli_num_rows($related_result) > 0) {
                                        while ($row_artikel = mysqli_fetch_assoc($related_result)) {
                                            echo '<a href="detail.php?id=' . $row_artikel['id_artikel'] . '" class="list-group-item list-group-item-action">';
                                            echo '<h6 class="mb-1">' . $row_artikel['judul'] . '</h6>';
                                            echo '<div class="text-muted fst-italic">' . date("F d, Y", strtotime($row_artikel['tanggal'])) . '</div>';
                                            echo '</a>';
                                        }
                                    } else {
                                        echo '<a href="#" class="list-group-item list-group-item-action disabled" aria-disabled="true">No related articles found</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Your Website 2023</p>
        </div>
    </footer>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
</body>

</html>