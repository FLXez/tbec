<?php
session_start();
if (isset($_SESSION['userId'])) {
    $name = $_POST['nameEtab'];
    $strasse = $_POST['strasseEtab'];
    $stadt = $_POST['plzStadtEtab'];


    $file_name = $_FILES['file']['name'];
    $file_type = $_FILES['file']['type'];
    $file_size = $_FILES['file']['size'];
    $file_tem_loc = $_FILES['file']['tmp_name'];

    if ($file_name) {
        $handle = fopen($file_tem_loc, 'r');
        $image = fread($handle, $file_size);
    } else {
        $image = "";
    }

    include('db/check_etab.php');
    if (!$etab_vorhanden) {
        include('db/insert_etab.php');
        $etabName = $name;
        include('db/select_etab_id.php');
        if ($result) {
            $etabId = $select_etab_id['id'];
        }
    }
}
if (isset($etabId)) {
    header("Location: ../site/etablissement_details.php?etab_id=" . $etabId);
} else {
    header("Location: " . $_SESSION['source']);
}