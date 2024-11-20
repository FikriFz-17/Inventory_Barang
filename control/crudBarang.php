<?php 
    require "./Connection/function.php";  
    $userrole = $_SESSION['role'];
    if (isset($_POST['add'])) {
        $kodeb = $_POST['kodeBarang'];
        $namaBarang = $_POST['namaBarang'];
        $jenis = $_POST['jenis'];
        $stock = $_POST['stock'];
        $ownerId = $_POST['userId'];
        $email = $_POST['email'];

        $result = mysqli_query($conn, "SELECT SUBSTRING_INDEX('$email', '@', 1) AS owner_name");

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $name = $row['owner_name']; // Ambil nama pemilik dari hasil query
            if($stock > 0){
                $addDB = mysqli_query($conn, "INSERT INTO barang (kode, namabarang, jenisbarang, stock, owner_id, name) VALUES ('$kodeb', '$namaBarang', '$jenis', '$stock', '$ownerId', '$name')");
                if ($userrole == "Admin") {
                    header('location:index.php');
                } else {
                    header('location:index2.php');
                }
            }else {
                if ($userrole == "Admin") {
                    header('location:index.php?error=Invalid+stock');
                } else {
                    header('location:index2.php?error=Invalid+stock');
                }
                exit;
            }


        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['updateBarang'])) {
        $id = $_POST['idb'];
        $kodeb = $_POST['kodeb'];
        $namaBarang = $_POST['namaBarang'];
        $jenis = $_POST['jenis'];
        $stock = $_POST['stock'];
        $ownerId = $_POST['userId'];
        $owner = $_POST['owner'];


        if ($userrole == "Admin") {
            if (is_numeric($jenis) || is_numeric($namaBarang)) {
                return header('location:index.php?error=Nama+barang+tidak+boleh+berupa+nomor');
            }
            
            if (gettype($namaBarang) == "string" && gettype($jenis) == "string" && $stock > 0) {
                $update = mysqli_query($conn, "UPDATE barang SET namabarang='$namaBarang', jenisbarang='$jenis', stock=$stock, name='$owner' WHERE idbarang='$id'");
                header('location:index.php');
            } else {
                header('location:index.php?error=Pastikan+field+nama,+jenis,+stock+terisi');
            }
        } else {
            if (is_numeric($jenis) || is_numeric($namaBarang)) {
                return header('location:index2.php?error=Nama+barang+tidak+boleh+berupa+nomor');
            }

            if (gettype($namaBarang) == "string" && gettype($jenis) == "string" && $stock > 0) {
                $update = mysqli_query($conn, "UPDATE barang SET namabarang='$namaBarang', jenisbarang='$jenis', stock=$stock, name='$owner' WHERE idbarang='$id' AND owner_id='$ownerId'");
                header('location:index2.php');
            } else {
                header('location:index2.php?error=Pastikan+field+nama,+jenis,+stock+terisi');
            }
        }
    }

    if (isset($_POST['hapusBarang'])) {
        $id = $_POST['idb'];
        $kodeb = $_POST['kodeb'];
        $ownerId = $_POST['userId'];

        if ($userrole == "Admin") {
            $delete = mysqli_query($conn, "DELETE FROM barang WHERE idbarang='$id'");
            header('location:index.php');
        } else {
            $delete = mysqli_query($conn, "DELETE FROM barang WHERE idbarang='$id' AND owner_id='$ownerId'");
            header('location:index2.php');
        }
    }
?>
