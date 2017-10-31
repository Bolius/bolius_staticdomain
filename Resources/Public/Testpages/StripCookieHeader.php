<style>
    body {
        font-family: "Courier";
    }
    tr:nth-child(odd) {
        background-color: #f0f0f0;
    }
    td {
        padding: 0.2em 2em;
        vertical-align: top;
    }
    pre {
        border: solid 1px #e0e0e0;
        background: #f0f0f0;
        padding: 0.2em 1em;
    }
    
</style>
<?php
error_reporting(E_ALL ^E_NOTICE);

/**
 * A bit hacky, but we need to call this outside of TYPO3.
 */
$typo3confDir = __DIR__ . '../../../../../../';
$GLOBALS['TYPO3_CONF_VARS'] = (include_once($typo3confDir . 'LocalConfiguration.php'));
include_once $typo3confDir . 'AdditionalConfiguration.php';

// Attempt to send a cookie to the browser.
setcookie('Test', 'testvalue');


echo "<table>";
echo "<tr><td>Hostname:</td><td>" . gethostname() . "</td></tr>";
echo "<tr><td>Sitename:</td><td>" . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] . "<br/>";
echo "<tr><td>Varnish headers:</td><td>";

if (isset($_SERVER['HTTP_X_VARNISH']) || isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    echo 'X-Varnish: ' . $_SERVER['HTTP_X_VARNISH'] . "<br/>";
    echo 'X-Forwarded-for: ' . $_SERVER['HTTP_X_FORWARDED_FOR'] . "<br/>";
} else {
    echo "Apparently not";
}

echo "<tr><td>Cookies in request header:</td><td>";

if (sizeof($_COOKIE)) {
    foreach ($_COOKIE as $k => $v) {
        echo crop($k) . " : " . crop($v) . "<br/>";

    }

} else {
    echo "None";
}

echo "</td>";

echo "</tr></table>";

// Explanation
echo nl2br('
/**
 * Cookies from client to server:
 * ------------------------------
 * This page should be requested with a value in the "Cookie" http header.
 * It responds with the cookie values if there are any,
 * and "None" in case of no cookie header.
 *
 * Used for testing if a server setup strips the "Cookie" header as intended.
 *
 * 
 * Cookies from server to client:
 * ------------------------------
 * This page also attempts to send a cookie to the browser. Test your setup by testing if this cookie reaches the client
 * on the domain(s) where it should, and gets removed on the domains where you want it to be removed.
 *
 */

');


function crop ($s) {
    return substr($s, 0, 20) . (strlen($s) > 20 ? '...' : '');
}