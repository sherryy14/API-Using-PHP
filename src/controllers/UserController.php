<?php

namespace App\Controllers;

use PDO;

class UserController {
    private $pdo;

    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=api_test';
        $username = 'root';
        $password = '';
        $this->pdo = new PDO($dsn, $username, $password);
    }

    public function index() {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    }

    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $stmt->execute(['name' => $input['name'], 'email' => $input['email']]);
        echo json_encode(['id' => $this->pdo->lastInsertId()]);
    }

    public function show($vars) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $vars['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($user);
    }

    public function update($vars) {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->execute(['name' => $input['name'], 'email' => $input['email'], 'id' => $vars['id']]);
        echo json_encode(['status' => 'User updated']);
    }

    public function destroy($vars) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $vars['id']]);
        echo json_encode(['status' => 'User deleted']);
    }
}
