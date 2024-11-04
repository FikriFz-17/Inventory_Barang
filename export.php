<?php 
    require "Connection/function.php"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <h2>Inventory Barang</h2>
        <div class="data-table datatable-dark">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="1">
                                        <thead>
                                            <tr>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Jenis</th>
                                                <th>Stock</th>
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
                                                    $jenis = $data['jenisbarang'];
                                                    $stock = $data['stock'];
                                                    $ownerId = $_SESSION['userId'];
                                            ?>
                                            
                                                    <tr>
                                                        <td><?=$kodeb;?></td>
                                                        <td><?=$namabarang;?></td>
                                                        <td><?=$jenis;?></td>
                                                        <td><?=$stock;?></td>
                                                    </tr>
                                            <?php 
                                            } // Tutup kurung kurawal untuk while loop
                                            ?>
                                        </tbody>
                                    </table>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
        $('#dataTable').DataTable( {
            dom: 'Bfrtip',
            buttons: [
             'print'
        ]
    } );
} );
    </script>




</body>
</html>