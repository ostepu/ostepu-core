<?php
/**
 * @file TEST_LFile.php Contains the TEST_LFile class
 * 
 * @author Till Uhlig
 * @date 2014
 */

require_once ( dirname( __FILE__ ) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname( __FILE__ ) . '/../../Assistants/CConfig.php' );
include_once ( dirname( __FILE__ ) . '/../../Assistants/Request.php' );
include_once ( dirname( __FILE__ ) . '/../../Assistants/Structures.php' );
include_once ( dirname( __FILE__ ) . '/../../Assistants/Logger.php' );
include_once ( dirname( __FILE__ ) . '/../../Assistants/Test/Testsystem.php' );
include_once ( 'PHPUnit/Autoload.php' );
//var_dump($_SERVER);
\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the TEST_LFile-Component
 */
class LFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Slim $_app the slim object
     */
    private $app = null;
    public static $data = null;
    private static $Tester = null;
    
    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct()
    {
        // initialize slim                
        if (!isset($_SERVER['REQUEST_METHOD'])){

        } else {   
            $this->app = new \Slim\Slim(array('debug' => true));
            $this->app->response->headers->set('Content-Type', 'application/json');
            
            $this->app->map('/test',
                            array($this, 'testLFile'))->via('POST');
                            
            $this->app->map('/fileDb/:path+/hash/:hash',
                            array($this, 'fileDbGet'))->via('GET');
                            
            $this->app->map('/fileDb/file',
                            array($this, 'fileDbPost'))->via('POST');
                            
            $this->app->map('/file/file',
                            array($this, 'fileFsPost'))->via('POST');

            // run Slim
            $this->app->run();
        }
    }
  
    public static function Init($data)
    {
        $component = new Component();
        $links = array();
        $link = new Link();
        $link->setName('fileDb');
        $link->setAddress('http://localhost/'.$data['path'].'/fileDb');
        $link->setPrefix('file');        
        $links[] = $link;
        
        $link = new Link();
        $link->setName('file');
        $link->setAddress('http://localhost/'.$data['path'].'/file');
        $link->setPrefix('file');        
        $links[] = $link;
        
        $component->setLinks($links);
        chdir(dirname(__FILE__));
        CConfig::saveConfigGlobal('',Component::encodeComponent($component));

    }
    
    private static $ass = null;
    public static function createTests()
    {
        LFileTest::$Tester = new Testsystem();
        
        $aufruf = function($methode, $url, $data){
            return Request::custom($methode, 'http://localhost/uebungsplattform/logic/LFile'.$url,array(),$data);
        };
        
        $auswertungOK = function($result, $methode, $url, $data, $fileDbGet, $fileDbPost, $fileFsPost){
            PHPUnit_Framework_Assert::assertTrue(isset($result['content']));
            PHPUnit_Framework_Assert::assertTrue(isset($result['status']));
            PHPUnit_Framework_Assert::assertEquals(201,$result['status'],$data);
            $objData = File::decodeFile($data);
            
            $obj = File::decodeFile($result['content']);
            PHPUnit_Framework_Assert::assertEquals(is_array($objData),is_array($obj));
            
            if (!is_array($obj)) $obj = array($obj);
            if (!is_array($objData)) $objData = array($objData);
            foreach($obj as $res){
                PHPUnit_Framework_Assert::assertEquals(null,$res->getBody());
                PHPUnit_Framework_Assert::assertEquals(201,$res->getStatus(),$data);
                
                // prüfe Inhalt der Objekte
            }
        };
        
        $auswertungWrongSyntax = function($result, $methode, $url, $data, $fileDbGet, $fileDbPost, $fileFsPost){
            PHPUnit_Framework_Assert::assertTrue(isset($result['content']));
            PHPUnit_Framework_Assert::assertTrue(isset($result['status']));
            PHPUnit_Framework_Assert::assertEquals(201,$result['status'],$data);
            $objData = File::decodeFile($data);
            
            $obj = File::decodeFile($result['content']);
            PHPUnit_Framework_Assert::assertEquals(is_array($objData),is_array($obj));
            
            if (!is_array($obj)) $obj = array($obj);
            if (!is_array($objData)) $objData = array($objData);
            foreach($obj as $res){
                PHPUnit_Framework_Assert::assertEquals(null,$res->getBody());
                PHPUnit_Framework_Assert::assertEquals(409,$res->getStatus(),$data);
            }
            
        };
        
        $filesOK = array('{"fileId":"1_?aÖ","body":"f4","address":"a","hash":"hh"}',
                         '{"body":"f4"}',
                         '[{"fileId":"1_?aÖ","body":"f4","address":"a","hash":"hh"}]');
                       
        $filesWrongSyntax = array('{"fileId":"1_?aÖ","body":"f4","address"a}',
                                  '[{"fileId":"1_?aÖ","body":"f4","address":"a","hash":"h]',
                                  '{"fileId":"1_?aÖ",}',
                                  '{"address":"a"}',
                                  '{"hash":"hh"}',
                                  '{"displayName":"hh"}',
                                  '',
                                  '[]',
                                  '{}',
                                  null,
                                  array());
        
                                     
        LFileTest::$Tester->situationHinzufuegen(new Situation('Funktioniert',
                                                        $aufruf,
                                                        $auswertungOK,
                                                        new Sammlung('Methoden','POST'),
                                                        new Sammlung('URL','/file'),
                                                        new Sammlung('Data',$filesOK),
                                                        new Sammlung('fileDbGet','{"fileId":"1_?aÖ","address":"a","hash":"hh"}'),
                                                        new Sammlung('fileDbPost','{"fileId":"1_?aÖ"}'),
                                                        new Sammlung('fileFsPost','{"address":"a","hash":"hh"}')
                                                               )
                                                );
                                                
        LFileTest::$Tester->situationHinzufuegen(new Situation('SyntaxFehlerhaft',
                                                        $aufruf,
                                                        $auswertungWrongSyntax,
                                                        new Sammlung('Methoden','POST'),
                                                        new Sammlung('URL','/file'),
                                                        new Sammlung('Data',$filesWrongSyntax),
                                                        new Sammlung('fileDbGet',null),
                                                        new Sammlung('fileDbPost',null),
                                                        new Sammlung('fileFsPost',null)
                                                               )
                                                );
    }

    public function fileDbGet($path, $hash)
    {
        LFileTest::createTests();
        $this->app->response->setBody(LFileTest::$Tester->gibSituationsdaten('fileDbGet'));
        $this->app->response->setStatus( 201 );
        $this->app->stop();
    }
    
    public function fileDbPost()
    {
        LFileTest::createTests();
        $this->app->response->setBody(LFileTest::$Tester->gibSituationsdaten('fileDbPost'));
        $this->app->response->setStatus( 201 );
        $this->app->stop();
    }
    
    public function fileFsPost()
    {
        LFileTest::createTests();
        $this->app->response->setBody(LFileTest::$Tester->gibSituationsdaten('fileFsPost'));
        $this->app->response->setStatus( 201 );
        $this->app->stop();
    }
    
    public function testLFile()
    {
        $data = LFileTest::$data;
        $data['path'] = 'uebungsplattform/logic/LFile/LFileTest.php';       
        
        // Testsituation initialisieren
        LFileTest::Init($data);
        LFileTest::createTests();
        LFileTest::$Tester->ausfuehren();
    }
}

$com = new LFileTest();
?>