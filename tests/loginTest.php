<?php
use PHPUnit\Framework\TestCase;
require "./control/login.php";

class LoginTest extends TestCase
{
    private $conn;
    private $login;

    protected function setUp(): void
    {
        // Setup test database connection
        $this->conn = new mysqli("localhost", "root", "root123", "test_stockbarang");

        // Create test table if not exists
        $this->conn->query("CREATE TABLE IF NOT EXISTS login (
            iduser INT(11) AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(11) DEFAULT NULL
        )");

        $this->login = new Login($this->conn);

        // Clear existing test data
        $this->conn->query("TRUNCATE TABLE login");
    }

    protected function tearDown(): void
    {
        // Clean up
        $this->conn->query("TRUNCATE TABLE login");
        $this->conn->close();
    }

    public function testAuthenticateWithValidCredentials()
    {
        $email = "test@example.com";
        $password = "validPassword";
        $role = "admin";

        // Insert a valid user into the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO login (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $role);
        $stmt->execute();

        $this->assertNull($this->login->authenticate($email, $password, $role));
    }

    public function testAuthenticateWithInvalidPassword()
    {
        $email = "test@example.com";
        $password = "invalidPassword";
        $role = "admin";

        // Insert a valid user into the database
        $hashedPassword = password_hash("validPassword", PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO login (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $role);
        $stmt->execute();

        $this->assertEquals("Credential atau role tidak valid", $this->login->authenticate($email, $password, $role));
    }

    public function testAuthenticateWithMissingEmail()
    {
        $email = "";
        $password = "password";
        $role = "admin";

        $this->assertEquals("Email harus diisi", $this->login->authenticate($email, $password, $role));
    }

    public function testAuthenticateWithMissingPassword()
    {
        $email = "test@example.com";
        $password = "";
        $role = "admin";

        $this->assertEquals("Password harus diisi", $this->login->authenticate($email, $password, $role));
    }
    
    public function testAuthenticateUserNotFound()
    {
        $email = "notfound@example.com";
        $password = "password";
        $role = "admin";

        $this->assertEquals("Credential atau role tidak valid", $this->login->authenticate($email, $password, $role));
    }
}
