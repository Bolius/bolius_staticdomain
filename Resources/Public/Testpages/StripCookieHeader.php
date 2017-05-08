<?php

// Attempt to send a cookie to the browser.
setcookie('Test', 'testvalue');

// Explanation
echo '
/**
 *
 * This page should be requested with a value in the "Cookie" http header.
 * It responds with "Cookie: yes" in case of a value in the cookie-header,
 * and "Cookie: no" in case of no cookie header.
 *
 * Used for testing if a server setup strips the "Cookie" header as intended.
 *
 * This page also attempts to send a cookie to the browser. Test your setup by testing if this cookie reaches the client
 * on the domain(s) where it should, and gets removed on the domains where you want it to be removed.
 *
 */

';

if (sizeof($_COOKIE)) {
    echo "Cookie: yes";
} else {
    echo "Cookie: no";
}
