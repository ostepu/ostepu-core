<?php
/**
 * @file StudIPAuthentication.php
 * Contains the StudIPAuthentication class.
 *
 * @author Ralf Busch
 */

include_once 'include/Helpers.php';
include_once 'include/AbstractAuthentication.php';
include_once '../Assistants/Structures.php';

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
     * @var CourseStatus of the user
     */
    private $courseStatus;

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
            // if cid is not numeric
            if (!is_numeric($this->cid)) {
                set_error("409");
                exit();
            }
        }
        if (isset($_GET['uid']) && isset($_GET['sid']) && isset($_GET['cid'])) {
            // log in user and return result
            $signed = $this->loginUser($this->uid, "");

            if ($signed == true) {

                // multiplexer which site the user wants to see
                $sites = array('0' => 'Student.php',
                               '1' => 'Tutor.php',
                               '3' => 'Lecturer.php');

                // if you are not in the course or the course doesn't exist set error 403
                if (isset($this->courseStatus) && (empty($sites[$this->courseStatus]) == false)) {
                    header('location: ' . $sites[$this->courseStatus] . '?cid=' . $this->cid);
                } else {
                    set_error("403");
                    exit();
                }
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
        if (isset($this->userData['courses'])) {
            foreach ($this->userData['courses'] as $course) {
                if ($course['course']['id'] == $this->cid) {
                    return $course['status'];
                }
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
     * Give user Data from Studip
     *
     * @param string $uid Is the userid from StudIP
     * @return User $user which ist our Structure User with the given information from StudIP
     */
    public function getUserInStudip($uid)
    {
        $query = "https://studip.uni-halle.de/upgateway/intern/request.php?cmd=get_user&uid={$uid}";
        $getUserData = http_get($query, false);

        // convert output to our user structure
        $getUserData = explode(":", $getUserData);

        $user = User::createUser(NULL,$getUserData[4],$getUserData[2],$getUserData[0],$getUserData[1],NULL,"1","noPassword","noSalt","0",$uid);
        return $user;
    }

    /**
     * Create User in DB
     *
     * @param User $data UserData which contains the created User
     * @return true if user is created
     */
    public function createUser($data)
    {
        $data = User::encodeUser($data);
        $url = "http://141.48.9.92/uebungsplattform/DB/DBControl/user";
        http_post_data($url, $data, true, $message);

        return $message == "201";
    }

    /**
     * Add Course to an user
     *
     * @param string $userId UserID in our System (Attention: NOT the externalID)
     * @param string $courseID CourseID
     * @param string $status The Status the user wants to have in given course.
     * @return true if user is logged in
     */
    public function createCourseStatus($userId,$courseId,$status)
    {
        $data = User::encodeUser(User::createCourseStatus($userId,$courseId,$status));

        $url = "http://141.48.9.92/uebungsplattform/DB/DBControl/coursestatus";
        http_post_data($url, $data, true, $message);

        return $message == "201";
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
        if ($message != "404" && empty($this->userData) == false) {
            // check if logged in in studip
            $studip = $this->checkUserInStudip($this->uid,$this->sid);

            if ($studip == true) {

                // save logged in uid
                $_SESSION['UID'] = $this->userData['id'];

                // refresh Session in UI and DB
                $refresh = $this->refreshSession();

                // get the courseStatus for given course
                $this->courseStatus = $this->findCourseStatus();

                // if user hase no status in course create it
                if (!isset($this->courseStatus)) {
                    $CourseStatusResponse = $this->createCourseStatus($this->userData['id'],$this->cid,"0");

                    // set courseStatus to 0 only if status is created in DB successfully
                    if ($CourseStatusResponse == true) {
                        $this->courseStatus = "0";
                    } 
                }

                return $refresh;
            }
        } else {
            // create new user from studIP
            $newUser = $this->getUserInStudip($username);
            $response = $this->createUser($newUser);
            
            // if successful try to login new user
            if ($response == true) {
                return $this->loginUser($username, "");
            }
        }
        return false;
    }
}
?>