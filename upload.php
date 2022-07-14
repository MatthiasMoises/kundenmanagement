<?php

require('required/config.php');

$target_path = TARGET_PATH."/";

$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "Die Datei ".  basename( $_FILES['uploadedfile']['name']). 
    " wurde erfolgreich hochgeladen.";
} else{
    echo "Beim Versuch die Datei hochzuladen ist ein Fehler aufgetreten, bitten versuchen Sie es erneut!";
}

?>