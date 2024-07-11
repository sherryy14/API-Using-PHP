<?php
// Database.php

class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "api_test";
    private $conn;

    // Constructor to initialize database connection
    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to get the database connection
    public function getConnection() {
        return $this->conn;
    }

    // Close the database connection
    public function closeConnection() {
        $this->conn->close();
    }
}
?>
