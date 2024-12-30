<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = ""; //YOUR DB NAME HERE

$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    $employee_ssn = $_POST["employee_ssn"];
    $name = $_POST["name"];
    $salary = $_POST["salary"];
    $department_id = $_POST["department_id"];

    $sql = "INSERT INTO employees (employee_ssn, name, salary, department_id) VALUES ('$employee_ssn', '$name', '$salary', '$department_id')";

    if (mysqli_query($conn, $sql)) {
        echo "Employee created successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
//search
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $searchTerm = $_POST["searchTerm"];
    $sqlSearch = "SELECT employees.id, employee_ssn, employees.name, salary, departments.name AS department_name, location
                  FROM employees
                  LEFT JOIN departments ON employees.department_id = departments.id
                  WHERE employees.name LIKE '%$searchTerm%'";
    $resultEmployees = mysqli_query($conn, $sqlSearch);
} else { //display all
    $sqlFetchEmployees = "SELECT employees.id, employee_ssn, employees.name, salary, departments.name AS department_name, location
                          FROM employees
                          LEFT JOIN departments ON employees.department_id = departments.id";
    $resultEmployees = mysqli_query($conn, $sqlFetchEmployees);
}




mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Employee Management</title>
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
<table border="1" style="border-collapse: collapse; width: 100%;">
    <tr>
        <th>ID</th>
        <th>Employee SSN</th>
        <th>Name</th>
        <th>Salary</th>
        
    </tr>
    <?php
    // Display all employees or searched employees
    if (isset($resultEmployees) && $resultEmployees->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($resultEmployees)) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["employee_ssn"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["salary"] . "</td>";
            

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No employees found.</td></tr>";
    }
    ?>
</table>

</body>
</html>
