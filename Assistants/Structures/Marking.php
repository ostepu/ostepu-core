<?php

/**
 * @file Marking.php contains the Marking class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2014
 */
include_once ( dirname(__FILE__) . '/Object.php' );

/**
 * the marking structure
 */
class Marking extends Object implements JsonSerializable {

    /**
     * @var string $id The identifier of this marking.
     *
     * type: string
     */
    private $id = null;

    /**
     * the $id getter
     *
     * @return the value of $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * the $id setter
     *
     * @param string $value the new value for $id
     */
    public function setId($value = null) {
        $this->id = $value;
    }

    /**
     * @var Submission $submission The submission this marking belongs to.
     */
    private $submission = null;

    /**
     * the $submission getter
     *
     * @return the value of $submission
     */
    public function getSubmission() {
        return $this->submission;
    }

    /**
     * the $submission setter
     *
     * @param Submission $value the new value for $submission
     */
    public function setSubmission($value = null) {
        $this->submission = $value;
    }

    /**
     * @var string $tutorId The id of the tutor that corrected the submission.
     */
    private $tutorId = null;

    /**
     * the $tutorId getter
     *
     * @return the value of $tutorId
     */
    public function getTutorId() {
        return $this->tutorId;
    }

    /**
     * the $tutorId setter
     *
     * @param string $value the new value for $tutorId
     */
    public function setTutorId($value = null) {
        $this->tutorId = $value;
    }

    /**
     * @var string $tutorComment a comment a tutor has made concerning a students submission.
     */
    private $tutorComment = null;

    /**
     * the $tutorComment getter
     *
     * @return the value of $tutorComment
     */
    public function getTutorComment() {
        return $this->tutorComment;
    }

    /**
     * the $tutorComment setter
     *
     * @param string $value the new value for $tutorComment
     */
    public function setTutorComment($value = null) {
        $this->tutorComment = $value;
    }

    /**
     * @var file $file  The file that contains the marked submission for the user.
     */
    private $file = null;

    /**
     * the $file getter
     *
     * @return the value of $file
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * the $file setter
     *
     * @param file $value the new value for $file
     */
    public function setFile($value = null) {
        $this->file = $value;
    }

    /**
     * @var int $points The amount of points a student has reached with his submission.
     *
     * type: int
     */
    private $points = null;

    /**
     * the $points getter
     *
     * @return the value of $points
     */
    public function getPoints() {
        return $this->points;
    }

    /**
     * the $points setter
     *
     * @param int $value the new value for $points
     */
    public function setPoints($value = null) {
        $this->points = str_replace(',', '.', $value);
    }

    /**
     * @var bool $outstanding if the submission stands out from the other submissions.
     */
    private $outstanding = null;

    /**
     * the $outstanding getter
     *
     * @return the value of $outstanding
     */
    public function getOutstanding() {
        return $this->outstanding;
    }

    /**
     * the $outstanding setter
     *
     * @param bool $value the new value for $outstanding
     */
    public function setOutstanding($value = null) {
        $this->outstanding = $value;
    }

    /**
     * @var string $prefix the marking status
     */
    private $status = null;

    /**
     * the $status getter
     *
     * @return the value of $status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * the $status setter
     *
     * @param string $value the new value for $status
     */
    public function setStatus($value = null) {
        $this->status = $value;
    }

    /**
     * @var date $date the date on which the marking was uploaded
     */
    private $date = null;

    /**
     * the $date getter
     *
     * @return the value of $date
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * the $date setter
     *
     * @param date $value the new value for $date
     */
    public function setDate($value = null) {
        $this->date = $value;
    }

    /**
     * @var int $hideFile Determines whether a marking file is displayed.
     */
    private $hideFile = null;

    /**
     * the $hideFile getter
     *
     * @return the value of $hideFile
     */
    public function getHideFile() {
        return $this->hideFile;
    }

    /**
     * the $hideFile setter
     *
     * @param hideFile $value the new value for $hideFile
     */
    public function setHideFile($value = null) {
        $this->hideFile = $value;
    }

    /**
     * Creates an Marking object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $markingId The id of the marking.
     * @param string $tutorId The id of the tutor(User).
     * @param string $fileId The id of the file.
     * @param string $submissionId The id of the submission.
     * @param string $tutorComment The tutor comment.
     * @param string $outstanding The outstanding flag.
     * @param string $status The status flag.
     * @param string $points The points.
     * @param string $date The date.
     * @param string $hideFile displays a marking.
     *
     * @return an marking object
     */
    public static function createMarking(
    $markingId, $tutorId, $fileId, $submissionId, $tutorComment, $outstanding,
            $status, $points, $date, $hideFile = null
    ) {
        return new Marking(array(
            'id' => $markingId,
            'tutorId' => $tutorId,
            'file' => new File(array('fileId' => $fileId)),
            'submission' => new Submission(array('id' => $submissionId)),
            'tutorComment' => $tutorComment,
            'outstanding' => $outstanding,
            'status' => $status,
            'points' => $points,
            'hideFile' => $hideFile,
            'date' => $date
        ));
    }

    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert() {
        return array(
            'M_id' => 'id',
            'U_id_tutor' => 'tutorId',
            'M_file' => 'file',
            'M_submission' => 'submission',
            'M_tutorComment' => 'tutorComment',
            'M_outstanding' => 'outstanding',
            'M_status' => 'status',
            'M_points' => 'points',
            'M_hideFile' => 'hideFile',
            'M_date' => 'date'
        );
    }

    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData($doubleEscaped = false) {
        $values = '';

        if ($this->id !== null) {
            $this->addInsertData(
                    $values, 'M_id', DBJson::mysql_real_escape_string($this->id)
            );
        }
        if ($this->tutorId !== null) {
            $this->addInsertData(
                    $values, 'U_id_tutor', DBJson::mysql_real_escape_string($this->tutorId)
            );
        }
        if ($this->file != null &&
                $this->file->getFileId() !== null) {
            $this->addInsertData(
                    $values, 'F_id_file', DBJson::mysql_real_escape_string($this->file->getFileId())
            );
        }
        if ($this->submission !== null &&
                $this->submission->getId() !== null) {
            $this->addInsertData(
                    $values, 'S_id', DBJson::mysql_real_escape_string($this->submission->getId())
            );
        }
        if ($this->tutorComment !== null) {
            $this->addInsertData(
                    $values, 'M_tutorComment', DBJson::mysql_real_escape_string($this->tutorComment)
            );
        }
        if ($this->outstanding !== null) {
            $this->addInsertData(
                    $values, 'M_outstanding', DBJson::mysql_real_escape_string($this->outstanding)
            );
        }
        if ($this->status !== null) {
            $this->addInsertData(
                    $values, 'M_status', DBJson::mysql_real_escape_string($this->status)
            );
        }
        if ($this->points !== null) {
            $this->addInsertData(
                    $values, 'M_points', DBJson::mysql_real_escape_string($this->points)
            );
        }
        if ($this->date !== null) {
            $this->addInsertData(
                    $values, 'M_date', DBJson::mysql_real_escape_string($this->date)
            );
        }
        if ($this->hideFile !== null) {
            $this->addInsertData(
                    $values, 'M_hideFile', DBJson::mysql_real_escape_string($this->hideFile)
            );
        }

        if ($values != '') {
            $values = substr(
                    $values, 1
            );
        }
        return ($doubleEscaped ? DBJson::mysql_real_escape_string($values) : $values);
    }

    /**
     * returns a sting/string[] of the database primary key/keys
     *
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey() {
        return'M_id';
    }

    /**
     * defines the marking status
     *
     * @return returns an mapping array
     */
    public static function getStatusDefinition() {
        return array(
            array(
                'id' => -1,
                'shortName' => 'nz',
                'longName' => 'nicht zugewiesen'
            ),
            array(
                'id' => '0',
                'shortName' => 'ne',
                'longName' => 'nicht eingesendet'
            ),
            array(
                'id' => 1,
                'shortName' => 'uk',
                'longName' => 'unkorrigiert'
            ),
            array(
                'id' => 2,
                'shortName' => 'vl',
                'longName' => 'vorläufig'
            ),
            array(
                'id' => 3,
                'shortName' => 'k',
                'longName' => 'korrigiert'
            ),
            array(
                'id' => 4,
                'shortName' => 'a',
                'longName' => 'automatisch'
            ),
        );
    }

    /*
     * ab hier werden die Korrekturstatus als Konstanten definiert
     * (entsprechend getStatusDefinition)
     */
    const NICHT_ZUGEWIESEN = -1;
    const NICHT_EINGESENDET = 0;
    const UNKORRIGIERT = 1;
    const VORLAEUFIG = 2;
    const KORRIGIERT = 3;
    const AUTOMATISCH = 4;

    /**
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct($data = array()) {
        if ($data === null) {
            $data = array();
        }

        foreach ($data AS $key => &$value) {
            if (isset($key)) {
                if ($key == 'file') {
                    $this->{
                            $key

                            } = File::decodeFile(
                                    $value, false
                    );
                } else
                if ($key == 'submission') {
                    $this->{
                            $key

                            } = Submission::decodeSubmission(
                                    $value, false
                    );
                } else {
                    $func = 'set' . strtoupper($key[0]) . substr($key, 1);
                    $methodVariable = array($this, $func);
                    if (is_callable($methodVariable)) {
                        $this->$func($value);
                    } else {
                        $this->{$key} = $value;
                    }
                }
            }
            unset($value);
        }
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeMarking($data) {
        /* if (is_array($data))reset($data);
          if (gettype($data) !== 'object' && !(is_array($data) && (current($data)===false || gettype(current($data)) === 'object'))){
          $e = new Exception();
          error_log(__FILE__.':'.__LINE__.' no object, '.gettype($data)." given\n".$e->getTraceAsString());
          ///return null;
          }
          if ((is_array($data) && (is_array(current($data)) || (current($data)!==false && get_class(current($data)) !== get_called_class()))) || (!is_array($data) && get_class($data) !== get_called_class())){
          $e = new Exception();
          $class = (is_array($data) && is_array(current($data)) ? 'array' : (is_array($data) ? (current($data)!==false ? get_class(current($data)) : 'array') : get_class($data)));
          error_log(__FILE__.':'.__LINE__.' wrong type, '.$class.' given, '.get_called_class()." expected\n".$e->getTraceAsString());
          ///return null;
          } */
        return json_encode($data);
    }

    /**
     * decodes $data to an object
     *
     * @param string $data json encoded data (decode=true)
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
     */
    public static function decodeMarking(
    $data, $decode = true
    ) {
        if ($decode &&
                $data == null) {
            $data = '{}';
        }

        if ($decode) {
            $data = json_decode($data);
        }

        $isArray = true;
        if (!$decode) {
            if ($data !== null) {
                reset($data);
                if (current($data) !== false && !is_int(key($data))) {
                    $isArray = false;
                }
            } else {
                $isArray = false;
            }
        }

        if ($isArray && is_array($data)) {
            $result = array();
            foreach ($data AS $key => $value) {
                $result[] = new Marking($value);
            }
            return $result;
        } else {
            return new Marking($data);
        }
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize() {
        $list = array();
        if ($this->id !== null) {
            $list['id'] = $this->id;
        }
        if ($this->submission !== null && $this->submission !== array()) {
            $list['submission'] = $this->submission;
        }
        if ($this->tutorId !== null) {
            $list['tutorId'] = $this->tutorId;
        }
        if ($this->tutorComment !== null) {
            $list['tutorComment'] = $this->tutorComment;
        }
        if ($this->file !== null) {
            $list['file'] = $this->file;
        }
        if ($this->points !== null) {
            $list['points'] = $this->points;
        }
        if ($this->outstanding !== null) {
            $list['outstanding'] = $this->outstanding;
        }
        if ($this->status !== null) {
            $list['status'] = $this->status;
        }
        if ($this->date !== null) {
            $list['date'] = $this->date;
        }
        if ($this->hideFile !== null) {
            $list['hideFile'] = $this->hideFile;
        }
        return array_merge($list, parent::jsonSerialize());
    }

    public static function ExtractMarking(
    $data, $singleResult = false, $FileExtension = '', $File2Extension = '',
            $SubmissionExtension = '', $MarkingExtension = '', $isResult = true
    ) {

        // generates an assoc array of files by using a defined list of
        // its attributes
        $files = DBJson::getObjectsByAttributes(
                        $data, File::getDBPrimaryKey(), File::getDBConvert(), $FileExtension
        );

        // generates an assoc array of files by using a defined list of
        // its attributes
        $files2 = DBJson::getObjectsByAttributes(
                        $data, File::getDBPrimaryKey(), File::getDBConvert(), $File2Extension . '2'
        );

        // generates an assoc array of a submission by using a defined
        // list of its attributes
        $submissions = DBJson::getObjectsByAttributes(
                        $data, Submission::getDBPrimaryKey(), Submission::getDBConvert(), $SubmissionExtension . '2'
        );

        // concatenates the submissions and the associated files
        $submissions = DBJson::concatObjectListsSingleResult(
                        $data, $submissions, Submission::getDBPrimaryKey(), Submission::getDBConvert()['S_file'], $files2, File::getDBPrimaryKey(), $File2Extension . '2', $SubmissionExtension . '2'
        );

        // sets the selectedForGroup attribute
        foreach ($submissions as & $submission) {
            if (isset($submission['selectedForGroup'])) {
                if (isset($submission['id']) &&
                        $submission['id'] == $submission['selectedForGroup']) {
                    $submission['selectedForGroup'] = (string) 1;
                } else {
                    unset($submission['selectedForGroup']);
                }
            }
        }

        // generates an assoc array of markings by using a defined list of
        // its attributes
        $markings = DBJson::getObjectsByAttributes(
                        $data, Marking::getDBPrimaryKey(), Marking::getDBConvert(), $MarkingExtension
        );

        // concatenates the markings and the associated files
        $res = DBJson::concatObjectListsSingleResult(
                        $data, $markings, Marking::getDBPrimaryKey(), Marking::getDBConvert()['M_file'], $files, File::getDBPrimaryKey(), $FileExtension, $MarkingExtension
        );

        // concatenates the markings and the associated submissions
        $res = DBJson::concatObjectListsSingleResult(
                        $data, $res, Marking::getDBPrimaryKey(), Marking::getDBConvert()['M_submission'], $submissions, Submission::getDBPrimaryKey(), $SubmissionExtension . '2', $MarkingExtension
        );

        if ($isResult) {
            // to reindex
            $res = array_values($res);
            $res = Marking::decodeMarking($res, false);

            if ($singleResult == true) {

                // only one object as result
                if (count($res) > 0) {
                    $res = $res[0];
                }
            }
        }

        return $res;
    }

}
