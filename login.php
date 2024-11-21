<?php 
session_start();
require "Connection/function.php";

// Cek apakah pengguna sudah login
if (isset($_SESSION['login']) && $_SESSION['login'] === TRUE) {
    // Redirect berdasarkan role yang tersimpan di session
    if ($_SESSION['role'] === "Admin") {
        header('location:index.php');
        exit;
    } elseif ($_SESSION['role'] === "User") {
        header('location:index2.php');
        exit;
    }
}

// Cek login terdaftar atau tidak
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validasi input kosong
    if (empty($email)) {
        header('location:login.php?error=Email+harus+diisi');
        exit;
    }

    if (empty($pass)) {
        header('location:login.php?error=Password+harus+diisi');
        exit;
    }

    if (empty($role)) {
        header('location:login.php?error=Harap+masukkan+role');
        exit;
    }

    // Periksa email, password, dan role di database
    $cekdata = mysqli_query($conn, "SELECT iduser, role FROM login WHERE email='$email' AND password='$pass'");
    $data = mysqli_fetch_array($cekdata);

    if ($data && $data['role'] == $role) {
        $_SESSION['login'] = TRUE;
        $_SESSION['email'] = $email;
        $_SESSION['userId'] = $data["iduser"];
        $_SESSION['role'] = $data['role'];
        
        // Redirect berdasarkan role
        if ($role == "Admin") {
            header('location:index.php');
        } elseif ($role == "User") {
            header('location:index2.php');
        }
        exit;
    } else {
        header('location:login.php?error=Credential+atau+role+tidak+valid');
        exit;
    }
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
        <title>Login</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                    <div class="card-body">
                                        <form method="post">
                                        <?php 
                                            if (isset($_GET['error'])) { // Periksa apakah parameter 'error' ada di URL
                                                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
                                            }
                                        ?>
                                            
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputEmailAddress">Email</label>
                                                <input class="form-control py-4" name="email" id="  inputEmailAddress" type="email" placeholder="Enter email address" />
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputPassword">Password</label>
                                                <input class="form-control py-4" name="password" id="inputPassword" type="password" placeholder="Enter password" />
                                            </div>
                                            <div class="mb-1">
                                                <label class="form-label">Select User Type</label>
                                            </div>
                                            <select class="form-control mb-3" aria-label="Default select example" name="role">
                                                <option value="User" selected>User</option>
                                                <option value="Admin">Admin</option>
                                            </select>
                                            <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button class="btn btn-primary" name="login">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="small">
                                                <a href="wa.php" target="_blank">
                                                    Need an account? Sign up!
                                                </a>
                                        </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
