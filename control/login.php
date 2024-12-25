<?php
session_start();

class Login
{
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function authenticate($email, $password, $role)
    {
        if (empty($email)) {
            return "Email harus diisi";
        }
        if (empty($password)) {
            return "Password harus diisi";
        }
        if (empty($role)) {
            return "Harap masukkan role";
        }

        $stmt = $this->conn->prepare("SELECT iduser, password, role FROM login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($iduser, $hashedPassword, $userRole);
        if ($stmt->fetch()) {
            if (password_verify($password, $hashedPassword) && $role === $userRole) {
                $this->startSession($iduser, $email, $role);
                return null; // No error
            }
        }
        return "Credential atau role tidak valid";
    }

    private function startSession($id, $email, $role)
    {
        $_SESSION['login'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['userId'] = $id;
        $_SESSION['role'] = $role;
    }
}

// Penggunaan
require "Connection/function.php";
$login = new Login($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    $error = $login->authenticate($email, $password, $role);
    if ($error) {
        header("location:login.php?error=" . urlencode($error));
        exit;
    }

    // Redirect berdasarkan role
    if ($_SESSION['role'] === 'Admin') {
        header('location:index.php');
    } elseif ($_SESSION['role'] === 'User') {
        header('location:index2.php');
    }
    exit;
}
?>
