<?php
/**
 * @file DBTransaction.php contains the DBTransaction class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "Transaction" table from database
 */
class DBTransaction
{

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    private $_component = null;
    public function __construct( )
    {
        $component = new Model('transaction,course', dirname(__FILE__), $this, false, false, array('cloneable'=>true,
                                                                                                   'addOptionsToParametersAsPostfix'=>true,
                                                                                                   'addProfileToParametersAsPostfix'=>true));
        $this->_component=$component;
        $component->run();
    }

    /**
     * Deletes a transaction.
     *
     * Called when this component receives an HTTP DELETE request to
     * (/:name)/authentication/:auid/transaction/:tid.
     *
     * @param string $tid The id of the transaction that is being deleted.
     * @param string $auid A text to verify the call .
     * @param int $name A optional name for the transaction table.
     */
    public function deleteTransaction( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteTransaction',dirname(__FILE__).'/Sql/DeleteTransaction.sql',$params,201,'Model::isCreated',array(new Transaction()),'Model::isProblem',array(new Transaction()));
    }

    public function deleteTransactionShort( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteTransactionShort',dirname(__FILE__).'/Sql/DeleteTransactionShort.sql',$params,201,'Model::isCreated',array(new Transaction()),'Model::isProblem',array(new Transaction()));
    }

    /**
     * Adds a transaction.
     *
     * Called when this component receives an HTTP POST request to
     * /transaction(/).
     * The request body should contain a JSON object representing the
     * transaction's attributes.
     *
     * @param string $name A optional name for the transaction table.
     */
    public function addTransaction( $callName, $input, $params = array() )
    {
        $uuid = new uuid();
         // this array contains the indices of the inserted objects
        $res = array( );
        $params['random'] = str_replace('-','',$uuid->get());
        $input->setTransactionId(null);

        $positive = function($input, $random) {
            // sets the new auto-increment id
           
           $course = Course::ExtractCourse($input[count($input)-1]->getResponse(),true);

            // sets the new auto-increment id
            $obj = new Transaction( );                
            $obj->setTransactionId( $course->getId() . '_' . $input[count($input)-2]->getInsertId( ) . '_' . $random );

            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addTransaction',dirname(__FILE__).'/Sql/AddTransaction.sql',array_merge($params,array( 'in' => $input)),201,$positive,array('random'=>$params['random']),'Model::isProblem',array(new Transaction()),false);
    }

    public function addSheetTransaction( $callName, $input, $params = array() )
    {
        $uuid = new uuid();
         // this array contains the indices of the inserted objects
        $res = array( );
        $params['random'] = str_replace('-','',$uuid->get());
        $input->setTransactionId(null);

        $positive = function($input, $random) {
            // sets the new auto-increment id
           
           $course = Course::ExtractCourse($input[count($input)-1]->getResponse(),true);

            // sets the new auto-increment id
            $obj = new Transaction( );                
            $obj->setTransactionId( $course->getId() . '_' . $input[count($input)-2]->getInsertId( ) . '_' . $random );

            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addSheetTransaction',dirname(__FILE__).'/Sql/AddSheetTransaction.sql',array_merge($params,array( 'in' => $input)),201,$positive,array('random'=>$params['random']),'Model::isProblem',array(new Transaction()),false);
    }

   public function addExerciseTransaction( $callName, $input, $params = array())
    {
        $uuid = new uuid();
         // this array contains the indices of the inserted objects
        $res = array( );
        $params['random'] = str_replace('-','',$uuid->get());
        $input->setTransactionId(null);

        $positive = function($input, $random) {
            // sets the new auto-increment id
           
           $course = Course::ExtractCourse($input[count($input)-1]->getResponse(),true);

            // sets the new auto-increment id
            $obj = new Transaction( );                
            $obj->setTransactionId( $course->getId() . '_' . $input[count($input)-2]->getInsertId( ) . '_' . $random );

            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addExerciseTransaction',dirname(__FILE__).'/Sql/AddExerciseTransaction.sql',array_merge($params,array( 'in' => $input)),201,$positive,array('random'=>$params['random']),'Model::isProblem',array(new Transaction()),false);
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        if (isset($params['tid'])){
            $params['courseid'] = Transaction::getCourseFromTransactionId($params['tid']);
            $params['random'] = Transaction::getRandomFromTransactionId($params['tid']);
            $params['tid'] = Transaction::getIdFromTransactionId($params['tid']);
        }

        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract transaction data from db answer
                    $res = Transaction::ExtractTransaction( $inp->getResponse( ), false);
                    $result['content'] = array_merge($result['content'], (is_array($res) ? $res : array($res)));
                    $result['status'] = 200;
                }
            }
            return $result;
        };

        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call($linkName, $params, '', 200, $positive, array(), 'Model::isProblem', array(), 'Query');
    }

    public function getMatch($callName, $input, $params = array())
    {
        return $this->get($callName,$callName,$params);
    }

    /**
     * Removes the component from a given course
     *
     * Called when this component receives an HTTP DELETE request to
     * (/$name)/course/$courseid.
     *
     * @param string $courseid The id of the course.
     * @param string $name A optional name for the transaction table.
     */
    public function deleteCourse( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteCourse',dirname(__FILE__).'/Sql/DeleteCourse.sql',array($params),201,'Model::isCreated',array(new Course()),'Model::isProblem',array(new Course()),false);
    }

    public function cleanTransactions( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('cleanTransactions',dirname(__FILE__).'/Sql/CleanTransactions.sql',array($params),201,'Model::isCreated',array(new Course()),'Model::isProblem',array(new Course()),false);
    }

    public function getAmountOfExpiredTransactions( $callName, $input, $params = array() )
    {    
        $positive = function($input) {
            $result = Model::isEmpty();$result['content']=array();
            foreach($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                     $result['content'] = $inp->getResponse();
                     foreach ($result['content'] as &$res){
                        $res['component'] = $this->_component->_conf->getName();
                    }
                    $result['status'] = 200;
                }
            }
            return $result;
        };

        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call('getAmountOfExpiredTransactions', $params, '', 200, $positive, array(), 'Model::isProblem', array(), 'Query');
    }

    /**
     * Adds the component to a course
     *
     * Called when this component receives an HTTP POST request to
     * (/$name)/course.
     *
     * @param string $name A optional name for the attachment table.
     */
    public function addCourse( $callName, $input, $params = array() )
    {        
        $positive = function($input, $course) {
            return array("status"=>201,"content"=>$course);
        };
        return $this->_component->callSqlTemplate('addCourse',dirname(__FILE__).'/Sql/AddCourse.sql',array_merge($params,array('object' => $input)),201,$positive,array('course'=>$input),'Model::isProblem',array(new Course()),false);
    }

}

/**
 @see http://de2.php.net/manual/de/function.uniqid.php#88400
 */
class uuid {

    protected $urand;

    public function __construct() {
        $this->urand = @fopen ( '/dev/urandom', 'rb' );
    }

    /**
     * @brief Generates a Universally Unique IDentifier, version 4.
     *
     * This function generates a truly random UUID. The built in CakePHP String::uuid() function
     * is not cryptographically secure. You should uses this function instead.
     *
     * @see http://tools.ietf.org/html/rfc4122#section-4.4
     * @see http://en.wikipedia.org/wiki/UUID
     * @return string A UUID, made up of 32 hex digits and 4 hyphens.
     */
    function get() {

        $pr_bits = false;
        if (is_a ( $this, 'uuid' )) {
            if (is_resource ( $this->urand )) {
                $pr_bits .= @fread ( $this->urand, 16 );
            }
        }
        if (! $pr_bits) {
            $fp = @fopen ( '/dev/urandom', 'rb' );
            if ($fp !== false) {
                $pr_bits .= @fread ( $fp, 16 );
                @fclose ( $fp );
            } else {
                // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
                $pr_bits = "";
                for($cnt = 0; $cnt < 16; $cnt ++) {
                    $pr_bits .= chr ( mt_rand ( 0, 255 ) );
                }
            }
        }
        $time_low = bin2hex ( substr ( $pr_bits, 0, 4 ) );
        $time_mid = bin2hex ( substr ( $pr_bits, 4, 2 ) );
        $time_hi_and_version = bin2hex ( substr ( $pr_bits, 6, 2 ) );
        $clock_seq_hi_and_reserved = bin2hex ( substr ( $pr_bits, 8, 2 ) );
        $node = bin2hex ( substr ( $pr_bits, 10, 6 ) );

        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec ( $time_hi_and_version );
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec ( $clock_seq_hi_and_reserved );
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

        return sprintf ( '%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node );
    }

}