<?php
namespace App\Database;

interface DatabaseInterface {
    public function prepare($query);
    public function query($query);
}