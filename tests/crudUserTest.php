<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\UserManagement;
use App\Database\DatabaseInterface;

class MockDatabase implements DatabaseInterface {
    private $shouldSucceed;
    private $returnRows;
    
    public function __construct(bool $shouldSucceed = true, array $returnRows = []) {
        $this->shouldSucceed = $shouldSucceed;
        $this->returnRows = $returnRows;
    }
    
    public function prepare($query) {
        return new MockStatement($this->shouldSucceed);
    }
    
    public function query($query) {
        return new MockResult($this->returnRows);
    }
}

class MockStatement {
    private $shouldSucceed;
    
    public function __construct(bool $shouldSucceed) {
        $this->shouldSucceed = $shouldSucceed;
    }
    
    public function bind_param() {
        return true;
    }
    
    public function execute() {
        return $this->shouldSucceed;
    }
    
    public function store_result() {
        return true;
    }
    
    public function close() {
        return true;
    }
    
    public $num_rows = 0;
}

class MockResult {
    private $rows;
    private $position = 0;
    
    public function __construct(array $rows) {
        $this->rows = $rows;
    }
    
    public function fetch_assoc() {
        if ($this->position >= count($this->rows)) {
            return null;
        }
        return $this->rows[$this->position++];
    }
}

class crudUserTest extends TestCase {
    private $userManagement;
    private $mockDb;
    
    protected function setUp(): void {
        $this->mockDb = new MockDatabase();
        $this->userManagement = new UserManagement($this->mockDb);
    }
    
    public function testAddUserSuccess() {
        $result = $this->userManagement->addUser(
            'test@example.com',
            'password123',
            'User'
        );
        
        $this->assertTrue($result['success']);
        $this->assertEquals("User berhasil ditambahkan", $result['message']);
    }
    
    public function testAddUserFailure() {
        $mockDb = new MockDatabase(false);
        $userManagement = new UserManagement($mockDb);
        
        $result = $userManagement->addUser(
            'test@example.com',
            'password123',
            'User'
        );
        
        $this->assertFalse($result['success']);
        $this->assertEquals("Gagal menambahkan user", $result['message']);
    }
    
    public function testUpdateUserSuccess() {
        $result = $this->userManagement->updateUser(
            1,
            'test@example.com',
            'newpassword123',
            'Admin'
        );
        
        $this->assertTrue($result['success']);
        $this->assertEquals("Data berhasil diperbarui", $result['message']);
    }
    
    public function testUpdateUserFailure() {
        $mockDb = new MockDatabase(false);
        $userManagement = new UserManagement($mockDb);
        
        $result = $userManagement->updateUser(
            1,
            'test@example.com',
            'newpassword123',
            'Admin'
        );
        
        $this->assertFalse($result['success']);
        $this->assertEquals("Gagal memperbarui data", $result['message']);
    }
    
    public function testDeleteUserSuccess() {
        $result = $this->userManagement->deleteUser(1);
        
        $this->assertTrue($result['success']);
        $this->assertEquals("User berhasil dihapus", $result['message']);
    }
    
    public function testDeleteUserFailure() {
        $mockDb = new MockDatabase(false);
        $userManagement = new UserManagement($mockDb);
        
        $result = $userManagement->deleteUser(1);
        
        $this->assertFalse($result['success']);
        $this->assertEquals("Gagal menghapus user", $result['message']);
    }
    
    public function testGetAllUsers() {
        $mockData = [
            ['iduser' => 1, 'email' => 'test1@example.com', 'role' => 'User'],
            ['iduser' => 2, 'email' => 'test2@example.com', 'role' => 'Admin']
        ];
        
        $mockDb = new MockDatabase(true, $mockData);
        $userManagement = new UserManagement($mockDb);
        
        $result = $userManagement->getAllUsers();
        
        $this->assertEquals($mockData, $result);
    }
}
?>