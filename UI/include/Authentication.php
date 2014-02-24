<?php
/**
 * @file Authentication.php
 * Contains the Authentication class.
 *
 * @author Ralf Busch
 */

include_once 'include/Helpers.php';
include_once 'include/AbstractAuthentication.php';

/**
 * Authentication class.
 *
 * Class for Loginsystem.
 */
class Authentication extends AbstractAuthentication
{
    /**
     * The default contructor which sets our sitekey. Sitekey have to be the same all the time!!
     */
    public function __construct()
    {
        // force to use session-cookies and to transmit SID over URL
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        // start session
        session_start();
    }

    /**
     * Prevent possible session fixation attack.
     */
    public static function preventSessionFix()
    {
        if (!isset( $_SESSION['SERVER_SID'] )) {
            // delete session content
            session_unset();
            $_SESSION = array();

            // restart session
            session_destroy();
            session_start();

            // generate new session id
            session_regenerate_id(true);

            // save status that serverSID is given
            $_SESSION['SERVER_SID'] = true;
        }
    }

    /**
     * Logs in a user.
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
        if ($message != "404" && empty($user) == false) {
            // create passwordhash with salt as suffix
            $password = $this->hashData('sha256',$password.$user['salt']);

            if ($password == $user['password']) {

                // save logged in uid
                $_SESSION['UID'] = $user['id'];
                $refresh = $this->refreshSession();
                return $refresh;
            } else {
                $userid = $user['id'];
                $databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$userid}/IncFailedLogin";
                $user = http_get($databaseURI, false, $message);
            }
        }
        return false;
    }
}
?>