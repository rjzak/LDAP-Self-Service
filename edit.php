<?php

# File: edit.php
# Use: The main user interface.  Check some parameters, show the form.

require_once("config.php");

if ($_SESSION['uid'] == "") {
	header("Location: index.php");
}

$userID = $_SESSION['uid'];
$userDN = $_SESSION['userDN'];
$userPW = $_SESSION['userPW'];

$ds = ldap_connect( $config["ldapServer"] ) or die("Could not reach LDAP server.");
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
if ($config["encryption"] == "tls") {
	ldap_start_tls($ds) or die("Failed to Start TLS: ".ldap_error($ds));
}

$bind = ldap_bind($ds, $config["dirAdmin"], $config["dirAdminPW"]) or die("Cound not bind to LDAP server.");

$filter = "(uid=$userID)";
$fields = array("cn", "givenName", "loginShell", "telephonenumber", "mobile", "mail", "title", "o");
$search = ldap_search($ds, $config["searchBase"], $filter, $fields);
$info = ldap_get_entries($ds, $search);
$groups = array();
if ($config["showGroupMembership"]) {
	$filter = "(&(objectClass=posixGroup)(memberUid=$userID))";
	$fields = array("cn", "description");
	$gSearch = ldap_search($ds, $config["searchBase"], $filter, $fields);
	$groups = ldap_get_entries($ds, $gSearch);
}
?>
<!doctype html>
<html lang="en">
<head>
	<title>LDAP User Management</title>
	<script language="javascript" type="text/javascript">
		function checkPassword() {
    			if (document.getElementById("newpass").value != document.getElementById("newpassconf").value) {
        			alert ('The passwords do not match!');
        			document.getElementById("newpassconf").setCustomValidity("The passwords don't match");
				return false;
    			} else {
				document.getElementById("newpassconf").setCustomValidity("");
			}
		}
	</script>
	<style type="text/css">
		:invalid {
			border: 2px solid #ff0000;
		}
	</style>
</head>
<body>
<center>
	<form method="post" action="update.php">
		<table>
			<caption>Details for <?= $userID ?></caption>
			<tbody>
				<tr>
					<td>Full Name</td>
					<td><?=$info[0]["cn"][0]?></td>
				</tr>
				<tr>
					<?php	if ($config["userEditableEmail"]) { ?>
						<td><label for="email">E-Mail</label></td>
						<td>
							<input type="email" name="email" id="email" value="<?=$info[0]["mail"][0]?>" title="Your email address" />
						</td>
					<?php } else { ?>
						<td>E-Mail</td>
						<td><?=$info[0]["mail"][0]?></td>
					<?php } ?>
				</tr>
				<tr>
					<?php if ($config['userEditableShell']) { ?>
						<td><label for="shell">Shell</label></td>
						<td>
							<select name="shell" id="shell">
								<?php foreach ($config["sysShells"] as &$shell) {
									if ($shell == $info[0]["loginshell"][0]) {
										echo "<option value=\"$shell\" selected>$shell</option>\n";
									} else {
										echo "<option value=\"$shell\">$shell</option>\n";
									}
								} ?>
							</select>
						</td>
					<?php } else { ?>
						<td>Shell</td>
                        <td><?=$info[0]["loginshell"][0]?></td>
					<?php } ?>
				</tr>
				<?php if ($config["userEditablePhones"]) { ?>
					<tr>
						<td><label for="phone">Telephone</label></td>
						<td><input type="tel" name="phone" id="phone" value="<?=$info[0]["telephonenumber"][0]?>" /></td>
					</tr>
					<tr>
						<td><label for="mobile">Mobile Phone</label></td>
						<td><input type="tel" name="mobile" id="mobile" value="<?=$info[0]["mobile"][0]?>" /></td>
					</tr>
				<?php } else { ?>
				 	<tr>
                    	<td>Telephone</td>
                        <td><?=$info[0]["telephonenumber"][0]?></td>
                    </tr>
                    <tr>
                        <td>Mobile Phone</td>
                        <td><?=$info[0]["mobile"][0]?></td>
                    </tr>
				<?php } ?>
				<tr>
					<td>Title</td>
					<td><?=$info[0]["title"][0]?></td>
				</tr>
				<tr>
					<td>Org</td>
					<td><?=$info[0]["o"][0]?></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td colspan="2" align="center">Change Password</td>
				</tr>
				<?php if ($userPW != "SSL_SIGNIN_NO_PASSWORD_PROVIDED") { ?>
					<tr>
						<td><label for="oldpass">Current Password</label></td>
						<td><input type="password" name="oldpass" id="oldpass" length="10" pattern=".{5,}" title="Passwords are at least 5 characters" autocomplete="off" /></td>
					</tr>
				<?php } ?>
				<tr>
                        <td><label for="newpass">New Password</label></td>
                        <td><input type="password" name="newpass" id="newpass" length="10" pattern=".{5,}" title="Passwords are at least 5 characters" autocomplete="off" /></td>
                </tr>
				<tr>
                        <td><label for="newpassconf">Confirm New Password</label></td>
                        <td><input type="password" name="newpassconf" id="newpassconf" length="10" pattern=".{5,}" title="Passwords are at least 5 characters" autocomplete="off" /></td>
                </tr>
				<tr>
					<td>&nbsp;</td>
					<td align="right">
						<input type="submit" value="Save " onClick="return checkPassword();" /> <br />
						<input type="button" value="Logout" onClick="javascript:location.href='logout.php';" />
					</td>
				</tr>
				<?php if ( strlen( $_SESSION['statMsg'] ) ) { ?>
					<tr>
						<td colspan="2">
							<strong><?php echo $_SESSION['statMsg']; ?></strong>
						</td>
					</tr>
				<?php
					$_SESSION['statMsg'] = ""; }
					if ($config["showGroupMembership"]) { ?>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td>Group Memberships</td>
							<td></td>
						</tr>
						<?php 
							foreach ($groups as &$g) {
								if (strlen($g['cn'][0])) {
									echo "<tr><td>&nbsp;</td><td>".$g['cn'][0]."</td></tr>";
								}
							}
						?>
				<?php } ?>
			</tbody>
		</table>
	</form>
</center>

</body>
</html>
<?php
ldap_close($ds);
?>
