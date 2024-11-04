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

    // Update email, password, dan role berdasarkan iduser
    $update = mysqli_query($conn, "UPDATE login SET email='$email', password='$pass', role='$role' WHERE iduser='$id'");
    
    if ($update) {
        header('location:user.php');
    } else {
        header('location:user.php');
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
