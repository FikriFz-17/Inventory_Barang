<?php
require_once "./control/crudBarangController.php";

class crudBarangHandler {
    private $controller;
    private $userRole;
    
    public function __construct($connection, $userRole) {
        $this->controller = new crudBarang($connection);
        $this->userRole = $userRole;
    }
    
    private function redirectBasedOnRole() {
        header('location:' . ($this->userRole == "Admin" ? 'index.php' : 'index2.php'));
        exit;
    }
    
    private function setErrorMessage($message) {
        $_SESSION['errorMessage'] = $message;
        $this->redirectBasedOnRole();
    }
    private function setSuccessMessage($message) {
        $_SESSION['successMessage'] = $message;
        $this->redirectBasedOnRole();
    }
    
    public function handleAddRequest($postData) {
        $result = $this->controller->addBarang(
            $postData['kodeBarang'],
            $postData['namaBarang'],
            $postData['jenis'],
            $postData['stock'],
            $postData['userId'],
            $postData['email']
        );
        
        if (!$result['success']) {
            $this->setErrorMessage($result['error']);
        } else {
            $this->setSuccessMessage($result['message']);
        }
        
        $this->redirectBasedOnRole();
    }
    
    public function handleUpdateRequest($postData) {
        $isAdmin = $this->userRole == "Admin";
        $result = $this->controller->updateBarang(
            $postData['idb'],
            $postData['namaBarang'],
            $postData['jenis'],
            $postData['stock'],
            $postData['owner'],
            $postData['userId'],
            $isAdmin
        );
        
        if (!$result['success']) {
            $this->setErrorMessage($result['error']);
        } else {
            $this->setSuccessMessage($result['message']);
        }
        
        $this->redirectBasedOnRole();
    }
    
    public function handleDeleteRequest($postData) {
        $isAdmin = $this->userRole == "Admin";
        $result = $this->controller->deleteBarang(
            $postData['idb'],
            $postData['userId'],
            $isAdmin
        );
        
        if (!$result['success']) {
            $this->setErrorMessage($result['error']);
        } else {
            $this->setSuccessMessage($result['message']);
        }
        
        $this->redirectBasedOnRole();
    }
}

// Initialize handler and process requests
if (isset($_SESSION['role'])) {
    $handler = new crudBarangHandler($conn, $_SESSION['role']);
    
    if (isset($_POST['add'])) {
        $handler->handleAddRequest($_POST);
    }
    
    if (isset($_POST['updateBarang'])) {
        $handler->handleUpdateRequest($_POST);
    }
    
    if (isset($_POST['hapusBarang'])) {
        $handler->handleDeleteRequest($_POST);
    }
}
?>