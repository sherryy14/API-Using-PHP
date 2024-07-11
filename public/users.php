<?php
// users.php

require_once 'Database.php';

// Create a new instance of the Database class
$database = new Database();
$conn = $database->getConnection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set headers for JSON response
header('Content-Type: application/json');

// Determine HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all users
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Fetch users from result set
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        } else {
            // No users found
            http_response_code(404);
            echo json_encode(["message" => "No users found"]);
        }
        break;

    case 'POST':
        // Create a new user
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';

        if (!$name || !$email) {
            http_response_code(400); // Bad request
            echo json_encode(["message" => "Missing required parameters"]);
            break;
        }

        // Insert query
        $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
        
        if ($conn->query($sql) === TRUE) {
            // Get the ID of the newly inserted user
            $newUserId = $conn->insert_id;
            http_response_code(201); // Created
            echo json_encode(["message" => "User created successfully", "id" => $newUserId]);
        } else {
            http_response_code(500); // Server error
            echo json_encode(["message" => "Error: " . $conn->error]);
        }
        break;

    case 'PUT':
        // Update an existing user
        parse_str(file_get_contents("php://input"), $_PUT);

        $id = isset($_PUT['id']) ? $_PUT['id'] : '';
        $name = isset($_PUT['name']) ? $_PUT['name'] : '';
        $email = isset($_PUT['email']) ? $_PUT['email'] : '';
        

        if (!$id || !$name || !$email) {
            http_response_code(400); // Bad request
            echo json_encode(["message" => "Missing required parameters"]);
            break;
        }

        // Update query
        $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            http_response_code(200); // OK
            echo json_encode(["message" => "User updated successfully"]);
        } else {
            http_response_code(500); // Server error
            echo json_encode(["message" => "Error updating user: " . $conn->error]);
        }
        break;

    case 'DELETE':
        // Delete an existing user
        parse_str(file_get_contents("php://input"), $_DELETE);

        $id = isset($_DELETE['id']) ? $_DELETE['id'] : '';

        if (!$id) {
            http_response_code(400); // Bad request
            echo json_encode(["message" => "Missing required parameter: id"]);
            break;
        }

        // Delete query
        $sql = "DELETE FROM users WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            http_response_code(200); // OK
            echo json_encode(["message" => "User deleted successfully"]);
        } else {
            http_response_code(500); // Server error
            echo json_encode(["message" => "Error deleting user: " . $conn->error]);
        }
        break;

    default:
        // Method not allowed
        http_response_code(405); // Method Not Allowed
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

// Close database connection
$database->closeConnection();
?>
