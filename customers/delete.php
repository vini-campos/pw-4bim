<?php
include "function.php";

try {
    if (isset($_GET['id'])) {
        delete($_GET['id']);
    } else {
        header("location: index.php");
        exit;
    }
} catch (Exception $e) {
    $_SESSION["message"] = "Não foi possível realizar a operação.";
    $_SESSION["type"] = "danger";
    header("location: index.php");
    exit;
}
?>