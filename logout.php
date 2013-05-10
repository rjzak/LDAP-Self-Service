<?php
# File: logout.php
# Use: Remove all information from the session, delete the session.
require_once("config.php");
$_SESSION['uid'] = "0";
$_SESSION['userDN'] = "0";
$_SESSION['userPW'] = "0";
session_unset();
session_destroy();
session_write_close();
header("HTTP/1.1 100 Continue");
header("Location: index.php");
?>
