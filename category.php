<?php
session_start();
include 'admin/connection.php';

// Ambil id kategori dari parameter GET
$id_kategori = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validasi id_kategori
if ($id_kategori <= 0) {
    die('Invalid category ID');
}

// Konfigurasi paginasi
$artikel_per_halaman = 3;
$halaman_sekarang = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman_sekarang - 1) * $artikel_per_halaman;

// Query untuk mengambil data artikel berdasarkan kategori
$query = "SELECT * FROM artikel WHERE kategori = $id_kategori ORDER BY tanggal DESC LIMIT $mulai, $artikel_per_halaman";
$article_result = mysqli_query($koneksi, $query);

if (!$article_result) {
    die('Error: ' . mysqli_error($koneksi));
}

// Query untuk menghitung total artikel dalam kategori
$total_artikel_query = "SELECT COUNT(*) AS total FROM artikel WHERE kategori = $id_kategori";
$total_artikel_result = mysqli_query($koneksi, $total_artikel_query);
$total_artikel = mysqli_fetch_assoc($total_artikel_result)['total'];
$total_halaman = ceil($total_artikel / $artikel_per_halaman);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Categories - Vava's Journey</title>
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Page header with logo and tagline-->
    <header class="py-5 bg-light border-bottom mb-4">
        <div class="container">
            <div class="text-center my-5">
                <h1 class="fw-bolder">Categories: 
                    <?php
                        $category_name_query = "SELECT nama_kategori FROM kategori WHERE id_kategori = $id_kategori";
                        $category_name_result = mysqli_query($koneksi, $category_name_query);
                        $category_name = mysqli_fetch_assoc($category_name_result)['nama_kategori'];
                        echo htmlspecialchars($category_name);
                    ?>
                </h1>
            </div>
        </div>
    </header>
    <!-- Page content-->
    <div class="container">
        <div class="row">
            <!-- Blog entries-->
            <div class="col-lg-8">
                <!-- Featured blog post-->
                <?php
                // Menampilkan artikel
                if (mysqli_num_rows($article_result) > 0) {
                    while ($row = mysqli_fetch_assoc($article_result)) {
                        echo '<div class="card mb-4">';
                        echo '<a href="detail.php?id=' . $row['id_artikel'] . '"><img class="card-img-top" src="admin/' . $row['gambar'] . '" alt="' . htmlspecialchars($row['judul']) . '" /></a>';
                        echo '<div class="card-body">';
                        echo '<div class="small text-muted">' . date("F d, Y", strtotime($row['tanggal'])) . '</div>';
                        echo '<h2 class="card-title">' . htmlspecialchars($row['judul']) . '</h2>';
                        echo '<p class="card-text">' . htmlspecialchars(substr($row['isi'], 0, 150)) . '...</p>';
                        echo '<a class="btn btn-primary" href="detail.php?id=' . $row['id_artikel'] . '">Read more →</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No articles found in this category</p>';
                }
                ?>
                <!-- Pagination-->
                <nav aria-label="Pagination">
                    <ul class="pagination justify-content-center my-4">
                        <?php if ($halaman_sekarang > 1) : ?>
                            <li class="page-item"><a class="page-link" href="?id=<?= $id_kategori ?>&halaman=<?= $halaman_sekarang - 1; ?>">Previous</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_halaman; $i++) : ?>
                            <li class="page-item <?= $i == $halaman_sekarang ? 'active' : ''; ?>"><a class="page-link" href="?id=<?= $id_kategori ?>&halaman=<?= $i; ?>"><?= $i; ?></a></li>
                        <?php endfor; ?>

                        <?php if ($halaman_sekarang < $total_halaman) : ?>
                            <li class="page-item"><a class="page-link" href="?id=<?= $id_kategori ?>&halaman=<?= $halaman_sekarang + 1; ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <!-- Side widgets-->
            <div class="col-lg-4">
                <!-- Categories widget-->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Categories</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="list-group">
                                    <?php
                                    $category_query = "SELECT * FROM kategori";
                                    $category_result = mysqli_query($koneksi, $category_query);
                                    if (mysqli_num_rows($category_result) > 0) {
                                        while ($row_kategori = mysqli_fetch_assoc($category_result)) {
                                            echo '<a href="category.php?id=' . $row_kategori['id_kategori'] . '" class="list-group-item list-group-item-action">';
                                            echo '<i class="bi bi-tag-fill text-primary"></i> ' . htmlspecialchars($row_kategori['nama_kategori']);
                                            echo '</a>';
                                        }
                                    } else {
                                        echo '<a href="#" class="list-group-item list-group-item-action disabled" aria-disabled="true">No categories found</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- About widget-->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">About</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">This is a simple blog application built with PHP and MySQL to showcase articles based on different categories.</p>
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
