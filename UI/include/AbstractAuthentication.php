<?php
/**
 * @file AbstractAuthentication.php
 * Contains the AbstractAuthentication class.
 *
 * @author Ralf Busch
 */

include_once 'include/Helpers.php';
include_once 'include/Config.php';

/**
 * AbstractAuthentication class.
 *
 * Abstract Class for Authentication.
 */
abstract class AbstractAuthentication
{
    abstract public function __construct();
    abstract public function loginUser($username, $password);

    /**
     * @var SiteKey as Password for all Hashfunctions
     */
    protected $siteKey = "b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4";

    /**
     * Generates a random string.
     *
     * @param int $length The length of the random string, 8 by default.
     * @return string
     */
    protected function randomBytes($length = 8)
    {
        $result = openssl_random_pseudo_bytes($length);

        return $result;
    }

    /**
     * Hash string with given method.
     *
     * @param string $method Defines hashmethod e.g. "sha256".
     * @param string $data The string which should be hashed.
     * @return string
     */
    protected function hashData($method, $data)
    {
        return hash_hmac($method, $data, $this->siteKey);
    }

    /**
     * Refreshes a user's session.
     *
     * @return true if refreshSession is successful
     */
    protected function refreshSession()
    {
        global $databaseURI;
        $_SESSION['SESSION'] = $this->hashData("md5", session_id() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

        // create Session in DB
        $sessionbody = array('user' => $_SESSION['UID'],
                             'session' => $_SESSION['SESSION']);
        $sessionbody = json_encode($sessionbody);
        $url = "{$databaseURI}/session";
        http_post_data($url, $sessionbody, false, $message);

        // only true if session is created in DB
        if ($message == "201") {
            $_SESSION['SIGNED'] = true;
            $_SESSION['LASTACTIVE'] = $_SERVER['REQUEST_TIME'];
            $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
            return true;
        } else {
            return false;
        }

    }

    /**
     * Checks if user is logged in.
     *
     * @return BOOL A boolean value that indicates if the is logged in.
     */
    public static function checkLogin()
    {

        // check if logged in
        if (!isset($_SESSION['SIGNED']) || !$_SESSION['SIGNED']) {
            return false;
        }

        // check for timeout (after 10 minutes of inactivity)
        if (!isset($_SESSION['LASTACTIVE'])
            || (($_SESSION['LASTACTIVE'] + 10*60) <= $_SERVER['REQUEST_TIME'])) {
            return false;
        }

        // check if browser agent changed
        if (!isset($_SESSION['HTTP_USER_AGENT'])
            || ($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        // check if ip changed
        if (!isset($_SESSION['IP'])
            || ($_SESSION['IP'] != $_SERVER['REMOTE_ADDR'])) {
            return false;
        }

        // update last activity
        $_SESSION['LASTACTIVE'] = $_SERVER['REQUEST_TIME'];
        return true;
    }

    /**
     * Logs out a user.
     *
     * @param bool $noback Set it manually to true, if no back-redirection-url is wanted.
     */
    public static function logoutUser($noback = false)
    {
        global $databaseURI;
        if($_GET['action'] == "logout" || $noback == true) {
            // delete session in DB
            $session = $_SESSION['SESSION'];
            http_delete("{$databaseURI}/session/{$session}",true,$message,true);

            // delete session in UI
            session_destroy();
            // redirect to Loginpage
            header('Location: Login.php');
            exit();
        } else {
            // delete session in DB
            $session = $_SESSION['SESSION'];
            http_delete("{$databaseURI}/session/{$session}",true,$message,true);

            // delete session in UI
            session_destroy();

            // get current relative url
            $backurl = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

            // if someone opens a page with /UI (without index.php) or a existing page without .php suffix
            if (!strpos($backurl,'.php')&&!file_exists($backurl.".php")) {
                $backurl = "index.php";
            } elseif (!strpos($backurl,'.php')&&file_exists($backurl.".php")) {
                $backurl = $backurl.".php";
            }

            // Url GET parameters
            $urlparameters = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            if ($urlparameters != "") {
                $urlparameters = "?" . rawurlencode($urlparameters);
            }

            // redirect to Loginpage and save current page in GET param
            header('Location: Login.php?back=' . $backurl . $urlparameters);
            exit();
        }
    }

    /**
     * Check the Rights of a User.
     * If the user does not have the rights required to view the page redirect
     * to index.php with error message 403.
     *
     * @param int $minimum Is the minimum right for visiting the given php site.
     * @param int $cid Is the courseid.
     * @param int $uid Is the userid.
     * @param array $data An associative array that contains the coursestatus.
     */
    public static function checkRights($minimum, $cid, $uid, $data)
    {
        // check if user exists in course
        if ($data !== array()) {
            // find the right course
            $status = -1;
            foreach ($data['courses'] as $element) {
                if ($element['course']['id'] == $cid) {
                    $status = $element['status'];
                    break;
                }
            }
            // check if minimum right is given
            if ($status < $minimum) {
                set_error("403");
            }

        } else {
            set_error("403");
        }
    }

    /**
     * Generates a random Salt hashed with sha1.
     *
     * @return string Salt hashed in Sha1.
     */
    public function generateSalt()
    {
        $random = $this->randomBytes();

        return $this->hashData("sha1", $random);
    }

    /**
     * Generates a random Salt hashed with sha1.
     *
     * @param string $password Is the password string in plaintext.
     * @param string $salt Is the salt string, which is hashed in sha1.
     *
     * @return string Password hashed in Sha256.
     */
    public function hashPassword($password, $salt)
    {
        return $this->hashData("sha256", $password . $salt);
    }
}
?>