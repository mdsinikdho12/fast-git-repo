<?php
session_start();
error_reporting(0);

// Check if user is logged in
// if (!isset($_SESSION['loggedin'])) {
//     header('Location: login.php');
//     exit();
// }

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_registration";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Handle search for Group A
    $search_roll_a = isset($_GET['roll_a']) ? intval($_GET['roll_a']) : '';
    $search_group_a = isset($_GET['group_a']) ? mysqli_real_escape_string($conn, $_GET['group_a']) : 'A';

    // Handle search for Group B
    $search_roll_b = isset($_GET['roll_b']) ? intval($_GET['roll_b']) : '';
    $search_group_b = isset($_GET['group_b']) ? mysqli_real_escape_string($conn, $_GET['group_b']) : 'B';

    // Handle sorting
    $sort = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'id_desc';

    // Fetch data for Group A
    $sql_a = "SELECT * FROM students WHERE group_name = 'A'";
    if (!empty($search_roll_a)) {
        $sql_a .= " AND roll = $search_roll_a";
    }
    if ($sort === 'roll_asc') {
        $sql_a .= " ORDER BY roll ASC";
    } elseif ($sort === 'roll_desc') {
        $sql_a .= " ORDER BY roll DESC";
    } else {
        $sql_a .= " ORDER BY id DESC";
    }
    $result_a = $conn->query($sql_a);

    // Fetch data for Group B
    $sql_b = "SELECT * FROM students WHERE group_name = 'B'";
    if (!empty($search_roll_b)) {
        $sql_b .= " AND roll = $search_roll_b";
    }
    if ($sort === 'roll_asc') {
        $sql_b .= " ORDER BY roll ASC";
    } elseif ($sort === 'roll_desc') {
        $sql_b .= " ORDER BY roll DESC";
    } else {
        $sql_b .= " ORDER BY id DESC";
    }
    $result_b = $conn->query($sql_b);
} catch (Exception $e) {
    echo "<script>alert('" . $e->getMessage() . "'); window.history.back();</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registration Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
            padding: 10px 20px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 55px;
        }
        .navbar-title {
            font-size: 24px;
            font-weight: bold;
        }
        .navbar-menu a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            font-size: 16px;
        }
        .navbar-menu a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 94%;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .group-toggle {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .group-toggle button {
            padding: 10px 20px;
            margin: 0 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .group-toggle button.active {
            background: #0056b3;
        }
        .search-print {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-print input, .search-print select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .search-print button {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-print button:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .delete-btn {
            padding: 5px 10px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background: #c82333;
        }
        .sort-link {
            color: #fff;
            text-decoration: none;
        }
        .sort-link:hover {
            text-decoration: underline;
        }
        .logOut button {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 40px;
            margin-left: 45%;
        }
        .logOut button:hover {
            background: rgb(16, 96, 161);
            color: yellow;
        }
        .logOut button a {
            color: #fff;
            font-size: 14px;
            text-decoration: none;
        }
        .print-title {
            display: none;
        }
        @media print {
            .print-title {
                display: block;
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .navbar, .group-toggle, .search-print, .logOut {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-title">Captain Dashboard</div>
        <div class="navbar-menu">
            <a href="#">Home</a>
            <a href="#">Search Student</a>
            <a href="#">Edit Application</a>
            <a href="#">Bulk Submit Attendance</a>
            <a href="#">Submit Attendance</a>
            <a href="#">Change Password</a>
            <a href="/login/login.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1>Registration Dashboard</h1>

        <!-- Group Toggle Buttons -->
        <div class="group-toggle">
            <button id="groupAButton" onclick="showGroup('A')">Group A</button>
            <button id="groupBButton" onclick="showGroup('B')">Group B</button>
        </div>

        <!-- Group A Section -->
        <div id="groupA" style="display: none;">
            <div class="search-print">
                <input type="number" id="searchRollA" placeholder="Search by Roll" value="<?php echo $search_roll_a; ?>">
                <select id="searchGroupA">
                    <option value="A" selected>A</option>
                </select>
                <button onclick="searchRecords('A')">Search Group A</button>
                <button onclick="printTable('A')">Print Group A</button>
            </div>
            <div class="print-title" id="printTitleA">CST Session 2023-24 - Group A</div>
            <table id="tableA">
                <thead>
                    <tr>
                        <th>Serial</th>
                        <th>Name</th>
                        <th>
                            <a href="dashboard.php?sort=<?php echo ($sort === 'roll_asc') ? 'roll_desc' : 'roll_asc'; ?>&roll=<?php echo $search_roll; ?>&group=<?php echo $search_group; ?>" class="sort-link">
                                Roll <?php echo ($sort === 'roll_asc') ? '▲' : ($sort === 'roll_desc' ? '▼' : ''); ?>
                            </a>
                        </th>
                        <th>Group</th>
                        <th>Mobile</th>
                        <th>Guardian Relation</th>
                        <th>Guardian Name</th>
                        <th>Guardian Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_a->num_rows > 0) {
                        $serial = 1;
                        while ($row = $result_a->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$serial}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['roll']}</td>
                                    <td>{$row['group_name']}</td>
                                    <td>{$row['mobile']}</td>
                                    <td>{$row['guardian_relation']}</td>
                                    <td>{$row['guardian_name']}</td>
                                    <td>{$row['guardian_mobile']}</td>
                                    <td><button class='delete-btn' onclick=\"deleteRecord({$row['id']})\">Delete</button></td>
                                  </tr>";
                            $serial++;
                        }
                    } else {
                        echo "<tr><td colspan='9'>No records found for Group A</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Group B Section -->
        <div id="groupB" style="display: none;">
            <div class="search-print">
                <input type="number" id="searchRollB" placeholder="Search by Roll" value="<?php echo $search_roll_b; ?>">
                <select id="searchGroupB">
                    <option value="B" selected>B</option>
                </select>
                <button onclick="searchRecords('B')">Search Group B</button>
                <button onclick="printTable('B')">Print Group B</button>
            </div>
            <div class="print-title" id="printTitleB">CST Session 2023-24 - Group B</div>
            <table id="tableB">
                <thead>
                    <tr>
                        <th>Serial</th>
                        <th>Name</th>
                        <th>
                            <a href="dashboard.php?sort=<?php echo ($sort === 'roll_asc') ? 'roll_desc' : 'roll_asc'; ?>&roll=<?php echo $search_roll; ?>&group=<?php echo $search_group; ?>" class="sort-link">
                                Roll <?php echo ($sort === 'roll_asc') ? '▲' : ($sort === 'roll_desc' ? '▼' : ''); ?>
                            </a>
                        </th>
                        <th>Group</th>
                        <th>Mobile</th>
                        <th>Guardian Relation</th>
                        <th>Guardian Name</th>
                        <th>Guardian Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_b->num_rows > 0) {
                        $serial = 1;
                        while ($row = $result_b->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$serial}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['roll']}</td>
                                    <td>{$row['group_name']}</td>
                                    <td>{$row['mobile']}</td>
                                    <td>{$row['guardian_relation']}</td>
                                    <td>{$row['guardian_name']}</td>
                                    <td>{$row['guardian_mobile']}</td>
                                    <td><button class='delete-btn' onclick=\"deleteRecord({$row['id']})\">Delete</button></td>
                                  </tr>";
                            $serial++;
                        }
                    } else {
                        echo "<tr><td colspan='9'>No records found for Group B</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="logOut">
            <button><a href='/login/login.php'>Logout</a></button>
        </div>
    </div>

    <script>
        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                window.location.href = `delete.php?id=${id}`;
            }
        }

        function searchRecords(group) {
            const roll = group === 'A' ? document.getElementById('searchRollA').value : document.getElementById('searchRollB').value;
            const groupValue = group === 'A' ? 'A' : 'B';
            window.location.href = `dashboard.php?roll_${group.toLowerCase()}=${roll}&group_${group.toLowerCase()}=${groupValue}`;
        }

        function printTable(group) {
            const printContents = document.getElementById(`table${group}`).outerHTML;
            const printTitle = document.getElementById(`printTitle${group}`).outerHTML;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = printTitle + printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }

        function showGroup(group) {
            if (group === 'A') {
                document.getElementById('groupA').style.display = 'block';
                document.getElementById('groupB').style.display = 'none';
                document.getElementById('groupAButton').classList.add('active');
                document.getElementById('groupBButton').classList.remove('active');
            } else {
                document.getElementById('groupA').style.display = 'none';
                document.getElementById('groupB').style.display = 'block';
                document.getElementById('groupAButton').classList.remove('active');
                document.getElementById('groupBButton').classList.add('active');
            }
        }
    </script>
</body>
</html>