<?php
session_start();

if(session_destroy())
{
    header("Location: ../admin/view/login.php");
}


?>