<?php
session_start();
include 'connection.php';
date_default_timezone_set('Asia/Jakarta');

$query = "SELECT * FROM kategori"; // Assuming 'kategori' is your table name
$result = mysqli_query($koneksi, $query);
$categories = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}

$query_artikel = "SELECT a.id_artikel, a.tanggal, a.judul, a.isi, a.kategori, a.penulis, a.gambar, k.nama_kategori 
                  FROM artikel a
                  LEFT JOIN kategori k ON a.kategori = k.id_kategori"; // Sesuaikan dengan struktur tabel Anda

$result_artikel = mysqli_query($koneksi, $query_artikel);
$articles = [];
if (mysqli_num_rows($result_artikel) > 0) {
    while ($row = mysqli_fetch_assoc($result_artikel)) {
        $articles[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard-artikel</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/dataTables/datatables.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/plugins/select2/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .ck-editor__editable[role="textbox"] {
            /* Editing area */
            min-height: 350px;
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $_SESSION['username']; ?></strong>
                                    </span> <span class="text-muted text-xs block">Author</span> </span> </a>
                        </div>
                        <div class="logo-element">
                            VV
                        </div>
                    </li>
                    <li>
                        <a href="dashboard.php"><i class="fa fa-th-large"></i><span class="nav-label">Dashboard</span></a>
                    </li>
                    <li>
                        <a href="artikel.php"><i class="bi bi-file-earmark-check-fill"></i><span class="nav-label">Artikel</span> </a>
                    </li>
                    <li>
                        <a href="kategori.php"><i class="bi bi-bookmark-check-fill"></i><span class="nav-label">Kategori</span></a>
                    </li>
                    <li>
                        <a href="penulis.php"><i class="bi bi-person-square"></i><span class="nav-label">Penulis</span></a>
                    </li>
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
                        <li>
                            <span class="m-r-sm text-muted welcome-message">Welcome</span>
                        </li>
                        <li>
                            <a href="login.php">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Artikel</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="active">
                            <strong>Artikel</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-info" data-toggle="modal" data-backdrop="static" data-target="#myModal5">New Article</button>
                                <!-- Modal -->
                                <div class="modal inmodal fade" id="myModal5" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <h4 class="modal-title">New Article</h4>
                                            </div>
                                            <form id="formNewArticle" method="post" action="artikel_handler.php" class="form-horizontal">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="tanggal" class="form-label">Tanggal:</label>
                                                        <input type="text" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d || H:i:s'); ?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Judul" class="form-label">Judul:</label>
                                                        <input type="text" class="form-control" id="Judul" name="Judul">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="penulis" class="form-label">Penulis:</label>
                                                        <input type="text" class="form-control" id="penulis" name="penulis" value="<?php echo $_SESSION['username']; ?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="kategori" class="form-label">Kategori:</label>
                                                        <select class="select2_demo_3 form-control" id="kategori" name="kategori" required>
                                                            <option value="">Pilih Kategori</option>
                                                            <?php foreach ($categories as $category) { ?>
                                                                <option value="<?php echo $category['id_kategori']; ?>"><?php echo $category['nama_kategori']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="isi" class="form-label">Isi Artikel:</label>
                                                        <textarea class="form-control" rows="5" id="isi" name="isi"></textarea>
                                                        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
                                                        <script>
                                                            // This sample still does not showcase all CKEditor&nbsp;5 features (!)
                                                            // Visit https://ckeditor.com/docs/ckeditor5/latest/features/index.html to browse all the features.
                                                            CKEDITOR.ClassicEditor.create(document.getElementById("isi"), {
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
                                                                toolbar: {
                                                                    items: [
                                                                        'exportPDF', 'exportWord', '|',
                                                                        'findAndReplace', 'selectAll', '|',
                                                                        'heading', '|',
                                                                        'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                                                                        'bulletedList', 'numberedList', 'todoList', '|',
                                                                        'outdent', 'indent', '|',
                                                                        'undo', 'redo',
                                                                        '-',
                                                                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                                                                        'alignment', '|',
                                                                        'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                                                                        'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                                                                        'textPartLanguage', '|',
                                                                        'sourceEditing'
                                                                    ],
                                                                    shouldNotGroupWhenFull: true
                                                                },
                                                                // Changing the language of the interface requires loading the language file using the <script> tag.
                                                                // language: 'es',
                                                                list: {
                                                                    properties: {
                                                                        styles: true,
                                                                        startIndex: true,
                                                                        reversed: true
                                                                    }
                                                                },
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
                                                                heading: {
                                                                    options: [{
                                                                            model: 'paragraph',
                                                                            title: 'Paragraph',
                                                                            class: 'ck-heading_paragraph'
                                                                        },
                                                                        {
                                                                            model: 'heading1',
                                                                            view: 'h1',
                                                                            title: 'Heading 1',
                                                                            class: 'ck-heading_heading1'
                                                                        },
                                                                        {
                                                                            model: 'heading2',
                                                                            view: 'h2',
                                                                            title: 'Heading 2',
                                                                            class: 'ck-heading_heading2'
                                                                        },
                                                                        {
                                                                            model: 'heading3',
                                                                            view: 'h3',
                                                                            title: 'Heading 3',
                                                                            class: 'ck-heading_heading3'
                                                                        },
                                                                        {
                                                                            model: 'heading4',
                                                                            view: 'h4',
                                                                            title: 'Heading 4',
                                                                            class: 'ck-heading_heading4'
                                                                        },
                                                                        {
                                                                            model: 'heading5',
                                                                            view: 'h5',
                                                                            title: 'Heading 5',
                                                                            class: 'ck-heading_heading5'
                                                                        },
                                                                        {
                                                                            model: 'heading6',
                                                                            view: 'h6',
                                                                            title: 'Heading 6',
                                                                            class: 'ck-heading_heading6'
                                                                        }
                                                                    ]
                                                                },
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
                                                                placeholder: 'Tulis artikel di sini',
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
                                                                fontFamily: {
                                                                    options: [
                                                                        'default',
                                                                        'Arial, Helvetica, sans-serif',
                                                                        'Courier New, Courier, monospace',
                                                                        'Georgia, serif',
                                                                        'Lucida Sans Unicode, Lucida Grande, sans-serif',
                                                                        'Tahoma, Geneva, sans-serif',
                                                                        'Times New Roman, Times, serif',
                                                                        'Trebuchet MS, Helvetica, sans-serif',
                                                                        'Verdana, Geneva, sans-serif'
                                                                    ],
                                                                    supportAllValues: true
                                                                },
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
                                                                fontSize: {
                                                                    options: [10, 12, 14, 'default', 18, 20, 22],
                                                                    supportAllValues: true
                                                                },
                                                                // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
                                                                htmlSupport: {
                                                                    allow: [{
                                                                        name: /.*/,
                                                                        attributes: true,
                                                                        classes: true,
                                                                        styles: true
                                                                    }]
                                                                },
                                                                // Be careful with enabling previews
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
                                                                htmlEmbed: {
                                                                    showPreviews: true
                                                                },
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
                                                                link: {
                                                                    decorators: {
                                                                        addTargetToExternalLinks: true,
                                                                        defaultProtocol: 'https://',
                                                                        toggleDownloadable: {
                                                                            mode: 'manual',
                                                                            label: 'Downloadable',
                                                                            attributes: {
                                                                                download: 'file'
                                                                            }
                                                                        }
                                                                    }
                                                                },
                                                                // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
                                                                mention: {
                                                                    feeds: [{
                                                                        marker: '@',
                                                                        feed: [
                                                                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                                                                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                                                                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                                                                            '@sugar', '@sweet', '@topping', '@wafer'
                                                                        ],
                                                                        minimumCharacters: 1
                                                                    }]
                                                                },
                                                                // The "superbuild" contains more premium features that require additional configuration, disable them below.
                                                                // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
                                                                removePlugins: [
                                                                    // These two are commercial, but you can try them out without registering to a trial.
                                                                    // 'ExportPdf',
                                                                    // 'ExportWord',
                                                                    'AIAssistant',
                                                                    'CKBox',
                                                                    'CKFinder',
                                                                    'EasyImage',
                                                                    // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                                                                    // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                                                                    // Storing images as Base64 is usually a very bad idea.
                                                                    // Replace it on production website with other solutions:
                                                                    // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                                                                    // 'Base64UploadAdapter',
                                                                    'MultiLevelList',
                                                                    'RealTimeCollaborativeComments',
                                                                    'RealTimeCollaborativeTrackChanges',
                                                                    'RealTimeCollaborativeRevisionHistory',
                                                                    'PresenceList',
                                                                    'Comments',
                                                                    'TrackChanges',
                                                                    'TrackChangesData',
                                                                    'RevisionHistory',
                                                                    'Pagination',
                                                                    'WProofreader',
                                                                    // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                                                                    // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                                                                    'MathType',
                                                                    // The following features are part of the Productivity Pack and require additional license.
                                                                    'SlashCommand',
                                                                    'Template',
                                                                    'DocumentOutline',
                                                                    'FormatPainter',
                                                                    'TableOfContents',
                                                                    'PasteFromOfficeEnhanced',
                                                                    'CaseChange'
                                                                ]
                                                            });
                                                        </script>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="formFile" class="form-label">Gambar:</label>
                                                        <input class="form-control" type="file" id="gambar" name="gambar">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover dataTables-example">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Tanggal</th>
                                                    <th>Judul</th>
                                                    <th>Kategori</th>
                                                    <th>Penulis</th>
                                                    <th>Gambar</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($articles as $key => $article) { ?>
                                                    <tr class="gradeX">
                                                        <td><?php echo $key + 1; ?></td>
                                                        <td><?php echo $article['tanggal']; ?></td>
                                                        <td><?php echo $article['judul']; ?></td>
                                                        <td><?php echo $article['nama_kategori']; ?></td>
                                                        <td><?php echo $article['penulis']; ?></td>
                                                        <td>
                                                            <?php if (!empty($article['gambar'])) { ?>
                                                                <img src="<?php echo 'path_to_your_image_directory/' . $article['gambar']; ?>" class="img-thumbnail" width="100" alt="Gambar Artikel">
                                                            <?php } else { ?>
                                                                No image available
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary edit-article" data-toggle="modal" data-target="#editModal" data-id="<?php echo $article['id_artikel']; ?>">Edit</button>
                                                            <button class="btn btn-sm btn-danger" onclick="deleteArticle(<?php echo $article['id_artikel']; ?>)">Delete</button>
                                                            <script>
                                                                function deleteArticle(id) {
                                                                    if (confirm('Are you sure you want to delete this article?')) {
                                                                        // Lakukan penghapusan artikel dengan AJAX
                                                                        $.ajax({
                                                                            url: 'delete_artikel.php',
                                                                            method: 'POST',
                                                                            data: {
                                                                                id_artikel: id
                                                                            },
                                                                            success: function(response) {
                                                                                var result = JSON.parse(response);
                                                                                if (result.status === 'success') {
                                                                                    alert('Article deleted successfully');
                                                                                    window.location.reload(); // Refresh halaman
                                                                                } else {
                                                                                    alert('Failed to delete article');
                                                                                }
                                                                            },
                                                                            error: function(xhr, status, error) {
                                                                                alert('Error deleting article');
                                                                                console.error(xhr);
                                                                            }
                                                                        });
                                                                    }
                                                                }
                                                            </script>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal inmodal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Edit Article</h4>
                    </div>
                    <form id="formEditArticle" method="post" action="edit_artikel_handler.php" class="form-horizontal">
                        <div class="modal-body">
                            <input type="hidden" name="id_artikel" id="edit_id_artikel">
                            <div class="form-group">
                                <label for="edit_tanggal" class="form-label">Tanggal:</label>
                                <input type="text" class="form-control" id="edit_tanggal" name="tanggal" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit_judul" class="form-label">Judul:</label>
                                <input type="text" class="form-control" id="edit_judul" name="judul">
                            </div>
                            <div class="form-group">
                                <label for="edit_penulis" class="form-label">Penulis:</label>
                                <input type="text" class="form-control" id="edit_penulis" name="penulis" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit_kategori" class="form-label">Kategori:</label>
                                <select class="select2_demo_3 form-control" id="edit_kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $category) { ?>
                                        <option value="<?php echo $category['id_kategori']; ?>"><?php echo $category['nama_kategori']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_isi" class="form-label">Isi Artikel:</label>
                                <textarea class="form-control" rows="5" id="edit_isi" name="isi"></textarea>
                                <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
                                <script>
                                    // This sample still does not showcase all CKEditor&nbsp;5 features (!)
                                    // Visit https://ckeditor.com/docs/ckeditor5/latest/features/index.html to browse all the features.
                                    CKEDITOR.ClassicEditor.create(document.getElementById("edit_isi"), {
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
                                        toolbar: {
                                            items: [
                                                'exportPDF', 'exportWord', '|',
                                                'findAndReplace', 'selectAll', '|',
                                                'heading', '|',
                                                'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                                                'bulletedList', 'numberedList', 'todoList', '|',
                                                'outdent', 'indent', '|',
                                                'undo', 'redo',
                                                '-',
                                                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                                                'alignment', '|',
                                                'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                                                'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                                                'textPartLanguage', '|',
                                                'sourceEditing'
                                            ],
                                            shouldNotGroupWhenFull: true
                                        },
                                        // Changing the language of the interface requires loading the language file using the <script> tag.
                                        // language: 'es',
                                        list: {
                                            properties: {
                                                styles: true,
                                                startIndex: true,
                                                reversed: true
                                            }
                                        },
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
                                        heading: {
                                            options: [{
                                                    model: 'paragraph',
                                                    title: 'Paragraph',
                                                    class: 'ck-heading_paragraph'
                                                },
                                                {
                                                    model: 'heading1',
                                                    view: 'h1',
                                                    title: 'Heading 1',
                                                    class: 'ck-heading_heading1'
                                                },
                                                {
                                                    model: 'heading2',
                                                    view: 'h2',
                                                    title: 'Heading 2',
                                                    class: 'ck-heading_heading2'
                                                },
                                                {
                                                    model: 'heading3',
                                                    view: 'h3',
                                                    title: 'Heading 3',
                                                    class: 'ck-heading_heading3'
                                                },
                                                {
                                                    model: 'heading4',
                                                    view: 'h4',
                                                    title: 'Heading 4',
                                                    class: 'ck-heading_heading4'
                                                },
                                                {
                                                    model: 'heading5',
                                                    view: 'h5',
                                                    title: 'Heading 5',
                                                    class: 'ck-heading_heading5'
                                                },
                                                {
                                                    model: 'heading6',
                                                    view: 'h6',
                                                    title: 'Heading 6',
                                                    class: 'ck-heading_heading6'
                                                }
                                            ]
                                        },
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
                                        placeholder: 'Tulis artikel di sini',
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
                                        fontFamily: {
                                            options: [
                                                'default',
                                                'Arial, Helvetica, sans-serif',
                                                'Courier New, Courier, monospace',
                                                'Georgia, serif',
                                                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                                                'Tahoma, Geneva, sans-serif',
                                                'Times New Roman, Times, serif',
                                                'Trebuchet MS, Helvetica, sans-serif',
                                                'Verdana, Geneva, sans-serif'
                                            ],
                                            supportAllValues: true
                                        },
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
                                        fontSize: {
                                            options: [10, 12, 14, 'default', 18, 20, 22],
                                            supportAllValues: true
                                        },
                                        // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
                                        htmlSupport: {
                                            allow: [{
                                                name: /.*/,
                                                attributes: true,
                                                classes: true,
                                                styles: true
                                            }]
                                        },
                                        // Be careful with enabling previews
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
                                        htmlEmbed: {
                                            showPreviews: true
                                        },
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
                                        link: {
                                            decorators: {
                                                addTargetToExternalLinks: true,
                                                defaultProtocol: 'https://',
                                                toggleDownloadable: {
                                                    mode: 'manual',
                                                    label: 'Downloadable',
                                                    attributes: {
                                                        download: 'file'
                                                    }
                                                }
                                            }
                                        },
                                        // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
                                        mention: {
                                            feeds: [{
                                                marker: '@',
                                                feed: [
                                                    '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                                                    '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                                                    '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                                                    '@sugar', '@sweet', '@topping', '@wafer'
                                                ],
                                                minimumCharacters: 1
                                            }]
                                        },
                                        // The "superbuild" contains more premium features that require additional configuration, disable them below.
                                        // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
                                        removePlugins: [
                                            // These two are commercial, but you can try them out without registering to a trial.
                                            // 'ExportPdf',
                                            // 'ExportWord',
                                            'AIAssistant',
                                            'CKBox',
                                            'CKFinder',
                                            'EasyImage',
                                            // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                                            // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                                            // Storing images as Base64 is usually a very bad idea.
                                            // Replace it on production website with other solutions:
                                            // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                                            // 'Base64UploadAdapter',
                                            'MultiLevelList',
                                            'RealTimeCollaborativeComments',
                                            'RealTimeCollaborativeTrackChanges',
                                            'RealTimeCollaborativeRevisionHistory',
                                            'PresenceList',
                                            'Comments',
                                            'TrackChanges',
                                            'TrackChangesData',
                                            'RevisionHistory',
                                            'Pagination',
                                            'WProofreader',
                                            // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                                            // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                                            'MathType',
                                            // The following features are part of the Productivity Pack and require additional license.
                                            'SlashCommand',
                                            'Template',
                                            'DocumentOutline',
                                            'FormatPainter',
                                            'TableOfContents',
                                            'PasteFromOfficeEnhanced',
                                            'CaseChange'
                                        ]
                                    });
                                </script>
                            </div>
                            <div class="form-group">
                                <label for="edit_gambar" class="form-label">Gambar:</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="edit_gambar" name="gambar">
                                    <label class="custom-file-label" id="edit_gambar_label" for="edit_gambar">Pilih file</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
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
            });

            $(document).ready(function() {
                $('#formNewArticle').submit(function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var formData = new FormData(form[0]);

                    $.ajax({
                        type: 'POST',
                        url: 'artikel_handler.php',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            alert('Artikel berhasil disimpan.');
                            $('#myModal5').modal('hide');
                            // Optional: reload table or update interface
                        },
                        error: function(xhr, status, error) {
                            alert('Terjadi kesalahan: ' + xhr.responseText);
                        }
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('.edit-article').click(function() {
                    var articleId = $(this).data('id');
                    $('#editModal' + articleId).modal('show');
                });

                // Initialize Select2
                $('.select2_demo_3').select2({
                    placeholder: "Pilih kategori",
                    allowClear: true
                });
            });
        </script>
</body>

</html>