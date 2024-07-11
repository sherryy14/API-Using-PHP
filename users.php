<?php
require_once 'public/Database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Start building the HTML output
    $output = '';

    // Loop through each row of data
    while ($row = $result->fetch_assoc()) {
        $output .= '<tr>';
        $output .= '<th scope="row">' . $row['id'] . '</th>';
        $output .= '<td>' . $row['name'] . '</td>';
        $output .= '<td>' . $row['email'] . '</td>';
        $output .= '<td><button class="btn btn-primary edit-btn" data-id="' . $row['id'] . '">Edit</button>
                              <button class="btn btn-danger delete-btn" data-id="' . $row['id'] . '">Delete</button></td>';
        $output .= '</tr>';
    }

    // Output the generated HTML
    echo $output;
} else {
    // No users found
    echo '<tr><td colspan="4">No users found</td></tr>';
}

// Close database connection
$database->closeConnection();
?>
