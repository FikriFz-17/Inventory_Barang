<?php 
    require "Connection/function.php";
    require "control/CekIsLogin.php";
    require "control/crudBarang.php";

    if (!isset($_SESSION['login']) || $_SESSION['role'] !== "User") {
        header('location:login.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Inventory</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
                <a class="navbar-brand" href="index.php">Inventori Barang</a>
                <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#">
                    <i class="fas fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user fa-fw"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="Logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Menu</div>
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index2.php' ? 'active' : '' ?>" href="index2.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Kelola Barang
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'Tidak ada email'; ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h1 class="mt-4">KELOLA BARANG</h1>
                        <div class="card mb-4">
                            <div class="card-header">
                                <!-- Button to Open the Modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                    Tambah Barang
                                </button>

                                <a id="export-btn" class="btn btn-info" role="button">Export</a>
                                

                                <!-- The Modals -->
                                <div class="modal fade" id="myModal">
                                    <div class="modal-dialog">
                                    <div class="modal-content">
                                    
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                        <h4 class="modal-title">Tambah Barang</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        
                                        <!-- Modal body -->
                                        <form method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="userId" id="userId" value="<?php echo $_SESSION["userId"] ?>">
                                                <input type="hidden" name="email" id="email" value="<?php echo $_SESSION["email"] ?>">
                                                <input type="text" name="kodeBarang" placeholder="Kode Barang" class="form-control my-3" required>
                                                <input type="text" name="namaBarang" placeholder="Nama Barang" class="form-control my-3" required>
                                                <input type="text" name="jenis" placeholder="Jenis Barang" class="form-control my-3" required>
                                                <input type="number" name="stock" class="form-control my-3" placeholder="stock barang" required>
                                                <button type="submit" class="btn btn-primary my-3" name="add">Add</button>
                                            </div>
                                        </form>
                                        
                                        <!-- Modal footer -->
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                        
                                    </div>
                                    </div>
                                </div>

                                <!-- Delete Modals -->
                                <div class="modal fade" id="delete">
                                    <div class="modal-dialog">
                                    <div class="modal-content">
                                    
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                        <h4 class="modal-title">Tambah Barang</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        
                                        <!-- Modal body -->
                                        <form method="post">
                                            <div class="modal-body">
                                                <input type="text" name="namaBarang" placeholder="Nama Barang" class="form-control my-3" required>
                                                <input type="text" name="jenis" placeholder="Jenis Barang" class="form-control my-3" required>
                                                <input type="number" name="stock" class="form-control my-3" placeholder="stock barang" required>
                                                <button type="submit" class="btn btn-primary my-3" name="add">Add</button>
                                            </div>
                                        </form>
                                        
                                        <!-- Modal footer -->
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                        
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Jenis</th>
                                                <th>Stok</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Tampilkan data dari database ke halaman web -->
                                            <?php 
                                                $userid = $_SESSION["userId"];
                                                $selectALL = mysqli_query($conn, "SELECT * FROM barang WHERE owner_id='$userid'");
                                                while ($data = mysqli_fetch_array($selectALL)) {
                                                    $idb = $data['idbarang'];
                                                    $kodeb = $data['kode'];
                                                    $namabarang = $data['namabarang'];
                                                    $owner = $data['name'];
                                                    $jenis = $data['jenisbarang'];
                                                    $stock = $data['stock'];
                                                    $ownerId = $_SESSION['userId'];
                                            ?>
                                            
                                                    <tr>
                                                        <td><?=$kodeb;?></td>
                                                        <td><?=$namabarang;?></td>
                                                        <td><?=$jenis;?></td>
                                                        <td><?=$stock;?></td>
                                                        <td>
                                                            <button type="submit" class="btn btn-warning my-3" data-toggle="modal" data-target="#edit<?=$idb;?>">Edit</button>

                                                            <button type="submit" class="btn btn-danger my-3" data-toggle="modal" data-target="#delete<?=$idb;?>">Delete</button>
                                                        </td>
                                                    </tr>

                                                    <!--  Edit Modals-->
                                                        <div class="modal fade" id="edit<?=$idb;?>">
                                                            <div class="modal-dialog">
                                                            <div class="modal-content">
                                                            
                                                                <!-- Modal Header -->
                                                                <div class="modal-header">
                                                                <h4 class="modal-title">Edit Barang</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                
                                                                <!-- Modal body -->
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <input type="text" name="namaBarang" value="<?=$namabarang;?>" class="form-control my-3" required>
                                                                        <input type="text" name="jenis" value="<?=$jenis;?>" class="form-control my-3" required>
                                                                        <input type="hidden" name="owner" value="<?=$owner;?>" class="form-control my-3" required>
                                                                        <input type="number" name="stock" class="form-control my-3" value="<?=$stock;?>" required>
                                                                        <button type="submit" class="btn btn-warning my-3" name="updateBarang">Edit</button>
                                                                        <input type="hidden" name="idb" value="<?=$idb;?>">
                                                                        <input type="hidden" name="userId" value="<?=$ownerId;?>">
                                                                        <input type="hidden" name="kodeb" value="<?=$kodeb;?>">
                                                                    </div>
                                                                </form>
                                                                <?php if (!empty($_SESSION['errorMessage'])): ?>
                                                                    <script>
                                                                        Swal.fire({
                                                                            icon: 'error',
                                                                            title: 'Input Tidak Valid!',
                                                                            text: '<?= $_SESSION['errorMessage']; ?>',
                                                                            confirmButtonText: 'Kembali'
                                                                        });
                                                                    </script>
                                                                    <?php unset($_SESSION['errorMessage']); // Hapus setelah ditampilkan ?>
                                                                <?php endif; ?>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    
                                                     <!--  Delete Modals-->
                                                     <div class="modal fade" id="delete<?=$idb;?>">
                                                            <div class="modal-dialog">
                                                            <div class="modal-content">
                                                            
                                                                <!-- Modal Header -->
                                                                <div class="modal-header">
                                                                <h4 class="modal-title">Hapus Barang</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                
                                                                <!-- Modal body -->
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        Hapus Barang <?=$namabarang;?>?
                                                                        <input type="hidden" name="idb" value="<?=$idb;?>">
                                                                        <input type="hidden" name="userId" value="<?=$ownerId;?>">
                                                                        <input type="hidden" name="kodeb" value="<?=$kodeb;?>">
                                                                        <br>
                                                                        <button type="submit" class="btn btn-danger my-3" name="hapusBarang">Delete</button>
                                                                    </div>
                                                                </form>
                                                                
                                                            </div>
                                                            </div>
                                                        </div>
                                            <?php 
                                            } // Tutup kurung kurawal untuk while loop
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2020</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        
        <!-- jquery export -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> <!-- plugin kalo mau export ke excel -->
        <!-- jquery end -->
        <script src="export.js"></script>
    </body>
</html>