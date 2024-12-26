<?php
require_once "./Connection/function.php";


class crudBarang {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function validateStock($stock) {
        return $stock > 0;
    }
    
    public function validateInput($jenis, $namaBarang, $owner = null) {
        if (is_numeric($jenis) || is_numeric($namaBarang)) {
            return false;
        }
        if ($owner !== null && is_numeric($owner)) {
            return false;
        }
        return true;
    }
    
    public function getOwnerName($email) {
        $result = mysqli_query($this->conn, "SELECT SUBSTRING_INDEX('$email', '@', 1) AS owner_name");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row['owner_name'];
        }
        return false;
    }
    
    public function addBarang($kodeb, $namaBarang, $jenis, $stock, $ownerId, $email) {
        if (!$this->validateStock($stock)) {
            return [
                'success' => false,
                'error' => 'Stock barang tidak valid'
            ];
        }
        
        $name = $this->getOwnerName($email);
        if (!$name) {
            return [
                'success' => false,
                'error' => 'Failed to get owner name'
            ];
        }
        
        $query = "INSERT INTO barang (kode, namabarang, jenisbarang, stock, owner_id, name) 
                  VALUES ('$kodeb', '$namaBarang', '$jenis', '$stock', '$ownerId', '$name')";
        
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            return [
                'success' => true,
                'message' => 'Barang berhasil ditambahkan'
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Failed to add barang'
        ];
    }
    
    public function updateBarang($id, $namaBarang, $jenis, $stock, $owner, $ownerId, $isAdmin = false) {
        if (!$this->validateStock($stock)) {
            return [
                'success' => false,
                'error' => 'Stock barang tidak valid'
            ];
        }
        
        if (!$this->validateInput($jenis, $namaBarang, $owner)) {
            $errorMsg = $isAdmin 
                ? "Jenis barang, nama barang, dan nama pemilik tidak boleh angka saja."
                : "Jenis barang dan nama barang tidak boleh angka saja.";
            
            return [
                'success' => false,
                'error' => $errorMsg
            ];
        }
        
        $query = $isAdmin
            ? "UPDATE barang SET namabarang='$namaBarang', jenisbarang='$jenis', stock=$stock, name='$owner' WHERE idbarang='$id'"
            : "UPDATE barang SET namabarang='$namaBarang', jenisbarang='$jenis', stock=$stock, name='$owner' WHERE idbarang='$id' AND owner_id='$ownerId'";
        
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            return [
                'success' => true,
                'message' => 'Barang berhasil diubah'
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Failed to update barang'
        ];
    }
    
    public function deleteBarang($id, $ownerId, $isAdmin = false) {
        $query = $isAdmin
            ? "DELETE FROM barang WHERE idbarang='$id'"
            : "DELETE FROM barang WHERE idbarang='$id' AND owner_id='$ownerId'";
        
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            return [
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Failed to delete barang'
        ];
    }
    
    public function getAllBarang($userId = null, $isAdmin = true) {
        $query = $isAdmin
            ? "SELECT * FROM barang"
            : "SELECT * FROM barang WHERE owner_id='$userId'";
        return mysqli_query($this->conn, $query);
    }
}
?>