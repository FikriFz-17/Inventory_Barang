<?php
use PHPUnit\Framework\TestCase;
require "./control/crudBarangController.php";

class CrudBarangTest extends TestCase
{
    private $crudBarang;
    private $conn;
    
    protected function setUp(): void
    {
        // Setup test database connection
        $this->conn = new mysqli("localhost", "root", "root123", "test_stockbarang");
        
        // Create test table if not exists
        $this->conn->query("CREATE TABLE IF NOT EXISTS barang (
            idbarang INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
            kode VARCHAR(50) NOT NULL,
            namabarang VARCHAR(50) NOT NULL,
            jenisbarang VARCHAR(25) NOT NULL,
            stock INT(11) NOT NULL,
            owner_id INT(11) NOT NULL,
            name VARCHAR(11) DEFAULT NULL
        )");
        
        $this->crudBarang = new crudBarang($this->conn);
        
        // Clear existing test data
        $this->conn->query("TRUNCATE TABLE barang");
    }
    
    protected function tearDown(): void
    {
        // Clean up
        $this->conn->query("TRUNCATE TABLE barang");
        $this->conn->close();
    }
    
    public function testAddBarangSuccess()
    {
        $result = $this->crudBarang->addBarang(
            'B001',
            'Laptop Asus',
            'Electronics',
            10,
            1,
            'test@example.com'
        );
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Barang added successfully', $result['message']);
        
        // Verify barang was added to database
        $stmt = $this->conn->prepare("SELECT * FROM barang WHERE kode = ?");
        $kode = 'B001';
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $queryResult = $stmt->get_result();
        $barang = $queryResult->fetch_assoc();
        
        $this->assertNotNull($barang);
        $this->assertEquals('Laptop Asus', $barang['namabarang']);
        $this->assertEquals('Electronics', $barang['jenisbarang']);
        $this->assertEquals(10, $barang['stock']);
        $this->assertEquals('test', $barang['name']); // From getOwnerName
    }
    
    public function testAddBarangWithInvalidStock()
    {
        $result = $this->crudBarang->addBarang(
            'B002',
            'Mouse',
            'Electronics',
            0, // Invalid stock
            1,
            'test@example.com'
        );
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Stock barang tidak valid', $result['error']);
        
        // Verify barang was not added
        $stmt = $this->conn->prepare("SELECT * FROM barang WHERE kode = ?");
        $kode = 'B002';
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $queryResult = $stmt->get_result();
        
        $this->assertEquals(0, $queryResult->num_rows);
    }
    
    public function testUpdateBarangSuccess()
    {
        // First add a barang
        $this->crudBarang->addBarang('B001', 'Laptop Asus', 'Electronics', 10, 1, 'test@example.com');
        
        // Get the barang's ID
        $stmt = $this->conn->prepare("SELECT idbarang FROM barang WHERE kode = ?");
        $kode = 'B001';
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $result = $stmt->get_result();
        $barang = $result->fetch_assoc();
        
        // Update the barang
        $result = $this->crudBarang->updateBarang(
            $barang['idbarang'],
            'Laptop Dell',
            'Computer',
            15,
            'test',
            1,
            false
        );
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Barang updated successfully', $result['message']);
        
        // Verify the update
        $stmt = $this->conn->prepare("SELECT * FROM barang WHERE idbarang = ?");
        $stmt->bind_param("i", $barang['idbarang']);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedBarang = $result->fetch_assoc();
        
        $this->assertEquals('Laptop Dell', $updatedBarang['namabarang']);
        $this->assertEquals('Computer', $updatedBarang['jenisbarang']);
        $this->assertEquals(15, $updatedBarang['stock']);
    }
    
    public function testUpdateBarangWithInvalidInput()
    {
        // First add a barang
        $this->crudBarang->addBarang('B001', 'Laptop Asus', 'Electronics', 10, 1, 'test@example.com');
        
        // Get the barang's ID
        $stmt = $this->conn->prepare("SELECT idbarang FROM barang WHERE kode = ?");
        $kode = 'B001';
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $result = $stmt->get_result();
        $barang = $result->fetch_assoc();
        
        // Try to update with invalid input
        $result = $this->crudBarang->updateBarang(
            $barang['idbarang'],
            '12345', // Invalid nama (numeric)
            'Computer',
            15,
            'test',
            1,
            false
        );
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Jenis barang dan nama barang tidak boleh angka saja.', $result['error']);
    }
    
    public function testDeleteBarangSuccess()
    {
        // First add a barang
        $this->crudBarang->addBarang('B001', 'Laptop Asus', 'Electronics', 10, 1, 'test@example.com');
        
        // Get the barang's ID
        $stmt = $this->conn->prepare("SELECT idbarang FROM barang WHERE kode = ?");
        $kode = 'B001';
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $result = $stmt->get_result();
        $barang = $result->fetch_assoc();
        
        // Delete the barang
        $result = $this->crudBarang->deleteBarang($barang['idbarang'], 1);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Barang deleted successfully', $result['message']);
        
        // Verify the barang was deleted
        $stmt = $this->conn->prepare("SELECT * FROM barang WHERE idbarang = ?");
        $stmt->bind_param("i", $barang['idbarang']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $this->assertEquals(0, $result->num_rows);
    }
    
    public function testGetAllBarang()
    {
        // Add multiple test barang
        $testBarang = [
            ['kode' => 'B001', 'nama' => 'Laptop Asus', 'jenis' => 'Electronics', 'stock' => 10],
            ['kode' => 'B002', 'nama' => 'Mouse', 'jenis' => 'Accessories', 'stock' => 20]
        ];
        
        foreach ($testBarang as $barang) {
            $this->crudBarang->addBarang(
                $barang['kode'],
                $barang['nama'],
                $barang['jenis'],
                $barang['stock'],
                1,
                'test@example.com'
            );
        }
        
        // Test getAllBarang without userId (admin view)
        $allBarang = $this->crudBarang->getAllBarang();
        $barangList = [];
        while ($row = $allBarang->fetch_assoc()) {
            $barangList[] = $row;
        }
        
        $this->assertCount(2, $barangList);
        $this->assertEquals('Laptop Asus', $barangList[0]['namabarang']);
        $this->assertEquals('Mouse', $barangList[1]['namabarang']);
        
        // Test getAllBarang with userId (user view)
        $userBarang = $this->crudBarang->getAllBarang(1);
        $userBarangList = [];
        while ($row = $userBarang->fetch_assoc()) {
            $userBarangList[] = $row;
        }
        
        $this->assertCount(2, $userBarangList);
        $this->assertEquals(1, $userBarangList[0]['owner_id']);
        $this->assertEquals(1, $userBarangList[1]['owner_id']);
    }
}
?>