<?php
require_once "./Connection/function.php";
require_once "./control/crudBarangController.php";

class crudUser {
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

    public function updateBarangUser($id, $email) {
        $crudBarang = new crudBarang($this->conn);
        $name = $crudBarang->getOwnerName($email);
        if (!$name) {
            return [
                'success' => false,
                'error' => 'Failed to get owner name'
            ];
        }
        $stmt = $this->conn->prepare("UPDATE barang SET name=? WHERE owner_id=?");
        $stmt->bind_param("si", $name, $id);
        
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

    public function deleteBarangUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM barang WHERE owner_id=?");
        $stmt->bind_param("i", $id);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return [
            'success' => $result,
            'message' => $result ? "Barang berhasil dihapus" : "Gagal menghapus barang"
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
        if ($excludeId !== null) {
            $stmt = $this->conn->prepare("SELECT iduser FROM login WHERE email = ? AND iduser != ?");
            // Create references for bind_param
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt = $this->conn->prepare("SELECT iduser FROM login WHERE email = ?");
            // Create reference for bind_param
            $stmt->bind_param("s", $email);
        }
        
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
}