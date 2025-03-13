<?php
require_once "config.php";
include ("session-checker.php");

$current_page = basename($_SERVER['PHP_SELF']);

if (isset($_GET['AssetNumber'])) {
    $assetNumber = $_GET['AssetNumber'];

    if (isset($_POST['btnupdate'])) {
        // Validate Year Model
        if (!preg_match('/^\d{4}$/', $_POST['txtYearModel'])) {
            echo "<script>alert('Year Model should be a 4-digit number.');</script>";
        } 
        else {
            $sql = "UPDATE tblequipments SET SerialNumber = ?, Type = ?, Manufacturer = ?, YearModel = ?, Description = ?, 
            Branch = ?, Department = ?, Status = ? WHERE AssetNumber = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssssssss", $_POST['txtSerialNumber'], 
                $_POST['cmbtype'], $_POST['txtManufacturer'], $_POST['txtYearModel'], $_POST['txtDescription'], 
                $_POST['cmbbranch'], $_POST['cmbDepartment'], $_POST['cmbStatus'], $assetNumber);

                if (mysqli_stmt_execute($stmt)) {
                    // Log the update action
                    $log_sql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
                
                    if ($log_stmt = mysqli_prepare($link, $log_sql)) {
                        $datelog = date("Y-m-d");
                        $timelog = date("H:i:s");
                        $action = "UPDATE";
                        $module = "Equipment";
                        $performedto = $assetNumber;
                        $performedby = $_SESSION['username'];
                        mysqli_stmt_bind_param($log_stmt, "ssssss", $datelog, $timelog, $action, $module, $performedto, $performedby);
                        mysqli_stmt_execute($log_stmt);
                    }
                    // Redirect to equipment-management.php with a success message
                    echo "<script>
                            window.location.href = 'equipment-management.php?message=Equipment Successfully Updated!';
                          </script>";
                    exit();
                } else {
                    echo "<script>alert('ERROR on updating equipment.');</script>";
                }
            }
        }
    } else {
        $sql = "SELECT * FROM tblequipments WHERE AssetNumber = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $assetNumber);

            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result);
                    $serialNumber = $row['SerialNumber'];
                    $type = $row['Type'];
                    $manufacturer = $row['Manufacturer'];
                    $yearModel = $row['YearModel'];
                    $description = ""; // Leave description blank
                    $branch = $row['Branch'];
                    $department = $row['Department'];
                    $status = $row['Status'];
                } 
                else {
                    echo "No record found.";
                }
            }
        }
    }
} else {
    header("location: equipment-management.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Equipment Page - AU Equipment Management System</title>
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel = "stylesheet" href = "https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
        }
        .header {
            background-color: rgb(89, 3, 118);
            color: #fff;
            padding: 20px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            position: fixed;
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
        .form-container {
            padding: 20px;
            margin-top: 95px;
            margin-left: 20px;
            margin-right: 20px;
            background-color:rgba(178, 55, 185, 0.35);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: calc(100% - 40px);
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
        .status-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .footer {
            background-color: rgba(30, 2, 40, 0.46);
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>

<body>
    <div class = "header">
        <div>
            <h1>Update Equipment</h1>
        </div>
    </div>

    <div class = "form-container">
        <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?AssetNumber=" . $assetNumber; ?>" method = "POST">
            <div class = "form-row">
                <div class = "form-left">
                    <div class = "form-group">
                        <label for = "txtAssetNumber">Asset Number:</label>
                        <input type = "text" value = "<?php echo $assetNumber; ?>" disabled style = "width: 100%;" />
                    </div>

                    <div class = "form-group">
                        <label for = "txtSerialNumber">Serial Number:</label>
                        <input type = "text" name = "txtSerialNumber" value ="<?php echo $serialNumber; ?>" required style = "width: 100%;" />
                    </div>

                    <div class = "form-group">
                        <label for = "txtManufacturer">Manufacturer:</label>
                        <input type= "text" name = "txtManufacturer" value = "<?php echo $manufacturer; ?>" required style = "width: 100%;" />
                    </div>

                    <div class = "form-group">
                        <label for = "txtYearModel">Year Model:</label>
                        <input type = "text" name = "txtYearModel" value = "<?php echo $yearModel; ?>" required pattern = "\d{4}"
                        title = "Year Model should be a 4-digit number" style = "width: 100%;" />
                    </div>

                    <div class = "form-group">
                        <label>Status:</label>

                        <div class = "status-group">
                            <input type = "radio" name = "cmbStatus" value = "WORKING" <?php if ($status == "WORKING") echo "checked"; ?>> Working
                            <input type = "radio" name = "cmbStatus" value = "ON-REPAIR" <?php if ($status == "ON-REPAIR") echo "checked"; ?>> On-repair
                            <input type = "radio" name = "cmbStatus" value = "RETIRED" <?php if ($status == "RETIRED") echo "checked"; ?>> Retired
                        </div>
                    </div>
                </div>

                <div class = "form-right">
                    <div class = "form-group">
                        <label for = "cmbtype">Equipment Type:</label>

                        <select name = "cmbtype" id = "cmbtype" required style = "width: 100%;">
                            <option value = "MONITOR" <?php if ($type == "MONITOR") echo "selected"; ?>>MONITOR</option>
                            <option value = "CPU" <?php if ($type == "CPU") echo "selected"; ?>>CPU</option>
                            <option value = "KEYBOARD" <?php if ($type == "KEYBOARD") echo "selected"; ?>>KEYBOARD</option>
                            <option value = "MOUSE" <?php if ($type == "MOUSE") echo "selected"; ?>>MOUSE</option>
                            <option value = "AVR" <?php if ($type == "AVR") echo "selected"; ?>>AVR</option>
                            <option value = "MAC" <?php if ($type == "MAC") echo "selected"; ?>>MAC</option>
                            <option value = "PRINTER" <?php if ($type == "PRINTER") echo "selected"; ?>>PRINTER</option>
                            <option value = "PROJECTOR" <?php if ($type == "PROJECTOR") echo "selected"; ?>>PROJECTOR</option>
                        </select>
                    </div>

                    <div class = "form-group">
                        <label for = "cmbbranch">Branches:</label>

                        <select name = "cmbbranch" id = "cmbbranch" required style = "width: 100%;">
                            <option value = "JUAN SUMULONG CAMPUS" <?php if ($branch == "JUAN SUMULONG CAMPUS") echo "selected"; ?>>JUAN SUMULONG CAMPUS</option>
                            <option value = "JOSE RIZAL CAMPUS" <?php if ($branch == "JOSE RIZAL CAMPUS") echo "selected"; ?>>JOSE RIZAL CAMPUS</option>
                            <option value = "ELISA ESGUERRA CAMPUS" <?php if ($branch == "ELISA ESGUERRA CAMPUS") echo "selected"; ?>>ELISA ESGUERRA CAMPUS</option>
                            <option value = "ANDRES BONIFACIO CAMPUS" <?php if ($branch == "ANDRES BONIFACIO CAMPUS") echo "selected"; ?>>ANDRES BONIFACIO CAMPUS</option>
                            <option value = "PLARIDEL CAMPUS" <?php if ($branch == "PLARIDEL CAMPUS") echo "selected"; ?>>PLARIDEL CAMPUS</option>
                            <option value = "APOLINARIO MABINI CAMPUS" <?php if ($branch == "APOLINARIO MABINI CAMPUS") echo "selected"; ?>>APOLINARIO MABINI CAMPUS</option>
                            <option value = "JOSE ABAD SANTOS CAMPUS" <?php if ($branch == "JOSE ABAD SANTOS CAMPUS") echo "selected"; ?>>JOSE ABAD SANTOS CAMPUS</option>
                        </select>
                    </div>

                    <div class = "form-group">
                        <label for = "cmbDepartment">Departments:</label>
                        <select name = "cmbDepartment" id = "cmbDepartment" required style = "width: 100%;">
                            <option value = "COLLEGE OF ARTS AND SCIENCES" <?php if ($department == "COLLEGE OF ARTS AND SCIENCES") echo "selected"; ?>>COLLEGE OF ARTS AND SCIENCES</option>
                            <option value = "COLLEGE OF CRIMINAL JUSTICE" <?php if ($department == "COLLEGE OF CRIMINAL JUSTICE") echo "selected"; ?>>COLLEGE OF CRIMINAL JUSTICE</option>
                            <option value = "COLLEGE OF ACCOUNTANCY" <?php if ($department == "COLLEGE OF ACCOUNTANCY") echo "selected"; ?>>COLLEGE OF ACCOUNTANCY</option>
                            <option value = "SCHOOL OF COMPUTER STUDIES" <?php if ($department == "SCHOOL OF COMPUTER STUDIES") echo "selected"; ?>>SCHOOL OF COMPUTER STUDIES</option>
                            <option value = "SCHOOL OF BUSINESS ADMINISTRATION" <?php if ($department == "SCHOOL OF BUSINESS ADMINISTRATION") echo "selected"; ?>>SCHOOL OF BUSINESS ADMINISTRATION</option>
                            <option value = "SCHOOL OF EDUCATION" <?php if ($department == "SCHOOL OF EDUCATION") echo "selected"; ?>>SCHOOL OF EDUCATION</option>
                            <option value = "SCHOOL OF LIBRARY SCIENCE" <?php if ($department == "SCHOOL OF LIBRARY SCIENCE") echo "selected"; ?>>SCHOOL OF LIBRARY SCIENCE</option>
                            <option value = "SCHOOL OF HOSPITALITY AND TOURISM MANAGEMENT" <?php if ($department == "SCHOOL OF HOSPITALITY AND TOURISM MANAGEMENT") echo "selected"; ?>>SCHOOL OF HOSPITALITY AND TOURISM MANAGEMENT</option>
                            <option value = "SCHOOL OF NURSING" <?php if ($department == "SCHOOL OF NURSING") echo "selected"; ?>>SCHOOL OF NURSING</option>
                            <option value = "SCHOOL OF MIDWIFERY" <?php if ($department == "SCHOOL OF MIDWIFERY") echo "selected"; ?>>SCHOOL OF MIDWIFERY</option>
                            <option value = "SCHOOL OF PSYCHOLOGY" <?php if ($department == "SCHOOL OF PSYCHOLOGY") echo "selected"; ?>>SCHOOL OF PSYCHOLOGY</option>
                            <option value = "COLLEGE OF RADIOLOGIC TECHNOLOGY" <?php if ($department == "COLLEGE OF RADIOLOGIC TECHNOLOGY") echo "selected"; ?>>COLLEGE OF RADIOLOGIC TECHNOLOGY</option>
                            <option value = "COLLEGE OF PHARMACY" <?php if ($department == "COLLEGE OF PHARMACY") echo "selected"; ?>>COLLEGE OF PHARMACY</option>
                            <option value = "COLLEGE OF MEDICAL TECHNOLOGY" <?php if ($department == "COLLEGE OF MEDICAL TECHNOLOGY") echo "selected"; ?>>COLLEGE OF MEDICAL TECHNOLOGY</option>
                            <option value = "COLLEGE OF PHYSICAL THERAPY" <?php if ($department == "COLLEGE OF PHYSICAL THERAPY") echo "selected"; ?>>COLLEGE OF PHYSICAL THERAPY</option>
                        </select>
                    </div>

                    <div class = "form-group">
                        <label for = "txtDescription">Description:</label>
                        <textarea name = "txtDescription" style = "width: 100%; height: 100px; white-space: pre-wrap;"></textarea>
                    </div>
                </div>
            </div>

            <div class = "form-group buttons">
                <input type = "submit" name = "btnupdate" value = "Save" class = "btn btn-primary">
                <a href = "equipment-management.php" class = "btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class = "footer">
        <p>&copy; 2025 AU Equipment Management System. All rights reserved.</p>
    </div>
</body>
</html>
