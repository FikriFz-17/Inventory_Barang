<?php
use PHPUnit\Framework\TestCase;
require "./control/crudUserController.php";

class crudUserTest extends TestCase {
    private $crudUser;
    private $conn;
    
    protected function setUp(): void {
        // Setup test database connection
        $this->conn = new mysqli("localhost", "root", "root123", "test_stockbarang");
        
        // Create test table if not exists
        $this->conn->query("CREATE TABLE IF NOT EXISTS login (
            iduser INT(11) AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(11) DEFAULT NULL
        )");
        
        $this->crudUser = new crudUser($this->conn);
        
        // Clear existing test data
        $this->conn->query("TRUNCATE TABLE login");
    }
    
    protected function tearDown(): void {
        // Clean up
        $this->conn->query("TRUNCATE TABLE login");
        $this->conn->close();
    }
    
    public function testAddUserSuccess() {
        // Test adding new user
        $result = $this->crudUser->addUser(
            'test@example.com',
            'TestPass123!',
            'User'
        );
        
        $this->assertTrue($result['success']);
        $this->assertEquals("User berhasil ditambahkan", $result['message']);
        
        // Verify user was added to database
        $stmt = $this->conn->prepare("SELECT * FROM login WHERE email = ?");
        $email = 'test@example.com';
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $queryResult = $stmt->get_result();
        $user = $queryResult->fetch_assoc();
        
        $this->assertNotNull($user);
        $this->assertEquals('test@example.com', $user['email']);
        $this->assertEquals('User', $user['role']);
        $this->assertTrue(password_verify('TestPass123!', $user['password']));
    }
    
    public function testAddUserDuplicateEmail() {
        // First add a user
        $this->crudUser->addUser('test@example.com', 'TestPass123!', 'User');
        
        // Try to add another user with same email
        $result = $this->crudUser->addUser(
            'test@example.com',
            'DifferentPass123!',
            'Admin'
        );
        
        $this->assertFalse($result['success']);
        $this->assertEquals("Email yang anda masukkan sudah terdaftar", $result['message']);
    }
    
    public function testUpdateUserSuccess() {
        // First add a user
        $this->crudUser->addUser('test@example.com', 'TestPass123!', 'User');
        
        // Get the user's ID
        $stmt = $this->conn->prepare("SELECT iduser FROM login WHERE email = ?");
        $email = 'test@example.com';
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Update the user
        $result = $this->crudUser->updateUser(
            $user['iduser'],
            'updated@example.com',
            'NewPass123!',
            'Admin'
        );
        
        $this->assertTrue($result['success']);
        $this->assertEquals("Data berhasil diperbarui", $result['message']);
        
        // Verify the update
        $stmt = $this->conn->prepare("SELECT * FROM login WHERE iduser = ?");
        $stmt->bind_param("i", $user['iduser']);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedUser = $result->fetch_assoc();
        
        $this->assertEquals('updated@example.com', $updatedUser['email']);
        $this->assertEquals('Admin', $updatedUser['role']);
        $this->assertTrue(password_verify('NewPass123!', $updatedUser['password']));
    }
    
    public function testDeleteUserSuccess() {
        // First add a user
        $this->crudUser->addUser('test@example.com', 'TestPass123!', 'User');
        
        // Get the user's ID
        $stmt = $this->conn->prepare("SELECT iduser FROM login WHERE email = ?");
        $email = 'test@example.com';
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Delete the user
        $result = $this->crudUser->deleteUser($user['iduser']);
        
        $this->assertTrue($result['success']);
        $this->assertEquals("User berhasil dihapus", $result['message']);
        
        // Verify the user was deleted
        $stmt = $this->conn->prepare("SELECT * FROM login WHERE iduser = ?");
        $stmt->bind_param("i", $user['iduser']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $this->assertEquals(0, $result->num_rows);
    }
    
    public function testGetAllUsers() {
        // Add multiple test users
        $testUsers = [
            ['email' => 'user1@example.com', 'password' => 'Pass123!', 'role' => 'User'],
            ['email' => 'user2@example.com', 'password' => 'Pass123!', 'role' => 'Admin']
        ];
        
        foreach ($testUsers as $user) {
            $this->crudUser->addUser($user['email'], $user['password'], $user['role']);
        }
        
        // Get all users
        $users = $this->crudUser->getAllUsers();
        
        $this->assertCount(2, $users);
        $this->assertEquals('user1@example.com', $users[0]['email']);
        $this->assertEquals('user2@example.com', $users[1]['email']);
    }
}