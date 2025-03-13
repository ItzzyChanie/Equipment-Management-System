<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Equipment Management Page - AU Equipment Management System</title>
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel = "stylesheet" href = "https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            background: url('picture/try.jpg') no-repeat center center/cover;
            color: #fff;
            width: 240px;
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: fixed;
            height: 100%;
            overflow: auto;
        }
        .sidebar h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 26px;
            font-weight: bold;
        }
        .sidebar h4 {
            margin-top: -25px;
            margin-bottom: 10px;
        }
        .sidebar a, .sidebar .btn, .sidebar .logout-btn {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px 0;
            text-align: left;
            width: 100%;
            border-radius: 0;
        }
        .sidebar a:hover, .sidebar .btn:hover, .sidebar .logout-btn:hover {
            color:rgb(248, 87, 242);
            background-color: transparent;
        }
        .sidebar a.selected, .sidebar .btn.selected, .sidebar .logout-btn.selected {
            color:rgb(248, 87, 242);
        }
        .sidebar a.selected::before, .sidebar .btn.selected::before, .sidebar .logout-btn.selected::before {
            content: "\2713"; /* Check mark icon */
            margin-right: 10px;
        }
        .sidebar img {
            width: 100px;
            margin-bottom: 20px;
        }
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: #f5f5f5;
            margin-left: 240px;
            overflow: auto;
        }
        .header {
            background-color: rgb(89, 3, 118);
            color: #fff;
            padding: 20px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: fixed;
            width: calc(100% - 240px);
            top: 0;
            z-index: 1000;
        }
        .header h1 {
            margin: 2px;
            font-size: 1.5em;
            font-weight: bold;
        }
        .header h2 {
            margin: 0;
            font-size: 0.8em;
        }
        .header img {
            height: 50px;
            margin-right: 20px;
        }
        .btn, .input-group-append .btn, input[type='submit'] {
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover, .input-group-append .btn:hover, input[type='submit']:hover {
            background-color:rgb(106, 4, 108);
            border-radius: 0;
        }
        .logout-btn {
            padding: 5px 10px;
            color: #fff;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            text-align: center;
        }
        .logout-btn:hover {
            color:rgb(233, 24, 233);
            background-color: transparent;
        }
        .icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
            padding: 6px;
        }
        .icon-btn i {
            font-size: 20px;
            color: rgb(204, 9, 234);
        }
        .icon-btn:hover i {
            color:rgb(154, 6, 105);
        }
        .icon-btn.delete i {
            color: #dc3545;
        }
        .icon-btn.delete:hover i {
            color: #a71d2a;
        }
        .table-container {
            padding: 20px;
            margin-top: 80px;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin: 10px 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        .styled-table th, .styled-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 0.8em;
            text-align: center;
        }
        .styled-table th {
            background-color: rgb(89, 3, 118);
            color: #fff;
        }
        .styled-table tr:nth-child(even) {
            background-color:rgba(232, 157, 221, 0.3);
        }
        .styled-table td.department {
            width: 90px;
        }
        .styled-table td.branch {
            width: 90px;
        }
        .styled-table th.action {
            width: 100px;
        }
        .input-group .form-control {
            height: 30px;
            font-size: 0.9em;
        }
        .input-group-append .btn {
            height: 30px;
            font-size: 0.9em;
            background-color: rgb(204, 9, 234);
        }
        .input-group-append .btn:hover {
            background-color: rgb(159, 9, 161);
        }
    </style>

    <script>
        function confirmDelete(assetNumber) {
            if (confirm("Are you sure you want to delete this equipment?")) {
                window.location.href = 'delete-equipment.php?AssetNumber=' + assetNumber;
            }
        }

        function confirmLogout(event) {
            event.preventDefault();
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = 'logout.php';
            }
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            if (message) {
                alert(message);
            }
        };
    </script>
</head>

<body>
    <div class = "sidebar">
        <img src = 'picture/Arellano_University_Logo.png' alt = 'Logo'>
        <h2>Equipment Management System</h2>
        <h4>______________</h4>
        <a href = "equipment-management.php" class = "<?php echo $current_page == 'equipment-management.php' ? 'selected' : ''; ?>">Equipment Table</a>
        <a href = "add-equipment.php" class = "btn <?php echo $current_page == 'add-equipment.php' ? 'selected' : ''; ?>">Add New Equipment</a>
        <a href = "logout.php" class = "logout-btn <?php echo $current_page == 'logout.php' ? 'selected' : ''; ?>" onclick="confirmLogout(event)">Logout</a>
    </div>

    <div class = "main-content">
        <?php
        if (isset($_SESSION['username'])) {
            echo "<div class='header'>";
            echo "<div>";
            echo "<h1>Welcome, " . $_SESSION['username'] . "</h1>";
            echo "<h2>Account type: " . $_SESSION['usertype'] . "</h2>";
            echo "</div>";
            echo "<form class='form-inline' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST'>";
            echo "<div class='input-group'>";
            echo "<input type='text' name='txtsearch' class='form-control' placeholder='Search Equipment...'>";
            echo "<div class='input-group-append'>";
            echo "<button class='btn' type='submit' name='btnsearch'><i class='fas fa-search'></i></button>";
            echo "</div>";
            echo "</div>";
            echo "</form>";
            echo "</div>";
        } else {
            header("location: login.php");
            exit();
        }
        ?>
        <div class = "table-container">
            <?php
            function buildtable($result) 
            {
                if (mysqli_num_rows($result) > 0) {
                    echo "<table class='styled-table'>";
                    echo "<thead><tr>
                        <th>Asset Number</th>
                        <th>Serial Number</th>
                        <th>Type</th>
                        <th>Manufacturer</th>
                        <th>Year Model</th>
                        <th>Description</th>
                        <th class='department'>Branch</th>
                        <th class='department'>Department</th>
                        <th>Status</th>
                        <th>Created by</th>
                        <th>Date Created</th>
                        <th class='action'>Action</th>
                    </tr></thead><tbody>";

                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['AssetNumber'] . "</td>";
                        echo "<td>" . $row['SerialNumber'] . "</td>";
                        echo "<td>" . $row['Type'] . "</td>";
                        echo "<td>" . $row['Manufacturer'] . "</td>";
                        echo "<td>" . $row['YearModel'] . "</td>";
                        echo "<td style='white-space: pre-wrap; word-wrap: break-word;'>" . $row['Description'] . "</td>";
                        echo "<td class='branch'>" . $row['Branch'] . "</td>";
                        echo "<td class='department'>" . $row['Department'] . "</td>";
                        echo "<td>" . $row['Status'] . "</td>";
                        echo "<td>" . $row['Createdby'] . "</td>";
                        echo "<td>" . $row['DateCreated'] . "</td>";
                        echo "<td>
                                <a class='icon-btn' href='update-equipment.php?AssetNumber=" . $row['AssetNumber'] . "'>
                                    <i class='fas fa-edit'></i>
                                </a>
                                <a class='icon-btn delete' href='javascript:void(0);' onclick='confirmDelete(\"" . $row['AssetNumber'] . "\")'>
                                    <i class='fas fa-trash'></i>
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "No record(s) found.";
                }
            }
            
            require_once "config.php";
            if (isset($_POST['btnsearch'])) {
                $sql = "SELECT * FROM tblequipments WHERE AssetNumber LIKE ? OR SerialNumber LIKE ? OR Type LIKE ? OR Department LIKE ? 
                ORDER BY AssetNumber";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    $searchvalue = '%' . $_POST['txtsearch'] . '%';

                    mysqli_stmt_bind_param($stmt, "ssss", $searchvalue, $searchvalue, 
                    $searchvalue, $searchvalue);

                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        buildtable($result);
                        
                    } else {
                        echo "ERROR on search.";
                    }
                }
            } else {
                $sql = "SELECT * FROM tblequipments ORDER BY AssetNumber";

                if ($stmt = mysqli_prepare($link, $sql)) 
                {
                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        buildtable($result);
                    }
                } else {
                    echo "ERROR on loading data.";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>