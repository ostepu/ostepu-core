<?php
/**
 * @file StudIPAuthentication.php
 * Contains the StudIPAuthentication class.
 *
 * @author Ralf Busch
 * @todo create User in DB if non existing
 * @todo add course to user if the user isn't in the course yet
 */

include_once 'include/Helpers.php';
include_once 'include/AbstractAuthentication.php';

/**
 * StudIPAuthentication class.
 *
 * Class for StudIP Loginsystem.
 */
class StudIPAuthentication extends AbstractAuthentication
{
    /**
     * @var UserID from studip
     */
    private $uid;

    /**
     * @var SessionID from studip
     */
    private $sid;

    /**
     * @var CourseID
     */
    private $cid;

    /**
     * @var userData of the user
     */
    private $userData;

    /**
     * The default contructor which logs the user in, if uid, cid and sid is given in GET Parameters.
     */
    public function __construct()
    {
        if (isset($_GET['uid'])) {
            $this->uid = cleanInput($_GET['uid']);
        }
        if (isset($_GET['sid'])) {
            $this->sid = cleanInput($_GET['sid']);
        }
        if (isset($_GET['cid'])) {
            $this->cid = cleanInput($_GET['cid']);
        }
        if (isset($_GET['uid']) && isset($_GET['sid']) && isset($_GET['cid'])) {
            // log in user and return result
            $signed = $this->loginUser($this->uid, "");

            if ($signed) {
                $courseStatus = $this->findCourseStatus();
                $sites = array('0' => 'Student.php',
                               '1' => 'Tutor.php',
                               '3' => 'Lecturer.php');
                header('location: ' . $sites[$courseStatus] . '?cid=' . $this->cid);
            } else {
                $this->logoutUser(true);
            }
        }
    }

    /**
     * Find the correct Course Status for given cid
     *
     * @return Status for Course
     */
    private function findCourseStatus()
    {
        $this->userData['courses'];
        foreach ($this->userData['courses'] as $course) {
            if ($course['course']['id'] == $this->cid) {
                return $course['status'];
            }
        }
    }

    /**
     * Check if user is logged in in StudIP
     *
     * @param string $uid Is the userid from StudIP
     * @param string $sid Is the sessionid from StudIP
     * @return true if user is logged in
     */
    public function checkUserInStudip($uid, $sid)
    {
        $query = "https://studip.uni-halle.de/upgateway/intern/request.php?cmd=check_user&uid={$uid}&sid={$sid}";
        $check = http_get($query, false);

        return $check == "OK";
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
        $this->userData = http_get($databaseURI, false, $message);
        $this->userData = json_decode($this->userData, true);

        // check if user exists in our system
        if ($message != "404") {
            // check if logged in in studip
            $studip = $this->checkUserInStudip($this->uid,$this->sid);

            if ($studip) {

                // save logged in uid
                $_SESSION['UID'] = $this->userData['id'];
                $refresh = $this->refreshSession();
                return $refresh;
            }
        }
        return false;
    }
}
?>