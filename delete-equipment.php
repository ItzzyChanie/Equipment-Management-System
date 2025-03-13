<?php
require_once "config.php";
include ("session-checker.php");

if (isset($_GET['AssetNumber'])) {
    $assetNumber = $_GET['AssetNumber'];

    $sql = "DELETE FROM tblequipments WHERE AssetNumber = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $assetNumber);
        if (mysqli_stmt_execute($stmt)) {
            header("location: equipment-management.php");
            exit();
        } else {
            echo "ERROR on deleting equipment.";
        }
    }
} else {
    header("location: equipment-management.php");
    exit();
}
?>
