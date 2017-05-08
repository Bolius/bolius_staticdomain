<?php

// Attempt to set a cookie. This makes cookie stripping testing easier with a browser.
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
 */

';

if (sizeof($_COOKIE)) {
    echo "Cookie: yes";
} else {
    echo "Cookie: no";
}
