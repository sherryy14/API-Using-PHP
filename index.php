<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Users API</title>
</head>

<body>
    <div class="container py-3">
        <h3 class="text-center">Add/Edit User</h3>
        <div id="messageContainer" class="mt-3">
            <!-- Messages from API response will be displayed here -->
        </div>
        <form id="userForm" class="row justify-content-center g-3 py-3">
            <input type="hidden" name="id" id="userId">
            <div class="col-md-4">
                <label for="validationDefault01" class="form-label">Name</label>
                <input type="text" name="name" class="form-control" id="validationDefault01" required>
            </div>
            <div class="col-md-4">
                <label for="validationDefault02" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="validationDefault02" required>
            </div>
            <div class="col-12 d-flex justify-content-center">
                <button id="submitButton" class="btn btn-primary" type="submit">Create</button>
                <button id="updateButton" class="btn btn-success ms-2 d-none" type="button">Update</button>
                <button id="deleteButton" class="btn btn-danger ms-2 d-none" type="button">Delete</button>
            </div>
        </form>
        <h3 class="text-center">Users List</h3>
        <table id="usersTable" class="table py-3">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
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
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<th scope="row">' . $row['id'] . '</th>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '<td><button class="btn btn-primary edit-btn"  data-id="' . $row['id'] . '" data-name="' . $row['name'] . '" data-email="' . $row['email'] . '">Edit</button>
                              <button class="btn btn-danger delete-btn" data-id="' . $row['id'] . '">Delete</button></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No users found</td></tr>';
                }

                // Close database connection
                $database->closeConnection();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-fTTSHimB00bMbq6z5lRB5vOuM3fZECwhsVV4ZdA7ntzWezWT6GZtJ6m3KnbBoM+t" crossorigin="anonymous"></script>

    <!-- jQuery for AJAX handling -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            // Handle form submission for creating and updating users
            $('#userForm').submit(function(event) {
                event.preventDefault(); // Prevent the form from submitting normally

                var formData = $(this).serialize();
                var url = 'http://localhost/api-test/public/users';

                var userId = $('#userId').val();
                var method = userId ? 'PUT' : 'POST';
                if (userId) {
                    url += '?id=' + userId;
                }

                $.ajax({
                    url: url,
                    type: method,
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        // Clear previous messages
                        $('#messageContainer').empty();

                        // Display success message
                        $('#messageContainer').append('<div class="alert alert-success">' + response.message + '</div>');

                        // Clear form inputs
                        $('#userForm')[0].reset();

                        // Refresh users list
                        refreshUsersList();
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        $('#messageContainer').empty().append('<div class="alert alert-danger">Error: ' + xhr.responseText + '</div>');
                    }
                });
            });
            


            // Handle edit button click
            $('#usersTable').on('click', '.edit-btn', function(event) {
                event.preventDefault();
                var userName = $(this).data('name');
                var userEmail = $(this).data('email');

                // Populate form fields with user data
                $('#validationDefault01').val(userName);
                $('#validationDefault02').val(userEmail);

                var userId = $(this).data('id');
                $('#userId').val(userId);

                // Fetch user details and populate form for editing
                $.ajax({
                    url: 'http://localhost/api-test/public/users?id=' + userId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#validationDefault01').val(response.name);
                        $('#validationDefault02').val(response.email);

                        // Switch button visibility
                        $('#submitButton').addClass('d-none');
                        $('#updateButton').removeClass('d-none');
                        $('#deleteButton').removeClass('d-none');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching user details:', error);
                    }
                });
            });

            // Handle update button click
            $('#updateButton').click(function() {
                $('#userForm').submit(); // Submit form for update
            });

            $('#deleteButton').click(function() {
                var userId = $('#userId').val();

                if (!confirm('Are you sure you want to delete this user?')) {
                    return;
                }

                // Make sure to include the `id` parameter in the URL for DELETE request
                $.ajax({
                    url: 'http://localhost/api-test/public/users',
                    type: 'DELETE',
                    dataType: 'json',
                    data: {
                        id: userId
                    }, // Pass `id` as data
                    success: function(response) {
                        // Clear previous messages
                        $('#messageContainer').empty();

                        // Display success message
                        $('#messageContainer').append('<div class="alert alert-success">' + response.message + '</div>');

                        // Clear form inputs
                        $('#userForm')[0].reset();

                        // Refresh users list
                        refreshUsersList();

                        // Reset form and button visibility
                        $('#submitButton').removeClass('d-none');
                        $('#updateButton').addClass('d-none');
                        $('#deleteButton').addClass('d-none');
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        $('#messageContainer').empty().append('<div class="alert alert-danger">Error: ' + xhr.responseText + '</div>');
                    }
                });
            });

            // Handle delete button click
            $('#usersTable').on('click', '.delete-btn', function() {
                var userId = $(this).data('id');
                $('#userId').val(userId);
                $('#deleteButton').click();
            });

            // Function to refresh users list
            function refreshUsersList() {
                $.ajax({
                    url: 'users.php',
                    type: 'GET',
                    success: function(data) {
                        // Update the users table
                        $('#usersTable tbody').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching users:', error);
                    }
                });
            }
        });
    </script>
</body>

</html>