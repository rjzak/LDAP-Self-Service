<?php

# File: config.php
# Use: Hold configuration variables for the application,
#	and to check the session variable.  It will trigger a
#	timeout if needed.

if(!isset($_SESSION)) {
	session_start();
	$_SESSION['timeout'] = time();
}

if ($_SESSION['timeout'] + 10 * 60 < time()) { # time out after 10 minutes of inactivity
	$_SESSION['uid'] = "0";
	$_SESSION['userDN'] = "0";
	$_SESSION['userPW'] = "0";
	session_unset();
	session_destroy();
	session_write_close();
	header("HTTP/1.1 100 Continue");
	header("Location: index.php");	
}

#
# LDAP server and environment configuration
#

$config["ldapServer"] = "127.0.0.1";
$config["encryption"] = ""; 
$config["searchBase"] = "dc=nodomain";
$config["peopleOU"]   = "ou=people";
$config["groupsOU"]   = "ou=groups";
$config["dirAdmin"]   = "cn=admin,".$config["searchBase"];
$config["dirAdminPW"] = "password";

#
# Site configuration
#

$config["adminName"]  = "Your Name";
$config["adminEmail"] = "you@example.com";
$config["adminPhone"] = "+1 555 555 1212";
$config["orgName"]    = "Your Company, Inc.";
$config["orgNameShort"] = "YCi";
$config["divName"]    = "Your Super Department";
$config["divNameShort"] = "YSD";
$config["orgWebsite"] = "http://www.example.com";

#
# Systems configuration
#

$config["sysShells"] = array("/bin/bash", "/bin/sh");

#
# Site options
#

$config["showGroupMembership"] = true;
$config["userEditableEmail"] = true;
$config["userEditableShell"] = true;
$config["userEditablePhones"] = true;

?>
