<?php
include_once ( dirname(__FILE__) . '/../Assistants/Model.php' );

/**
 * 
 */
class LGitLab
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
    private $config = array();
    public function __construct( )
    {        
        if (file_exists(dirname(__FILE__).'/config.ini')){
            $this->config = parse_ini_file(
                                           dirname(__FILE__).'/config.ini',
                                           TRUE
                                           );
        }
        
        $component = new Model('', dirname(__FILE__), $this, false, false, array('cloneable'=>true,
                                                                                 'addProfileToParametersAsPostfix'=>true,
                                                                                 'addRequestToParams'=>true));
        $this->_component=$component;
        $component->run();
    }

    public function submit( $callName, $input, $params = array() )
    {
        $data = json_decode($input, true);
        
        
        if (!isset($data['event_name']) || $data['event_name'] !== 'tag_push'){
            return Model::isProblem("falscher Ereignistyp!");
        }
        
        if (!isset($data['project_id'])){
            return Model::isError("Projektnummer fehlt!");
        }
        
        if (!isset($data['checkout_sha'])){
            return Model::isError("Commit-Zusammenhang fehlt!");
        }
        
        if (!isset($data['ref'])){
            return Model::isError("Tag-Daten fehlen!");
        }
        
        if (!isset($params['courseid'])){
            return Model::isError("die Veranstaltungsnummer fehlt!");            
        }
        
        file_put_contents("aa.txt",json_encode($params));
        
        $courseId = $params['courseid'];
        $user = null;
        
        // "ref":"refs/tags/SHEETID_EXERCISEID",
        $tag = $data['ref'];
        $tagRaw = explode("/",$tag);
        end($tagRaw);
        $tagRaw = current($tagRaw); // SHEETID_EXERCISEID
        $tagRaw = explode("_",$tagRaw); // [SHEETID, EXERCISEID]
        
        if (count($tagRaw)!=2){
            return Model::isError("der Tagname ist ungültig");            
        }
        
        $sheetId = $tagRaw[0];
        $exerciseId = $tagRaw[1];
        $projectId = $data['project_id'];
        $checkoutSha = $data['checkout_sha'];
        
        // Konfiguration: URL + private_token
        
        $url = $this->config['GITLAB']['gitLabUrl'].'/api/v3/projects/'.$projectId.'/repository/archive?private_token='.$this->config['GITLAB']['private_token'].'&sha='.$checkoutSha;
        $res = Request::get($url, array(),  '');
        
        if ($res['status'] == 200 && isset($res['content'])){
            $content = $res['content']; // das ist bereits die Datei
            
        } else {
            
        }
    }
    
    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts GET GetExistsPlatform',
                    LogLevel::DEBUG
                    );
                   
        if (!file_exists(dirname(__FILE__).'/config.ini')){
            return Model::isProblem();
        }
      
        return Model::isOk();
    }
   
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts DELETE DeletePlatform',
                    LogLevel::DEBUG
                    );
        if (file_exists(dirname(__FILE__).'/config.ini') && !unlink(dirname(__FILE__).'/config.ini')){
            return Model::isProblem();
        }
       
        return Model::isCreated();
    }
   
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts POST AddPlatform',
                    LogLevel::DEBUG
                    );
       
        $file = dirname(__FILE__).'/config.ini';
        $text = "[DIR]\n".
                "temp = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$input->getTempDirectory()))."\"\n";
                
        $settings = $input->getSettings();
        $text .= "[GITLAB]\n".
                "gitLabUrl = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$settings->LGitLab_gitLabUrl))."\"\n".
                "private_token = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$settings->LGitLab_private_token))."\"\n";
                
        if (!@file_put_contents($file,$text)){
            Logger::Log(
                        'POST AddPlatform failed, config.ini no access',
                        LogLevel::ERROR
                        );

            return Model::isProblem();
        }  

        $platform = new Platform();
        $platform->setStatus(201);
       
        return Model::isCreated($platform);
    }

}
