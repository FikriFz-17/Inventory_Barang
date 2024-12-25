<?php
require_once "./Connection/function.php";

class UserManagement {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function addUser($email, $password, $role) {
        // Check if email already exists
        if ($this->isEmailExists($email)) {
            return [
                'success' => false,
                'message' => "Email yang anda masukkan sudah terdaftar"
            ];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and execute insert statement
        $stmt = $this->conn->prepare("INSERT INTO login (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $role);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return [
            'success' => $result,
            'message' => $result ? "User berhasil ditambahkan" : "Gagal menambahkan user"
        ];
    }
    
    public function updateUser($id, $email, $password, $role) {
        // Check if email exists for other users
        if ($this->isEmailExists($email, $id)) {
            return [
                'success' => false,
                'message' => "Email yang anda masukkan sudah terdaftar"
            ];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update user data
        $stmt = $this->conn->prepare("UPDATE login SET email=?, password=?, role=? WHERE iduser=?");
        $stmt->bind_param("sssi", $email, $hashedPassword, $role, $id);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return [
            'success' => $result,
            'message' => $result ? "Data berhasil diperbarui" : "Gagal memperbarui data"
        ];
    }
    
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM login WHERE iduser=?");
        $stmt->bind_param("i", $id);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return [
            'success' => $result,
            'message' => $result ? "User berhasil dihapus" : "Gagal menghapus user"
        ];
    }
    
    public function getAllUsers() {
        $query = "SELECT * FROM login";
        $result = mysqli_query($this->conn, $query);
        $users = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    private function isEmailExists($email, $excludeId = null) {
        $query = "SELECT iduser FROM login WHERE email = ?";
        $params = ["s", $email];
        
        if ($excludeId !== null) {
            $query .= " AND iduser != ?";
            $params = ["si", $email, $excludeId];
        }
        
        $stmt = $this->conn->prepare($query);
        call_user_func_array([$stmt, 'bind_param'], $params);
        $stmt->execute();
        $stmt->store_result();
        
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userManager = new UserManagement($conn);
    $result = null;
    
    if (isset($_POST['add'])) {
        $result = $userManager->addUser($_POST['email'], $_POST['password'], $_POST['role']);
    } 
    elseif (isset($_POST['updateUser'])) {
        $result = $userManager->updateUser($_POST['id'], $_POST['email'], $_POST['pass'], $_POST['role']);
    }
    elseif (isset($_POST['hapusUser'])) {
        $result = $userManager->deleteUser($_POST['id']);
    }
    
    // Handle response
    if ($result !== null) {
        echo "<script type='text/javascript'>
                window.location.href='/stockbarang/user.php';
                alert('" . $result['message'] . "');
              </script>";
        exit;
    }
}
?>