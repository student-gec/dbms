<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = ""; //YOUR DB NAME HERE

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Create operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    $employee_ssn = $_POST["employee_ssn"];
    $name = $_POST["name"];
    $salary = $_POST["salary"];
    $department_id = $_POST["department_id"];

    $stmt = $conn->prepare("INSERT INTO employees (employee_ssn, name, salary, department_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $employee_ssn, $name, $salary, $department_id);

    if ($stmt->execute()) {
        echo "Employee created successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
//handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $employee_ssn = $_POST["employee_ssn"];
    $name = $_POST["name"];
    $salary = $_POST["salary"];
    $department_id = $_POST["department_id"];

    $stmt = $conn->prepare("UPDATE employees SET employee_ssn = ?, name = ?, salary = ?, department_id = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $employee_ssn, $name, $salary, $department_id, $id);

    if ($stmt->execute()) {
        echo "Employee updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// Handle Delete operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["id"];

    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Employee deleted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// Handle Search or Fetch All
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $searchTerm = $_POST["searchTerm"];
    $sql = "SELECT employees.id, employee_ssn, employees.name, salary, departments.name AS department_name, location
            FROM employees
            LEFT JOIN departments ON employees.department_id = departments.id
            WHERE employees.name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%$searchTerm%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $resultEmployees = $stmt->get_result();
    $stmt->close();
} else {
    $sql = "SELECT employees.id, employee_ssn, employees.name, salary, departments.name AS department_name, location
            FROM employees
            LEFT JOIN departments ON employees.department_id = departments.id";
    $resultEmployees = $conn->query($sql);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Add Employee</h2>
<form method="post">
    <label for="employeeSSN">Employee SSN:</label>
    <input type="text" id="employeeSSN" name="employee_ssn" required><br>

    <label for="employeeName">Employee Name:</label>
    <input type="text" id="employeeName" name="name" required><br>

    <label for="employeeSalary">Employee Salary:</label>
    <input type="text" id="employeeSalary" name="salary" required><br>

    <label for="employeeDepartment">Employee Department ID:</label>
    <input type="text" id="employeeDepartment" name="department_id" required><br>

    <input type="submit" name="create" value="Add Employee">
</form>

<h2>Search Employee</h2>
<form method="post">
    <label for="searchTerm">Search by Employee Name:</label>
    <input type="text" id="searchTerm" name="searchTerm" required>
    <input type="submit" name="search" value="Search">
</form>

<h2>Employee List</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Employee SSN</th>
        <th>Name</th>
        <th>Salary</th>
        <th>Department</th>
        <th>Location</th>
        <th>Actions</th>
    </tr>
    <?php
    if (isset($resultEmployees) && $resultEmployees->num_rows > 0) {
        while ($row = $resultEmployees->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["employee_ssn"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["salary"] . "</td>";
            echo "<td>" . $row["department_name"] . "</td>";
            echo "<td>" . $row["location"] . "</td>";
            echo "<td>";
            echo "<form method='post' style='display:inline-block;'>
                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                    <input type='text' name='employee_ssn' value='" . $row["employee_ssn"] . "' required>
                    <input type='text' name='name' value='" . $row["name"] . "' required>
                    <input type='text' name='salary' value='" . $row["salary"] . "' required>
                    <input type='text' name='department_id' value='" . $row["department_name"] . "' required>
                    <input type='submit' name='update' value='Update'>
                  </form>
                  <form method='post' style='display:inline-block;'>
                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                    <input type='submit' name='delete' value='Delete'>
                  </form>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No employees found.</td></tr>";
    }
    ?>
</table>

</body>
</html>
