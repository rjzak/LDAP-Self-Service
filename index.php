<?php
require_once("config.php");
# File: index.php
# Use: Login form.
?>
<!doctype html>
<html lang="en">
<head>
	<title>LDAP User Management</title>
	<style type="text/css">
		:invalid {
			border: 2px solid #ff0000;
		}
	</style>
</head>
<body>
<center>
	<form method="post" action="/login.php">
		<table>
			<caption>Login to <?= $config["orgNameShort"]?>/<?= $config["divNameShort"]?> </caption>
			<tbody>
				<tr>
					<td><label for="uid">User ID</label></td>
					<td><input type="text" name="uid" id="uid" length="10" maxlength="20" pattern=".{3,}" title="Usernames are at least 3 characters" required /></td>
				</tr>
				<tr>
					<td><label for="pass">Password</label></td>
					<td><input type="password" name="pass" id="pass" length="10" pattern=".{5,}" title="Passwords are at least 5 characters" required /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" value="Login" /> 
						<?php if ($config["allowPKIlogin"] ) { ?>
							&nbsp; &nbsp;
							<input type="button" value="PKI Login" onClick="location.href='pki.php';" />
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</center>
</body>
</html>
