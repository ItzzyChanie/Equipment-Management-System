<?php
require_once "config.php";
include ("session-checker.php");

$error_message = "";
$current_page = basename($_SERVER['PHP_SELF']);

if (isset($_POST['btnsave']))
{
    // Validate Year Model
    if (!preg_match('/^\d{4}$/', $_POST['txtYearModel'])) {
        $error_message = "Year Model should be a 4-digit number.";
    } 
    else {
        //check if the asset number is existing on the table
        $sql = "SELECT * FROM tblequipments WHERE AssetNumber = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_POST['txtAssetNumber']);

            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 0){
                    //add accounts
                    $sql = "INSERT INTO tblequipments (AssetNumber, SerialNumber, Type, Manufacturer, YearModel, Description, Branch,
                    Department, Status, Createdby, DateCreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    if ($stmt = mysqli_prepare($link, $sql))
                    {
                        $status = 'WORKING';
                        $date = date("Y-m-d"); // Use correct date format for SQL
                        mysqli_stmt_bind_param($stmt, "sssssssssss", $_POST['txtAssetNumber'], $_POST['txtSerialNumber'], 
                        $_POST['cmbtype'], $_POST['txtManufacturer'], $_POST['txtYearModel'], $_POST['txtDescription'],  
                        $_POST['cmbbranch'], $_POST['cmbDepartment'], $status, $_SESSION['username'], $date);

                        if (mysqli_stmt_execute($stmt)) {
                            // Log the add action
                            $log_sql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
                            
                            if ($log_stmt = mysqli_prepare($link, $log_sql)) {
                                $datelog = date("Y-m-d");
                                $timelog = date("H:i:s");
                                $action = "ADD";
                                $module = "Equipment";
                                $performedto = $_POST['txtAssetNumber'];
                                $performedby = $_SESSION['username'];
                                mysqli_stmt_bind_param($log_stmt, "ssssss", $datelog, $timelog, $action, $module, $performedto, $performedby);
                                mysqli_stmt_execute($log_stmt);
                            }
                            // Redirect to equipment-management.php with a success message
                            echo "<script>
                                    window.location.href = 'equipment-management.php?message=New Equipment Added!';
                                  </script>";
                            exit();
                        } 
                        else {
                            $error_message = "ERROR on adding new account.";
                        }
                    }
                } else {
                    $error_message = "Asset Number is already in use.";
                }
            }
        } else {
            $error_message = "ERROR on validating if asset number exists.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Equipment Page - AU Equipment Management System</title>
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
            color: #0056b3;
            background-color: transparent;
        }
        .form-container {
            padding: 37px;
            margin-top: 100px;
            margin-left: 20px;
            margin-right: 20px;
            background-color:rgba(178, 55, 185, 0.35);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .form-left, .form-right {
            flex: 1;
        }
        .form-left {
            margin-right: 20px;
        }
        .form-right {
            margin-left: 20px;
        }
        .form-group.buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .form-group.buttons .btn-primary {
            background-color: rgb(112, 37, 137);
            border: none;
        }
        .form-group.buttons .btn-primary:hover {
            background-color: rgb(106, 4, 108);
        }
        .form-group.buttons .btn-secondary:hover {
            background-color: #555;
        }
        .footer {
            background-color: rgba(30, 2, 40, 0.46);
            color: #fff;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            width: calc(100% - 240px);
            bottom: 0;
        }
    </style>
</head>

<body>
<script>
    window.onload = function() {
        var errorMessage = "<?php echo $error_message; ?>";
        if (errorMessage !== "") {
            alert(errorMessage);
        }
    };
</script>

    <div class = "sidebar">
        <img src = 'picture/Arellano_University_Logo.png' alt = 'Logo'>
        <h2>Equipment Management System</h2>
        <h4>______________</h4>
        <a href = "equipment-management.php" class = "<?php echo $current_page == 'equipment-management.php' ? 'selected' : ''; ?>">Equipment Table</a>
        <a href = "add-equipment.php" class = "btn <?php echo $current_page == 'add-equipment.php' ? 'selected' : ''; ?>">Add New Equipment</a>
        <a href = "logout.php" class = "logout-btn <?php echo $current_page == 'logout.php' ? 'selected' : ''; ?>">Logout</a>
    </div>

    <div class = "main-content">
        <div class = "header">
            <div>
                <h1>Add New Equipment</h1>
            </div>
        </div>

        <div class = "form-container">
            <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">

                <div class = "form-row">
                    <div class = "form-left">
                        <div class = "form-group">
                            <label for = "txtAssetNumber">Asset Number:</label>
                            <input type = "text" name = "txtAssetNumber" id = "txtAssetNumber" required>
                        </div>

                        <div class = "form-group">
                            <label for = "txtSerialNumber">Serial Number:</label>
                            <input type = "text" name = "txtSerialNumber" id = "txtSerialNumber" required>
                        </div>

                        <div class = "form-group">
                            <label for = "txtManufacturer">Manufacturer:</label>
                            <input type = "text" name = "txtManufacturer" id = "txtManufacturer" required>
                        </div>

                        <div class = "form-group">
                            <label for = "txtYearModel">Year Model:</label>
                            <input type = "text" name = "txtYearModel" id = "txtYearModel" required pattern="\d{4}"
                            title="Year Model should be a 4-digit number">
                        </div>
                    </div>

                    <div class = "form-right">
                        <div class = "form-group">
                            <label for = "cmbtype">Equipment Type:</label>
                            <select name = "cmbtype" id = "cmbtype" required>
                                <option value = "">--Select Equipment Type--</option>
                                <option value = "MONITOR">MONITOR</option>
                                <option value = "CPU">CPU</option>
                                <option value = "KEYBOARD">KEYBOARD</option>
                                <option value = "MOUSE">MOUSE</option>
                                <option value = "AVR">AVR</option>
                                <option value = "MAC">MAC</option>
                                <option value = "PRINTER">PRINTER</option>
                                <option value = "PROJECTOR">PROJECTOR</option>
                            </select>
                        </div>

                        <div class = "form-group">
                            <label for = "cmbbranch">Branches:</label>
                            <select name = "cmbbranch" id="cmbbranch" required>
                                <option value = "">--Select AU Branch--</option>
                                <option value = "JUAN SUMULONG CAMPUS">JUAN SUMULONG CAMPUS</option>
                                <option value = "JOSE RIZAL CAMPUS">JOSE RIZAL CAMPUS</option>
                                <option value = "ELISA ESGUERRA CAMPUS">ELISA ESGUERRA CAMPUS</option>
                                <option value = "ANDRES BONIFACIO CAMPUS">ANDRES BONIFACIO CAMPUS</option>
                                <option value = "PLARIDEL CAMPUS">PLARIDEL CAMPUS</option>
                                <option value = "APOLINARIO MABINI CAMPUS">APOLINARIO MABINI CAMPUS</option>
                                <option value = "JOSE ABAD SANTOS CAMPUS">JOSE ABAD SANTOS CAMPUS</option>
                            </select>
                        </div>

                        <div class = "form-group">
                            <label for = "cmbDepartment">Departments:</label>
                            <select name = "cmbDepartment" id="cmbDepartment" required>
                                <option value = "">--Select AU College Department--</option>
                                <option value = "COLLEGE OF ARTS AND SCIENCES">COLLEGE OF ARTS AND SCIENCES</option>
                                <option value = "COLLEGE OF CRIMINAL JUSTICE">COLLEGE OF CRIMINAL JUSTICE</option>
                                <option value = "COLLEGE OF ACCOUNTANCY">COLLEGE OF ACCOUNTANCY</option>
                                <option value = "SCHOOL OF COMPUTER STUDIES">SCHOOL OF COMPUTER STUDIES</option>
                                <option value = "SCHOOL OF BUSINESS ADMINISTRATION">SCHOOL OF BUSINESS ADMINISTRATION</option>
                                <option value = "SCHOOL OF EDUCATION">SCHOOL OF EDUCATION</option>
                                <option value = "SCHOOL OF LIBRARY SCIENCE">SCHOOL OF LIBRARY SCIENCE</option>
                                <option value = "SCHOOL OF HOSPITALITY AND TOURISM MANAGEMENT">SCHOOL OF HOSPITALITY AND TOURISM MANAGEMENT</option>
                                <option value = "SCHOOL OF NURSING">SCHOOL OF NURSING</option>
                                <option value = "SCHOOL OF MIDWIFERY">SCHOOL OF MIDWIFERY</option>
                                <option value = "SCHOOL OF PSYCHOLOGY">SCHOOL OF PSYCHOLOGY</option>
                                <option value = "COLLEGE OF RADIOLOGIC TECHNOLOGY">COLLEGE OF RADIOLOGIC TECHNOLOGY</option>
                                <option value = "COLLEGE OF PHARMACY">COLLEGE OF PHARMACY</option>
                                <option value = "COLLEGE OF MEDICAL TECHNOLOGY">COLLEGE OF MEDICAL TECHNOLOGY</option>
                                <option value = "COLLEGE OF PHYSICAL THERAPY">COLLEGE OF PHYSICAL THERAPY</option>
                            </select>
                        </div>

                        <div class = "form-group">
                            <label for = "txtDescription">Description:</label>
                            <textarea name = "txtDescription" id = "txtDescription" required></textarea>
                        </div>
                    </div>
                </div>

                <div class = "form-group buttons">
                    <input type = "submit" name = "btnsave" value = "Save" class = "btn btn-primary">
                    <a href = "equipment-management.php" class = "btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class = "footer">
            <p>&copy; 2025 AU Equipment Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>