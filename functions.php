<?php

# File: functions.php
# Use: Calculate the salted hash for a password.  More secure.

# This SSHA function was taken from:
# http://coding.debuntu.org/php-how-calculate-ssha-value-string

function make_ssha_password($password){
  mt_srand((double)microtime()*1000000);
  $salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
  $hash = "{SSHA}" . base64_encode(pack("H*", sha1($password . $salt)) . $salt);
  return $hash;
}

?>
