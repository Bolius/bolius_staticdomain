<?php

setcookie('Test', 'testvalue');

echo '
 *
 * This page responds with a value in the "Set-cookie" http header.
 *
 * Used for testing if a server setup strips the "Set-cookie" header as intended.
 *
';  