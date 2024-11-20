<?php 
require "./Connection/function.php";  

// Fungsi untuk menambah pengguna baru
if (isset($_POST['add'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Ambil nilai role dari form

    // Tambahkan pengguna baru dengan email, password, dan role
    $add = mysqli_query($conn, "INSERT INTO login (email, password, role) VALUES ('$email','$password', '$role')");
    
    if ($add) {
        header('location:user.php');
    } else {
        header('location:user.php');
    }
}

// Fungsi untuk memperbarui pengguna
if (isset($_POST['updateUser'])) {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $role = $_POST['role']; // Ambil nilai role dari form

    // Cek apakah email sudah terdaftar
    $checkEmail = mysqli_query($conn, "SELECT iduser FROM login WHERE email='$email' AND iduser != '$id'");
    
    if (mysqli_num_rows($checkEmail) > 0) {
        // Jika email sudah ada, redirect dengan pesan error
        $message = "Email yang anda masukkan sudah terdaftar";
        echo "<script type='text/javascript'>
                window.location.href=\"/stockbarang/user.php\";
                alert('$message');
            </script>";  
        exit;
    } else {
        // Jika email belum terdaftar, lanjutkan update
        $update = mysqli_query($conn, "UPDATE login SET email='$email', password='$pass', role='$role' WHERE iduser='$id'");
        
        if ($update) {
            $message = "Data berhasil diperbarui";
        echo "<script type='text/javascript'>
                window.location.href=\"/stockbarang/user.php\";
                alert('$message');
            </script>";  
        exit;
        } else {
            header('location:user.php?error=Gagal memperbarui data');
        }
    }
}


// Fungsi untuk menghapus pengguna
if (isset($_POST['hapusUser'])) {
    $id = $_POST['id'];

    // Hapus pengguna berdasarkan iduser
    $delete = mysqli_query($conn, "DELETE FROM login WHERE iduser='$id'");
    
    if ($delete) {
        header('location:user.php');
    } else {
        header('location:user.php');
    }
}
?>
