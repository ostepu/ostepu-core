<?php
/**
 * @file LMusterkomponente.php Contains the LMusterkomponente class
 *
 * @author Max Muster
 */

require '../../Assistants/Slim/Slim.php';
include '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LMusterkomponente-Component
 */
class LMusterkomponente
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "musterkomponente";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LMusterkomponente::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LMusterkomponente::$_prefix = $value;
    }

    /**
     * @var string $lURL the URL of the logic-controller
     */
    private $lURL = ""; // readed out from config below

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct($conf)
    {
        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = CConfig::getLink($conf->getLinks(),"controller");

        // initialize lURL
        $this->lURL = $this->query->getAddress();

        /* a Pattern:
         *
         * // METHOD functionWhichShouldBeExecuted
         * $this->app->method('/'.$this->getPrefix().'/URL', array($this, 'functionWhichShouldBeExecuted'));
         *
         */


        // PUT functionToPut
        $this->app->put('/'.$this->getPrefix().'/muster/:id', array($this, 'functionToPut'));

        // run Slim
        $this->app->run();
    }

    /**
     * Short description.
     *
     * Long description. (otpional)
     *
     * @param int $id Description of the parameter.
     */
    public function functionToPut($id){
        // commands of the function
        print("Hello User");
    }

}


// get new config data from DB
$com = new CConfig(LMusterkomponente::getPrefix());

// create a new instance of LUser class with the config data
if (!$com->used())
    new LMusterkomponente($com->loadConfig());
?>