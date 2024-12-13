<?php 
require "./Connection/function.php";  

// Fungsi untuk menambah pengguna baru
if (isset($_POST['add'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Ambil nilai role dari form
<<<<<<< HEAD

    // Tambahkan pengguna baru dengan email, password, dan role
    $add = mysqli_query($conn, "INSERT INTO login (email, password, role) VALUES ('$email','$password', '$role')");
    
    if ($add) {
        header('location:user.php');
    } else {
        header('location:user.php');
=======
    // Cek apakah email sudah terdaftar
    $checkEmail = $conn->prepare("SELECT iduser FROM login WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();
    
    if ($checkEmail->num_rows > 0) {
        $checkEmail->close();
        // Jika email sudah terdaftar, redirect dengan pesan error
        $message = "Email yang anda masukkan sudah terdaftar";
        echo "<script type='text/javascript'>
                window.location.href=\"/stockbarang/user.php\";
                alert('$message');
            </script>";  
        exit;
    } else {
        $checkEmail->close();
        // Hash password sebelum disimpan
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Gunakan prepared statement untuk mencegah SQL injection
        $stmt = $conn->prepare("INSERT INTO login (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $role);
        
        if ($stmt->execute()) {
            header('location:user.php');
        } else {
            header('location:user.php');
        }
        $stmt->close();
>>>>>>> ba514db (Update 13 December)
    }
}

// Fungsi untuk memperbarui pengguna
if (isset($_POST['updateUser'])) {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $role = $_POST['role']; // Ambil nilai role dari form
<<<<<<< HEAD

    // Update email, password, dan role berdasarkan iduser
    $update = mysqli_query($conn, "UPDATE login SET email='$email', password='$pass', role='$role' WHERE iduser='$id'");
    
    if ($update) {
        header('location:user.php');
    } else {
        header('location:user.php');
    }
}

=======
    
    // Cek apakah email sudah terdaftar
    $checkEmail = $conn->prepare("SELECT iduser FROM login WHERE email=? AND iduser != ?");
    $checkEmail->bind_param("si", $email, $id);
    $checkEmail->execute();
    $checkEmail->store_result();
    
    if ($checkEmail->num_rows > 0) {
        $checkEmail->close();
        // Jika email sudah ada, redirect dengan pesan error
        $message = "Email yang anda masukkan sudah terdaftar";
        echo "<script type='text/javascript'>
                window.location.href=\"/stockbarang/user.php\";
                alert('$message');
            </script>";  
        exit;
    } else {
        // Jika email belum terdaftar, lanjutkan update
        $checkEmail->close();
        // Hash password sebelum memperbarui
        $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE login SET email=?, password=?, role=? WHERE iduser=?");
        $stmt->bind_param("sssi", $email, $hashedPassword, $role, $id);
        
        if ($stmt->execute()) {
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
    $stmt->close();
}


>>>>>>> ba514db (Update 13 December)
// Fungsi untuk menghapus pengguna
if (isset($_POST['hapusUser'])) {
    $id = $_POST['id'];

<<<<<<< HEAD
    // Hapus pengguna berdasarkan iduser
    $delete = mysqli_query($conn, "DELETE FROM login WHERE iduser='$id'");
    
    if ($delete) {
=======
    // Gunakan prepared statement untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM login WHERE iduser=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
>>>>>>> ba514db (Update 13 December)
        header('location:user.php');
    } else {
        header('location:user.php');
    }
<<<<<<< HEAD
=======
    $stmt->close();
>>>>>>> ba514db (Update 13 December)
}
?>
