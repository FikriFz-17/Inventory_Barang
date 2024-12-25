<?php
namespace App;

use App\Database\DatabaseInterface;

class UserManagement {
    private $db;
    
    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }
    
    public function addUser(string $email, string $password, string $role): array {
        if ($this->isEmailExists($email)) {
            return [
                'success' => false,
                'message' => "Email yang anda masukkan sudah terdaftar"
            ];
        }
        
        $hashedPassword = $this->hashPassword($password);
        
        $stmt = $this->db->prepare("INSERT INTO login (email, password, role) VALUES (?, ?, ?)");
        if (!$stmt) {
            return [
                'success' => false,
                'message' => "Database preparation failed"
            ];
        }
        
        $stmt->bind_param("sss", $email, $hashedPassword, $role);
        $result = $stmt->execute();
        $stmt->close();
        
        return [
            'success' => $result,
            'message' => $result ? "User berhasil ditambahkan" : "Gagal menambahkan user"
        ];
    }
    
    public function updateUser(int $id, string $email, string $password, string $role): array {
        if ($this->isEmailExists($email, $id)) {
            return [
                'success' => false,
                'message' => "Email yang anda masukkan sudah terdaftar"
            ];
        }
        
        $hashedPassword = $this->hashPassword($password);
        
        $stmt = $this->db->prepare("UPDATE login SET email=?, password=?, role=? WHERE iduser=?");
        if (!$stmt) {
            return [
                'success' => false,
                'message' => "Database preparation failed"
            ];
        }
        
        $stmt->bind_param("sssi", $email, $hashedPassword, $role, $id);
        $result = $stmt->execute();
        $stmt->close();
        
        return [
            'success' => $result,
            'message' => $result ? "Data berhasil diperbarui" : "Gagal memperbarui data"
        ];
    }
    
    public function deleteUser(int $id): array {
        $stmt = $this->db->prepare("DELETE FROM login WHERE iduser=?");
        if (!$stmt) {
            return [
                'success' => false,
                'message' => "Database preparation failed"
            ];
        }
        
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        
        return [
            'success' => $result,
            'message' => $result ? "User berhasil dihapus" : "Gagal menghapus user"
        ];
    }
    
    public function getAllUsers(): array {
        $result = $this->db->query("SELECT * FROM login");
        $users = [];
        
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    protected function isEmailExists(string $email, ?int $excludeId = null): bool {
        $query = "SELECT iduser FROM login WHERE email = ?";
        $params = ["s", $email];
        
        if ($excludeId !== null) {
            $query .= " AND iduser != ?";
            $params = ["si", $email, $excludeId];
        }
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }
        
        call_user_func_array([$stmt, 'bind_param'], $params);
        $stmt->execute();
        $stmt->store_result();
        
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
    
    protected function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}