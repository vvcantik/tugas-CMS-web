<?php
session_start();
include 'connection.php';

// Handle add new author
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama_penulis = $_POST['nama_penulis'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Prepared statement untuk menghindari SQL Injection
    $stmt = $koneksi->prepare("INSERT INTO penulis (nama_penulis, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama_penulis, $username, $password);

    if ($stmt->execute()) {
        header('Location: penulis.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_penulis = $_POST['id_penulis'];
    $nama_penulis = $_POST['nama_penulis'];
    $username = $_POST['username'];

    // Check if the password field is not empty
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $koneksi->prepare("UPDATE penulis SET nama_penulis=?, username=?, password=? WHERE id_penulis=?");
        $stmt->bind_param("sssi", $nama_penulis, $username, $password, $id_penulis);
    } else {
        $stmt = $koneksi->prepare("UPDATE penulis SET nama_penulis=?, username=? WHERE id_penulis=?");
        $stmt->bind_param("ssi", $nama_penulis, $username, $id_penulis);
    }

    if ($stmt->execute()) {
        header('Location: penulis.php');
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id_penulis = $_GET['delete'];

    $stmt = $koneksi->prepare("DELETE FROM penulis WHERE id_penulis=?");
    $stmt->bind_param("i", $id_penulis);

    if ($stmt->execute()) {
        header('Location: penulis.php');
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch authors from the database
$query = "SELECT id_penulis, nama_penulis, username FROM penulis";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Penulis</title>
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
                                <button type="button" class="btn btn-info" data-toggle="modal" data-backdrop="static" data-target="#myModal5">New Author</button>
                                <!-- Modal -->
                                <div class="modal inmodal fade" id="myModal5" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <h4 class="modal-title">New Author</h4>
                                            </div>
                                            <form method="post" action="penulis.php" class="form-horizontal">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="nama_penulis" class="form-label">Nama:</label>
                                                        <input type="text" class="form-control" id="nama_penulis" name="nama_penulis" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="username" class="form-label">Username:</label>
                                                        <input type="text" class="form-control" id="username" name="username" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password" class="form-label">Password:</label>
                                                        <input type="password" class="form-control" id="password" name="password" required>
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
                                                <th>Nama Penulis</th>
                                                <th>Username</th>
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
                                                    echo "<td>" . $row['nama_penulis'] . "</td>";
                                                    echo "<td>" . $row['username'] . "</td>";
                                                    echo "<td>
                                                        <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal' data-id='" . $row['id_penulis'] . "' data-nama='" . $row['nama_penulis'] . "' data-username='" . $row['username'] . "'>Edit</button>
                                                        <a href='penulis.php?delete=" . $row['id_penulis'] . "' class='btn btn-danger btn-sm'>Delete</a>
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
                                            <h4 class="modal-title">Edit Author</h4>
                                        </div>
                                        <form method="post" action="penulis.php" class="form-horizontal" id="editForm">
                                            <div class="modal-body">
                                                <input type="hidden" id="edit-id" name="id_penulis">
                                                <div class="form-group">
                                                    <label for="edit-nama_penulis" class="form-label">Nama:</label>
                                                    <input type="text" class="form-control" id="edit-nama_penulis" name="nama_penulis">
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit-username" class="form-label">Username:</label>
                                                    <input type="text" class="form-control" id="edit-username" name="username">
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit-password" class="form-label">Password:</label>
                                                    <input type="password" class="form-control" id="edit-password" name="password">
                                                    <small class="text-muted">Leave this field blank if you don't want to change the password.</small>
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
                    var username = button.data('username');

                    var modal = $(this);
                    modal.find('#edit-id').val(id);
                    modal.find('#edit-nama_penulis').val(nama);
                    modal.find('#edit-username').val(username);
                });
            });
        </script>
</body>

</html>