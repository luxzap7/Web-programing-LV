<?php
// logout.php - Odjava korisnika
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit;
