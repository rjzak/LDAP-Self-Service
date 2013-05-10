<?php

# File: login.php
# Use: Performs the login, this is a controller.

require_once("config.php");

$ds = ldap_connect( $config["ldapServer"] ) or die("Could not reach LDAP server.");
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3); 

$userDN = "uid=".$_POST["uid"].",".$config["peopleOU"].",".$config["searchBase"];

$bind = ldap_bind($ds, $userDN, $_POST["pass"]);

$loggedIn = false;
if($bind) {
	$loggedIn = true;
	$_SESSION['uid'] = $_POST['uid'];
	$_SESSION['userDN'] = $userDN;
	$_SESSION['userPW'] = $_POST["pass"];
	session_write_close();
}

ldap_close($ds);

if ($loggedIn) {
	header("HTTP/1.1 100 Continue");
	header("Location: edit.php");
} else {
	header("HTTP/1.1 100 Continue");
	header("Location: index.php");
}

?>
