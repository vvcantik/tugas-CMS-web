<?php
session_start();
include 'connection.php';

// Handle add new category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama_kategori = $_POST['nama_kategori'];
    $keterangan = $_POST['keterangan'];

    // Prepared statement untuk menghindari SQL Injection
    $stmt = $koneksi->prepare("INSERT INTO kategori (nama_kategori, keterangan) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_kategori, $keterangan);

    if ($stmt->execute()) {
        header('Location: kategori.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_kategori = $_POST['id_kategori'];
    $nama_kategori = $_POST['nama_kategori'];
    $keterangan = $_POST['keterangan'];

    $stmt = $koneksi->prepare("UPDATE kategori SET nama_kategori=?, keterangan=? WHERE id_kategori=?");
    $stmt->bind_param("ssi", $nama_kategori, $keterangan, $id_kategori);

    if ($stmt->execute()) {
        header('Location: kategori.php');
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id_kategori = $_GET['delete'];

    $stmt = $koneksi->prepare("DELETE FROM kategori WHERE id_kategori=?");
    $stmt->bind_param("i", $id_kategori);

    if ($stmt->execute()) {
        header('Location: kategori.php');
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch categories from the database
$query = "SELECT id_kategori, nama_kategori, keterangan FROM kategori";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kategori</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/dataTables/datatables.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/plugins/select2/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                    <span class="block m-t-xs">
                                        <strong class="font-bold"><?php echo $_SESSION['username']; ?></strong>
                                    </span>
                                    <span class="text-muted text-xs block">Author</span>
                                </span>
                            </a>
                        </div>
                        <div class="logo-element">VV</div>
                    </li>
                    <li><a href="dashboard.php"><i class="fa fa-th-large"></i><span class="nav-label">Dashboard</span></a></li>
                    <li><a href="artikel.php"><i class="bi bi-file-earmark-check-fill"></i><span class="nav-label">Artikel</span></a></li>
                    <li><a href="kategori.php"><i class="bi bi-bookmark-check-fill"></i><span class="nav-label">Kategori</span></a></li>
                    <li><a href="penulis.php"><i class="bi bi-person-square"></i><span class="nav-label">Penulis</span></a></li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li><span class="m-r-sm text-muted welcome-message">Welcome</span></li>
                        <li><a href="login.php">
                                <i class="fa fa-sign-out"></i> Log out
                            </a></li>
                    </ul>
                </nav>
            </div>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Kategori</h2>
                    <ol class="breadcrumb">
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li class="active"><strong>Category</strong></li>
                    </ol>
                </div>
            </div>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-info" data-toggle="modal" data-backdrop="static" data-target="#myModal5">New Category</button>
                                <!-- Modal -->
                                <div class="modal inmodal fade" id="myModal5" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <h4 class="modal-title">New Category</h4>
                                            </div>
                                            <form method="post" action="kategori.php" class="form-horizontal">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="nama_kategori" class="form-label">Nama Kategori:</label>
                                                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="keterangan" class="form-label">Keterangan:</label>
                                                        <textarea class="form-control" rows="5" id="keterangan" name="keterangan"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" name="add">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Kategori</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (mysqli_num_rows($result) > 0) {
                                                $no = 1;
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $no . "</td>";
                                                    echo "<td>" . $row['nama_kategori'] . "</td>";
                                                    echo "<td>" . $row['keterangan'] . "</td>";
                                                    echo "<td>
                                                        <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal' data-id='" . $row['id_kategori'] . "' data-nama='" . $row['nama_kategori'] . "' data-keterangan='" . $row['keterangan'] . "'>Edit</button>
                                                        <a href='kategori.php?delete=" . $row['id_kategori'] . "' class='btn btn-danger btn-sm'>Delete</a>
                                                    </td>";
                                                    echo "</tr>";
                                                    $no++;
                                                }
                                            } else {
                                                echo "<tr><td colspan='4'>No data available</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Edit Modal -->
                            <div class="modal inmodal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title">Edit Category</h4>
                                        </div>
                                        <form method="post" action="kategori.php" class="form-horizontal" id="editForm">
                                            <div class="modal-body">
                                                <input type="hidden" id="edit-id" name="id_kategori">
                                                <div class="form-group">
                                                    <label for="edit-nama_kategori" class="form-label">Nama Kategori:</label>
                                                    <input type="text" class="form-control" id="edit-nama_kategori" name="nama_kategori">
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit-keterangan" class="form-label">Keterangan:</label>
                                                    <textarea class="form-control" rows="5" id="edit-keterangan" name="keterangan"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" name="update">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mainly scripts -->
        <script src="js/jquery-3.1.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
        <script src="js/plugins/dataTables/datatables.min.js"></script>
        <!-- Custom and plugin javascript -->
        <script src="js/inspinia.js"></script>
        <script src="js/plugins/pace/pace.min.js"></script>
        <!-- Page-Level Scripts -->
        <script>
            $(document).ready(function() {
                $('.dataTables-example').DataTable({
                    pageLength: 25,
                    responsive: true,
                    dom: '<"html5buttons"B>lTfgitp',
                    buttons: [{
                            extend: 'copy'
                        },
                        {
                            extend: 'csv'
                        },
                        {
                            extend: 'excel',
                            title: 'ExampleFile'
                        },
                        {
                            extend: 'pdf',
                            title: 'ExampleFile'
                        },
                        {
                            extend: 'print',
                            customize: function(win) {
                                $(win.document.body).addClass('white-bg');
                                $(win.document.body).css('font-size', '10px');
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            }
                        }
                    ]
                });

                $('#editModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('id');
                    var nama = button.data('nama');
                    var keterangan = button.data('keterangan');

                    var modal = $(this);
                    modal.find('#edit-id').val(id);
                    modal.find('#edit-nama_kategori').val(nama);
                    modal.find('#edit-keterangan').val(keterangan);
                });
            });
        </script>
</body>

</html>