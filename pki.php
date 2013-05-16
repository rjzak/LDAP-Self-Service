<?php
# File: pki.php
# Use: Retrieve the user's ID from the Common Name (CN) attribute of
#	the client-provided SSL certificate.

require_once("config.php");
require_once("functions.php");

if ($config["allowPKIlogin"]) {
        $userID = $_SERVER['SSL_CLIENT_S_DN_CN'];
        $userDN = "uid=".$userID.",".$config["peopleOU"].",".$config["searchBase"];
		$ds = ldap_connect( $config["ldapServer"] ) or die("Could not reach LDAP server: ".ldap_error($ds));
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if ($config["encryption"] == "tls") {
                ldap_start_tls($ds) or die("Failed to Start TLS: ".ldap_error($ds));
        }
        ldap_bind($ds, $config["dirAdmin"], $config["dirAdminPW"]) or die("Cound not bind to LDAP server: ".ldap_error($ds));
        $filter = "(uid=$userID)";
        $fields = array("cn");
        $search = ldap_search($ds, $config["searchBase"], $filter, $fields);
        $info = ldap_get_entries($ds, $search);
        ldap_close($ds);
        if ($info[0]["dn"] == $userDN) {
                $_SESSION['uid'] = $userID;
                $_SESSION['userDN'] = $userDN;
                $_SESSION['userPW'] = "SSL_SIGNIN_NO_PASSWORD_PROVIDED";
                header("Location: edit.php");
        } else {
                header("Location: index.php");
        }
} else {
	header("Location: index.php");
}

?>
