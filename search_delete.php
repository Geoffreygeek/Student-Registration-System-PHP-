<?php
// Database connection settings
$host = "localhost";
$username = "root";
$password = "";
$db_name = "connect2";

// Connect to the database
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a delete action is requested
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Record deleted successfully.</p>";
    } else {
        echo "<p style='color: red;'>Error deleting record: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Get the search query from the form
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Display the search results
if ($query) {
    $search_query = "SELECT students.id, students.name, students.regno, courses.course_name, students.email
                     FROM students
                     JOIN courses ON students.course_id = courses.course_id
                     WHERE students.name LIKE ? OR students.regno LIKE ?";
    $stmt = $conn->prepare($search_query);
    $search_term = "%" . $query . "%";
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table style='width: 75%; border-collapse: collapse; margin-top: 30px; background-color:gold;'>";
        echo "<tr style='background-color: #4CAF50; color: white;'><th>ID</th><th>Name</th><th>Registration Number</th><th>Course</th><th>Email</th><th>Action</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['regno']) . "</td>";
            echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td><a class='delete-button' href='?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No results found for '$query'.</p>";
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Search Form</title>
    <style>
        /* Center the form on the page */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #5a92e6;
            margin: 0;
        }

        /* Form container styling */
        .search-form-container {
            width: 350px;
            padding: 25px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0px 4px 12px rgba(231, 96, 96, 0.1);
            text-align: center;
        }

        /* Form title styling */
        .search-form-container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        /* Input field styling */
        .search-form-container input[type="text"] {
            width: 100%;
            padding: 10px 15px;
            font-size: 16px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        /* Focus effect for input */
        .search-form-container input[type="text"]:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0px 0px 5px rgba(76, 175, 80, 0.3);
        }

        /* Submit button styling */
        .search-form-container input[type="submit"] {
            width: 100%;
            padding: 10px 15px;
            font-size: 16px;
            color: white;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        /* Hover effect for submit button */
        .search-form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Table styling */
        table {
            width: 75%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .delete-button {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }

        .delete-button:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <!-- Search Form -->
    <div class="search-form-container">
        <h2>Search Students</h2>
        <form action="" method="get">
            <input type="text" name="query" placeholder="Enter name or registration number" required>
            <input type="submit" value="Search">
        </form>
    </div>

</body>
</html>
