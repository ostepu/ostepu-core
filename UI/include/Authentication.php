<?php
/**
 * @file Authentication.php
 * Contains the Authentication class.
 */

include_once 'include/Helpers.php';

/**
 * Authentication class.
 *
 * Class for Loginsystem.
 * @author Ralf Busch
 */
class Authentication
{
    /**
     * @var SiteKey as Password for all Hashfunctions
     */
    private $siteKey;
    public $error;

    /**
     * The default contructor which sets our sitekey. Sitekey have to be the same all the time!!
     */
    public function __construct()
    {
        $this->siteKey = "b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4";

        // force to use session-cookies and to transmit SID over URL
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        
        // start session
        session_start();
    }

    /**
     * Generate random String
     *
     * @param int $length is the length of the random string, default is 50
     * @return string
     */
    private function randomBytes($length = 8)
    {
        $result = openssl_random_pseudo_bytes($length);

        return $result;
    }

    /**
     * hash string with given method
     *
     * @param string $method defines hashmethod e.g. "sha256"
     * @param string $data is the string which has to be hashed
     * @return string
     */
    protected function hashData($method, $data)
    {
        return hash_hmac($method, $data, $this->_siteKey);
    }

    /**
     * Prevent possible session fixation attack
     */
    public function preventSessionFix()
    {
        if (!isset( $_SESSION['server_SID'] )) {
            // delete session content
            session_unset();
            $_SESSION = array();
            // restart session
            session_destroy();
            session_start();
            // generate new session id
            session_regenerate_id();
            // save status that serverSID is given
            $_SESSION['server_SID'] = true;
        }   
    }

    /**
     * Log in a user
     *
     * @param string $username
     * @param string $password
     * @return true if login is successful 
     */
    public function loginUser($username, $password)
    {
        $databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$username}";
        $user = http_get($databaseURI, false, $message);
        $user = json_decode($user, true);

        // check if user exists 
        if ($message != "404") {
            // create passwordhash with salt as suffix
            $password = $this->hashData('sha256',$password.$user['salt']);

            if ($password == $user['password']) {

                // save logged in uid
                $_SESSION['uid'] = $user['id'];
                $refresh = $this->refreshSession($username, $password);
                return $refresh;
            } else {
                /**
                 * @todo increase FailedLogin field 
                 */
            }
        }
        return false;
    }

    /**
     * Log in a user
     *
     * @param string $username
     * @param string $password
     * @return true if refreshSession is successful 
     */
    private function refreshSession($username, $password)
    {
        $_SESSION['session'] = $this->hashData("md5", session_id().$username.$password);
        /**
         * @todo create session on server with $_SESSION['session']
         */

        /**
         * @todo only if created session on server is successful
         */
        $_SESSION['signed'] = true;
        $_SESSION['lastactive'] = $_SERVER['REQUEST_TIME'];
    

        return true;
    }

    /**
     * check if user is logged in
     */
    public function checkLogin()
    {
        session_regenerate_id(true);
        if (!isset($_SESSION['signed']) || !$_SESSION['signed']) {return false;}
        // check for timeout (after 10 minutes of inactivity)
        if (!isset($_SESSION['lastactive']) || ($_SESSION['lastactive'] + 10*60) <= $_SERVER['REQUEST_TIME']) {return false;}

        // update last activity 
        $_SESSION['lastactive'] = $_SERVER['REQUEST_TIME'];
        return true;
    }

    /**
     * Log out a user
     */
    public function logoutUser()
    {
        /**
         * @todo delete session from uid in DB
         */
        if($_GET['action'] == "logout") {
            session_destroy();
            header('location: Login.php');
            exit;
        } else {
            session_destroy();
            // get current relative url
            $backurl = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            // if someone opens a page with /UI (without index.php) or a existing page withour .php suffix
            if (!strpos($backurl,'.php')&&!file_exists($backurl.".php")) {
                $backurl = "index.php";
            } elseif (!strpos($backurl,'.php')&&file_exists($backurl.".php")) {
                $backurl = $backurl.".php";
            }
            $urlparameters = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            if ($urlparameters != "") {$urlparameters = "?".$urlparameters;}
            // redirect to Loginpage and save current page in GET param
            header('location: Login.php?back='.$backurl.$urlparameters);
            exit;
        }
    }

    /**
     * check the Rights of an User and logout if it hasn't the correct one
     */
    public function checkRights($minimum, $cid, $uid, $data)
    {
        // check if user exists 
        if (!is_null($data)) {
            // check if minimum right is given
            if ($data['courses'][0]['status'] < $minimum) {
                header('location: index.php?error=403');
            } 
        }
    }
}
?>