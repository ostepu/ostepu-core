<?php

/**
 * @file CourseStatus.php contains the CourseStatus class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */
include_once ( dirname(__FILE__) . '/Object.php' );

/**
 * the course status structure
 */
class CourseStatus extends Object implements JsonSerializable {

    /**
     * @var Course $course A course.
     */
    private $course = null;

    /**
     * the $course getter
     *
     * @return the value of $course
     */
    public function getCourse() {
        return $this->course;
    }

    /**
     * the $course setter
     *
     * @param string $value the new value for $course
     */
    public function setCourse($value = null) {
        $this->course = $value;
    }

    /**
     * @var string $status  a string that defines which status the user has in that course.
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
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert() {
        return array(
            'CS_course' => 'course',
            'CS_status' => 'status'
        );
    }

    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData($doubleEscaped = false) {
        $values = '';

        if ($this->status != null) {
            $this->addInsertData(
                    $values, 'CS_status', DBJson::mysql_real_escape_string($this->status)
            );
        }
        if ($this->course != null &&
                $this->course->getId() != null) {
            $this->addInsertData(
                    $values, 'C_id', DBJson::mysql_real_escape_string($this->course->getId())
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
        return array(
            'C_id',
            'U_id'
        );
    }

    /**
     * returns an array to get the course status defintions
     */
    public static function getStatusDefinition($flip = false) {
        $arr = array(
            '0' => 'student',
            '1' => 'tutor',
            '2' => 'lecturer',
            '3' => 'administrator',
            '4' => 'super-administrator'
        );
        return (!$flip ? $arr : array_flip($arr));
    }

    /*
     * die Konstanten der Nutzerstatus (entsprechen getStatusDefinition)
     */
    const STUDENT = 0;
    const TUTOR = 1;
    const LECTURER = 2;
    const ADMIN = 3;
    const SUPERADMIN = 4;

    /**
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct($data = array()) {
        if ($data === null) {
            $data = array();
        }

        foreach ($data AS $key => $value) {
            if (isset($key)) {
                if ($key == 'course') {
                    $this->{
                            $key

                            } = Course::decodeCourse(
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
        }
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeCourseStatus($data) {
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
    public static function decodeCourseStatus(
    $data, $decode = true
    ) {
        if ($decode && $data == null) {
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
                $result[] = new CourseStatus($value);
            }
            return $result;
        } else {
            return new CourseStatus($data);
        }
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize() {
        $list = array();
        if ($this->course !== null) {
            $list['course'] = $this->course;
        }
        if ($this->status !== null) {
            $list['status'] = $this->status;
        }
        return array_merge($list, parent::jsonSerialize());
    }

}
