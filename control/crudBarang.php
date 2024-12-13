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
<<<<<<< HEAD
            
            
            $addDB = mysqli_query($conn, "INSERT INTO barang (kode, namabarang, jenisbarang, stock, owner_id, name) VALUES ('$kodeb', '$namaBarang', '$jenis', '$stock', '$ownerId', '$name')");
            
            // Redirect berdasarkan role
            if ($userrole == "Admin") {
                header('location:index.php');
            } else {
                header('location:index2.php');
            }
=======
            if($stock > 0){
                $addDB = mysqli_query($conn, "INSERT INTO barang (kode, namabarang, jenisbarang, stock, owner_id, name) VALUES ('$kodeb', '$namaBarang', '$jenis', '$stock', '$ownerId', '$name')");
                if ($userrole == "Admin") {
                    header('location:index.php');
                } else {
                    header('location:index2.php');
                }
            }else {
                if ($userrole == "Admin") {
                    $message = "Stock barang tidak valid";
                    echo "<script type='text/javascript'>
                            window.location.href=\"/stockbarang/index.php\";
                                    alert('$message');
                        </script>";    
                    exit;
                } else {
                    $message = "Stock barang tidak valid";
                    echo "<script type='text/javascript'>
                            window.location.href=\"/stockbarang/index2.php\";
                                    alert('$message');
                        </script>";  
                    exit;
                }
                exit;
            }


>>>>>>> ba514db (Update 13 December)
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

<<<<<<< HEAD
        if ($userrole == "Admin") {
            $update = mysqli_query($conn, "UPDATE barang SET namabarang='$namaBarang', jenisbarang='$jenis', stock=$stock, name='$owner' WHERE idbarang='$id'");
            header('location:index.php');
        } else {
            $update = mysqli_query($conn, "UPDATE barang SET namabarang='$namaBarang', jenisbarang='$jenis', stock=$stock, name='$owner' WHERE idbarang='$id' AND owner_id='$ownerId'");
            header('location:index2.php');
=======

        if ($userrole == "Admin") {
            if (is_numeric($jenis) || is_numeric($namaBarang) || is_numeric($owner)) {
                $_SESSION['errorMessage'] = "Jenis barang, nama barang, dan nama pemilik tidak boleh angka saja.";
                header("Location:index.php");
                exit;
            }
            
            if ($stock > 0) {
                $update = mysqli_query($conn, "UPDATE barang SET namabarang='$namaBarang', jenisbarang='$jenis', stock=$stock, name='$owner' WHERE idbarang='$id'");
                header('location:index.php');
            } else {
                $_SESSION['errorMessage'] = "Stock barang tidak valid";
                header("Location:index.php");
                exit;
            }
        } else {
            if (is_numeric($jenis) || is_numeric($namaBarang)) {
                $_SESSION['errorMessage'] = "Jenis barang dan nama barang tidak boleh angka saja.";
                header("Location:index2.php");
                exit;
            }

            if ($stock > 0) {
                $update = mysqli_query($conn, "UPDATE barang SET namabarang='$namaBarang', jenisbarang='$jenis', stock=$stock, name='$owner' WHERE idbarang='$id' AND owner_id='$ownerId'");
                header('location:index2.php');
            } else {
                $_SESSION['errorMessage'] = "Stock barang tidak valid";
                header("Location:index2.php");
                exit;
            }
>>>>>>> ba514db (Update 13 December)
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
<<<<<<< HEAD
?>
=======
?>
>>>>>>> ba514db (Update 13 December)
