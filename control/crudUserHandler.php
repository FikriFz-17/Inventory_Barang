<?php
require_once "./control/crudUserController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userManager = new crudUser($conn);
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

function getAllUsers($userManager) {
    return $userManager->getAllUsers();
}
