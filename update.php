<?php
# File: update.php
# Use: Update the user's content, if provided and if modification is allowed.
#	Pure controller.

require_once("config.php");
require_once("functions.php");

$_SESSION['timeout'] = time(); # Update the timeout, we want to timeout based on usage, not when logged in.
$_SESSION['statMsg'] = "";

$conn = ldap_connect( $config["ldapServer"] ) or die("Unable to connect to LDAP");
if ($config["encryption"] == "tls") {
	ldap_start_tls($conn) or die("Unable to StartTLS");
}
ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
#$bind = ldap_bind($conn, $_SESSION['userDN'], $_POST['oldpass']); # This didn't allow updating anything other than password.
$bind = ldap_bind($conn, $config['dirAdmin'], $config['dirAdminPW']);
$entry = array();

if ($config["userEditablePhones"] && isset( $_POST['phone'] ) && strlen( $_POST['phone'] ) > 0) {
	$entry["telephonenumber"] = $_POST['phone'];
	$success = ldap_modify($conn, $_SESSION['userDN'], $entry);
	$entry = array();
}

if ($config["userEditablePhones"] && isset( $_POST['mobile'] ) && strlen( $_POST['mobile'] ) > 0) {
        $entry["mobile"] = $_POST['mobile'];
        $success = ldap_modify($conn, $_SESSION['userDN'], $entry);
        $entry = array();
}

if ($config["userEditableShell"] && isset( $_POST['shell'] ) && strlen( $_POST['shell'] ) > 0) {
        $entry["loginshell"] = $_POST['shell'];
        $success = ldap_modify($conn, $_SESSION['userDN'], $entry);
        $entry = array();
}

if ($config['userEditableEmail'] && isset( $_POST['email'] ) && strlen( $_POST['email'] ) > 0) {
        $entry["mail"] = $_POST['email'];
        $success = ldap_modify($conn, $_SESSION['userDN'], $entry);
        $entry = array();
}


if (strlen( $_POST['oldpass'] ) > 0 ) {
	if ( $_POST['oldpass'] != $_SESSION['userPW'] ) {
		$_SESSION['statMsg'] = "Old password incorrect!";
	} else {
		if ( $_POST['newpass'] != $_POST['newpassconf'] ) {
			$_SESSION['statMsg'] = "New passwords do not match!";
		} else {
			if ( strlen($_POST['newpass']) < 4 ) {
				$_SESSION['statMsg'] = "New password was too short.";
			} else {
				$encodedPass = make_ssha_password( $_POST['newpass'] );
				$entry["userPassword"] = "$encodedPass";
				$success = ldap_modify($conn, $_SESSION['userDN'], $entry);
				if ($success) {
					$_SESSION['statMsg'] = "Password change successful!";
					$_SESSION['userPW'] = $_POST['newpass'];
				} else {
					$error = ldap_error($conn);
					$_SESSION['statMsg'] = "Password change failed: ".$error;
				}
			}
		}
	}
}
ldap_close($conn);

header("HTTP/1.1 100 Continue");
header("Location: edit.php");

?>
