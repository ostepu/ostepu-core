<?php
// TODO: Passwortverwaltung
// TODO: Verschluesselung

// Fragen
// * Klasse für Punktearten in JSON?                                    gute Frage

/**
* 
*/
class Course extends Object implements JsonSerializable
{
    /**
     * a string that identifies the course
     *
     * type: string
     */
    private $id;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }

    /**
     * the name of the course
     *
     * type: string
     */
    private $name;
    public function getName(){
        return $name;
    }
    public function setName($value){
        $name = $value;
    }

    /**
     * the semester in which the course is offered
     *
     * type: string
     */
    private $semester;
    public function getSemester(){
        return $semester;
    }
    public function setSemester($value){
        $semester = $value;
    }

    /**
     * a set of ids of exercise sheets that belong to this course
     *
     * type: string[]
     */
    private $exerciseSheets = array();
    public function getExerciseSheets(){
        return $exerciseSheets;
    }
    public function setExerciseSheets($value){
        $exerciseSheets = $value;
    }

    /**
     * the default size of groups in the course
     *
     * type: int
     */
    private $defaultGroupSize;
    public function getDefaultGroupSize(){
        return $defaultGroupSize;
    }
    public function setDefaultGroupSize($value){
        $defaultGroupSize = $value;
    }

    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'semester' => $this->semester,
            'exerciseSheets' => $this->exerciseSheets,
            'defaultGroupSize' => $this->defaultGroupSize
        );
    }
}

/**
* 
*/
class Backup extends Object implements JsonSerializable
{   
    /**
     * a unique identifier for a backup
     *
     * type: string
     */
    private $id;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }

    /**
     * the date on which the backup was created
     * 
     * type: date
     */
    private $date;
    public function getDate(){
        return $date;
    }
    public function setDate($value){
        $date = $value;
    }

    /**
     * a file where the backup is stored
     *
     * type: File
     */
    private $file;
    public function getFile(){
        return $file;
    }
    public function setFile($value){
        $file = $value;
    }

    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'date' => $this->date,
            'file' => $this->file
        );
    }
    
}

/**
* 
*/
class Group extends Object implements JsonSerializable
{
    /**
     * all members of the group of the current users
     * 
     * type: User[]
     */
    private $members;
    public function getMembers(){
        return $members;
    }
    public function setMembers($value){
        $members = $value;
    }

    /**
     * the id of the user that is the leader of the group
     *
     * type: User
     */
    private $leader;
    public function getLeader(){
        return $leader;
    }
    public function setLeader($value){
        $leader = $value;
    }

    /**
     * the id of the sheet for which this group exists
     *
     * type: string
     */
    private $sheetId;
    public function getSheetId(){
        return $sheetId;
    }
    public function setSheetId($value){
        $sheetId = $value;
    }
    
    public function jsonSerialize() {
        return array(
            'members' => $this->members,
            'leaderId' => $this->leader,
            'sheetId' => $this->sheetId
        );
    }
}

/**
* 
*/
class Invitation extends Object implements JsonSerializable
{
    /**
     * the user that was invited
     * 
     * type: User
     */
    private $user;
    public function getUser(){
        return $user;
    }
    public function setUser($value){
        $user = $value;
    }

    /**
     * the user that created the group
     * 
     * type: User 
     */
    private $leader;
    public function getLeader(){
        return $leader;
    }
    public function setLeader($value){
        $leader = $value;
    }

    /**
     * 
     * type: string
     */
    private $sheet;
    public function getSheet(){
        return $sheet;
    }
    public function setSheet($value){
        $sheet = $value;
    }
    
    public function jsonSerialize() {
        return array(
            'user' => $this->user,
            'leader' => $this->leader,
            'sheet' => $this->sheet
        );
    }
}

/**
* A pair of a course and a status for some user.
* The status reflects the rights the particular user has in that
* course
*/
class CourseStatus extends Object implements JsonSerializable
{
    /**
     * A course.
     *
     * type: Course
     */
    private $course;
    public function getCourse(){
        return $course;
    }
    public function setCourse($value){
        $course = $value;
    }

    /**
     * a string that defines which status the user has in that course.
     *
     * type: string
     */
    private $status;
    public function getStatus(){
        return $status;
    }
    public function setStatus($value){
        $status = $value;
    }
    
    public function jsonSerialize() {
        return array(
            'course' => $this->course,
            'status' => $this->status
        );
    }
}

/**
 * Contains all relevant Data for an exercise.
 */
class Exercise extends Object implements JsonSerializable
{
    // TODO
    // TODO wie werden in Exercise die Unteraufgaben eingebaut oder ist das unwichtig???
    // TODO

    /**
     * a string that identifies the exercise.
     *
     * type: string
     */
    private $id;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }

    /**
     * The id of the course this exercise belongs to.
     *
     * type: string
     */
    private $courseId;
    public function getCourseId(){
        return $courseId;
    }
    public function setCourseId($value){
        $courseId = $value;
    }

    /**
     * The id of the sheet this exercise is on.
     *
     * type: string
     */
    private $sheetId;
    public function getSheetId(){
        return $sheetId;
    }
    public function setSheetId($value){
        $sheetId = $value;
    }

    /**
     * The maximum amount of points a student can reach in this exercise.
     *
     * type: decimal
     */
    private $maxPoints;
    public function getMaxPoints(){
        return $maxPoints;
    }
    public function setMaxPoints($value){
        $maxPoints = $value;
    }

    /**
     * The type of points this exercise yields.
     *
     * type: string
     */
    private $type;
    public function getType(){
        return $type;
    }
    public function setType($value){
        $type = $value;
    }

    /**
     * the submissions (?) for this exercise
     *
     * type: Submission[]
     */
    private $submissions;
    public function getSubmissions(){
        return $submissions;
    }
    public function setSubmissions($value){
        $submissions = $value;
    }
    
    /**
     * a set of attachments that belong to this sheet
     *
     * type: File[]
     */
    private $attachments = array();
    public function getAttachments(){
        return $attachments;
    }
    public function setAttachments($value){
        $attachments = $value;
    }
    
    
    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'courseId' => $this->courseId,
            'sheetId' => $this->sheetId,
            'maxPoints' => $this->maxPoints,
            'type' => $this->type,
            'submissions' => $this->submissions,
            'attachments' => $this->attachments
        );
    }
}

class Submission extends Object implements JsonSerializable
{
    /**
     * The identifier of this submission.
     */
    private $id;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }

    /**
     * The id of the student that submitted his solution.
     *
     * type: string
     */
    private $studentId;
    public function getStudentId(){
        return $studentId;
    }
    public function setStudentId($value){
        $studentId = $value;
    }

    /**
     * a string that identifies the exercise this submission belongs to.
     *
     * type: string
     */
    private $exerciseId;
    public function getExerciseId(){
        return $exerciseId;
    }
    public function setExerciseId($value){
        $exerciseId = $value;
    }

    /**
     * A comment that a student made on his submission.
     *
     * type: string
     */
    private $comment;
    public function getComment(){
        return $comment;
    }
    public function setComment($value){
        $comment = $value;
    }
    
    /**
     * A students submission.
     *
     * type: File
     */
    private $file;
    public function getFile(){
        return $file;
    }
    public function setFile($value){
        $file = $value;
    }
    
    /**
     * If the submission has been accepted for marking.
     *
     * type: bool
     */
    private $accepted;
    public function getAccepted(){
        return $accepted;
    }
    public function setAccepted($value){
        $accepted = $value;
    }
    
    /**
     * If the submission has been selected as submission for the user's group
     *
     * type: bool
     */
    private $selectedForGroup;
    public function getSelectedForGroup(){
        return $selectedForGroup;
    }
    public function setSelectedForGroup($value){
        $selectedForGroup = $value;
    }
    
    /**
     * description
     *
     * type: date
     */
    private $date;
    public function getDate(){
        return $date;
    }
    public function setDate($value){
        $date = $value;
    }
    
    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'studentId' => $this->studentId,
            'exerciseId' => $this->exerciseId,
            'comment' => $this->comment,
            'file' => $this->file,
            'accepted' => $this->accepted,
            'selectedForGroup' => $this->selectedForGroup,
            'date' => $this->date
        );
    }
}

class Marking extends Object implements JsonSerializable
{
    /**
     * THe identifier of this marking.
     *
     * type: string
     */
    private $id;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }
    
    /**
     * The identifier of the submission this marking belongs to.
     *
     * type: string
     */
    private $submissionId;
    public function getSubmissionId(){
        return $submissionId;
    }
    public function setSubmissionId($value){
        $submissionId = $value;
    }
    
    /**
     * The id of the tutor that corrected the submission.
     * 
     * type: string
     */
    private $tutorId;
    public function getTutorId(){
        return $tutorId;
    }
    public function setTutorId($value){
        $tutorId = $value;
    }
    
    /**
     * a comment a tutor has made concerning a students submission.
     *
     * type: string
     */
    private $tutorComment;
    public function getTutorComment(){
        return $tutorComment;
    }
    public function setTutorComment($value){
        $tutorComment = $value;
    }
    
    /**
     * The file that contains the marked submission for the user.
     *
     * type: File
     */
    private $file;
    public function getFile(){
        return $file;
    }
    public function setFile($value){
        $file = $value;
    }
    
    /**
     * The amount of points a student has reached with his submission.
     *
     * type: decimal
     */
    private $points;
    public function getPoints(){
        return $points;
    }
    public function setPoints($value){
        $points = $value;
    }

    /**
     * if the submission stands out from the other submissions.
     *
     * type: bool
     */
    private $outstanding;
    public function getOutstanding(){
        return $outstanding;
    }
    public function setOutstanding($value){
        $outstanding = $value;
    }
    
    /**
     * status
     *
     * type: string
     */
    private $status;
    public function getStatus(){
        return $status;
    }
    public function setStatus($value){
        $status = $value;
    }
    
    /**
     * 
     *
     * type: date
     */
    private $date;
    public function getDate(){
        return $date;
    }
    public function setDate($value){
        $date = $value;
    }
    
    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'submissionId' => $this->submissionId,
            'tutorId' => $this->tutorId,
            'tutorComment' => $this->tutorComment,
            'file' => $this->file,
            'points' => $this->points,
            'outstanding' => $this->outstanding,
            'status' => $this->status,
            'date' => $this->date
        );
    }
}



/**
* 
*/
class ExerciseSheet extends Object implements JsonSerializable
{
    /**
     * a string that identifies the exercise sheet
     *
     * type: string
     */
    private $id;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }
    
     /**
     * The id of the course this exercise belongs to.
     *
     * type: string
     */
    private $courseId;
    public function getCourseId(){
        return $courseId;
    }
    public function setCourseId($value){
        $courseId = $value;
    }

    /**
     * the date and time of the last submission
     *
     * type: date
     */
    private $endDate;
    public function getEndDate(){
        return $endDate;
    }
    public function setEndDate($value){
        $endDate = $value;
    }

    /**
     * the date and time the exercise sheet is shown to students
     *
     * type: date
     */
    private $startDate;
    public function getStartDate(){
        return $startDate;
    }
    public function setStartDate($value){
        $startDate = $value;
    }

    /**
     * a file that contains student submissions that were previosly
     * assinged to a tutor
     *
     * type: File
     */
    private $zipFile;
    public function getZipFile(){
        return $zipFile;
    }
    public function setZipFile($value){
        $zipFile = $value;
    }
    
    /**
     * file that contains the sample solution
     *
     * type: File
     */
    private $sampleSolution;
    public function getSampleSolution(){
        return $sampleSolution;
    }
    public function setSampleSolution($value){
        $sampleSolution = $value;
    }

    /**
     * file that contains the exercise sheet
     *
     * type: File
     */
    private $sheetFile;
    public function getSheetFile(){
        return $sheetFile;
    }
    public function setSheetFile($value){
        $sheetFile = $value;
    }

    /**
     * a set of exercises that belong to this sheet
     *
     * type: Exercise[]
     */
    private $exercises = array();
    public function getExercises(){
        return $exercises;
    }
    public function setExercises($value){
        $exercises = $value;
    }

    /**
     * the maximum group size that is allowed for this exercise sheet
     *
     * type: integer
     */
    private $groupSize;
    public function getGroupSize(){
        return $groupSize;
    }
    public function setGroupSize($value){
        $groupSize = $value;
    }

    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'courseId' => $this->courseId,
            'endDate' => $this->endDate,
            'startDate' => $this->startDate,
            'zipFile' => $this->zipFile,
            'sampleSolution' => $this->sampleSolution,
            'sheetFile' => $this->sheetFile,
            'exercises' => $this->exercises,
            'groupSize' => $this->groupSize
        );
    }
}

/**
* 
*/
class File extends Object implements JsonSerializable
{
    /**
     * An id that identifies the file.
     *
     * type: string
     */
    private $fileId=null;
    public function getFileId(){
        return $fileId;
    }
    public function setFileId($value){
        $fileId = $value;
    }

    /**
     * The name that should be displayed for the file.
     *
     * type: string
     */
    private $displayName=null;
    public function getDisplayName(){
        return $displayName;
    }
    public function setDisplayName($value){
        $displayName = $value;
    }

    /**
     * The URL of the file
     *
     * type: string
     */
    private $address=null;
    public function getAddress(){
        return $address;
    }
    public function setAddress($value){
        $address = $value;
    }

    /**
     * When the file was created, this is necessary since the file might
     * be on another server as the server logic and/or interface.
     *
     * type: date/integer
     */
    private $timeStamp=null;
    public function getTimeStamp(){
        return $timeStamp;
    }
    public function seTimeStamp($value){
        $timeStamp = $value;
    }

    /**
     * the size of the file.
     *
     * type: decimal
     */
    private $fileSize=null;
    public function getFileSize(){
        return $fileSize;
    }
    public function setFileSize($value){
        $fileSize = $value;
    }

    /**
     * hash of the file, ensures that the user has up-/downloaded the right
     * file.
     *
     * type: string/integer
     */
    private $hash=null;
    public function getHash(){
        return $hash;
    }
    public function setHash($value){
        $hash = $value;
    }
    
     /**
     * content
     *
     * type: string
     */
    private $body=null;
    public function getBody(){
        return $body;
    }
    public function setBody($value){
        $body = $value;
    }
        
    public function jsonSerialize() {
        return [
            'fileId' => $this->fileId,
            'displayName' => $this->displayName,
            'address' => $this->address,
            'timeStamp' => $this->timeStamp,
            'fileSize' => $this->fileSize,
            'hash' => $this->hash,
            'body' => $this->body
        ];
    }
}

/**
* 
*/
abstract class Object
{
    /**
     * Possibly unnecessary
     * a string that identifies who sent the object
     *
     * type: string
     */
    private $sender;
    public function getSender(){
        return $sender;
    }
    public function setSender($value){
        $sender = $value;
    }
}

/**
 * Assigns certain exercises with to tutor.
 */
class TutorAssignment extends Object implements JsonSerializable
{
    /**
     * tutor
     *
     * type: User
     */
    private $tutor;
    public function getTutor(){
        return $tutor;
    }
    public function setTutor($value){
        $tutor = $value;
    }

    /**
     * ids of the submissions the tutor was assigned to correct.
     *
     * type: string[]
     */
    private $submissionIds = array();
    public function getSubmissionIds(){
        return $submissionIds;
    }
    public function setSubmissionIds($value){
        $submissionIds = $value;
    }

    public function jsonSerialize() {
        return array(
            'tutor' => $this->tutor,
            'submissionIds' => $this->submissionIds
        );
    }
}

/**
 * 
 */
class User extends Object implements JsonSerializable
{

    /**
     * a id that identifies the user
     *
     * type: int
     */
    private $id;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }
    
    /**
     * a string that identifies the user
     *
     * type: string
     */
    private $userName; 
    public function getUserName(){
        return $userName;
    }
    public function setUserName($value){
        $userName = $value;
    }

    /**
     * The user's email address.
     *
     * type: string
     */
    private $email;
    public function getEmail(){
        return $email;
    }
    public function setWmail($value){
        $email = $value;
    }

    /**
     * The user's first name(s)
     *
     * type: string
     */
    private $firstName;
    public function getFirstName(){
        return $firstName;
    }
    public function setFirstName($value){
        $firstName = $value;
    }

    /**
     * The user's last name(s)
     *
     * type: string
     */
    private $lastName;
    public function getLastName(){
        return $lastName;
    }
    public function setLastName($value){
        $lastName = $value;
    }

    /**
     * possibly a title the user holds
     *
     * type: string
     */
    private $title; 
    public function getTitle(){
        return $title;
    }
    public function setTitle($value){
        $title = $value;
    }

    /**
     * an array of CourseStatus objects that represents the courses
     * the user is enlisted in and which role she/he has in that course
     *
     * type: Course[]
     */
    private $courses = array();
    public function getCourses(){
        return $courses;
    }
    public function setCourses($value){
        $courses = $value;
    }

    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'userName' => $this->userName,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'title' => $this->title,
            'courses' => $this->courses
        );
    }
}

/**
 * 
 */
class Component extends Object implements JsonSerializable
{
    private $id = null;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }
    
    private $name = null;
    public function getName(){
        return $name;
    }
    public function setName($value){
        $name = $value;
    }
    
    private $address = null;
    public function getAddress(){
        return $address;
    }
    public function setAddress($value){
        $address = $value;
    }
    
    private $option = null;
    public function getOption(){
        return $option;
    }
    public function setOption($value){
        $option = $value;
    }
    
    // []
    private $links = null;
    public function getLinks(){
        return $links;
    }
    public function setLinks($value){
        $links = $value;
    }
    
    public function __construct($data) {
        foreach ($data AS $key => $value) {
            if (is_array($value)) {
                $sub = new Component;
                $sub->set($value);
                $value = $sub;
            }
            $this->{$key} = $value;
        }
    }
    
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'option' => $this->option,
            'links' => $this->links
        ];
    }

}

/**
 * 
 */
class Link extends Object implements JsonSerializable
{
    private $id = null;
    public function getId(){
        return $id;
    }
    public function setId($value){
        $id = $value;
    }
    
    private $name = null;
    public function getName(){
        return $name;
    }
    public function setName($value){
        $name = $value;
    }
    
    private $address = null;
    public function getAddress(){
        return $address;
    }
    public function setAddress($value){
        $address = $value;
    }
    
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address
        ];
    }
}
?>