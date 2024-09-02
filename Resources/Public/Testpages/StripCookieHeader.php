<style>
    body {
        font-family: "Courier", serif;
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
error_reporting(E_ALL ^ E_NOTICE);
loadTypo3Config();

// Attempt to send a cookie to the browser.
setcookie('Test', 'testvalue');

echo "<table>";
echo "<tr><td>Hostname:</td><td>" . htmlEscapeString(gethostname()) . "</td></tr>";
echo "<tr><td>Sitename:</td><td>" . htmlEscapeString($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']) . "<br/>";
echo "<tr><td>Varnish headers:</td><td>";


$headers = ['HTTP_X_VARNISH' => 'X-Varnish', 'HTTP_X_FORWARDED_FOR' => 'X-Forwarded-for'];

foreach ($headers as $header => $name) {
    echo isset($_SERVER[$header])
        ? htmlEscapeString($name) . ': ' . htmlEscapeString($_SERVER[$header]) . '<br/>'
        : '';
}

if (!array_intersect_key($_SERVER, $headers)) {
    echo "Apparently not";
}

echo "<tr><td>Cookies in request header:</td><td>";
echo empty($_COOKIE) ? 'None' : '';

foreach ($_COOKIE as $key => $value) {
    echo htmlEscapeString(crop($key)) . ' : ' . htmlEscapeString(crop($value)) . '<br/>';
}

echo '</td></tr></table>';

// Explanation
echo nl2br(
    '
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

'
);

/** Crop a string to a maximum length of 20 characters with ellipsis dots if the string is longer than 20 characters.
 *
 * @param string $string
 * @return string
 */
function crop(string $string): string
{
    return substr($string, 0, 20) . (strlen($string) > 20 ? '...' : '');
}

/** Utilize HTML escaping to prevent XSS attacks and other security issues.
 *
 * @param string $string
 * @return string
 */
function htmlEscapeString(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/** A bit hacky, but we need to call this outside of TYPO3 to get the configuration values from the configuration files.
 *
 * @return void
 */
function loadTypo3Config(): void
{
    $typo3confDir = __DIR__ . '../../../../../../';
    $GLOBALS['TYPO3_CONF_VARS'] = (include_once($typo3confDir . 'LocalConfiguration.php'));
    include_once $typo3confDir . 'AdditionalConfiguration.php';
}