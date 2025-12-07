<?php
session_start();
session_unset();
session_destroy();

// Redirect to login.php with a query parameter
header("Location: login.php?loggedout=1");
exit;
?>