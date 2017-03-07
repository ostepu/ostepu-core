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
    public function __construct( )
    {
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
        
        file_put_contents("aa.txt",json_encode($params));
        
        $projectId = $data['project_id'];
        $checkoutSha = $data['checkout_sha'];
        $private_token = '???';
        
        $url = 'https://gitlab.informatik.uni-halle.de/api/v3/projects/'.$projectId.'/repository/archive?private_token='.$private_token.'&sha='.$checkoutSha;
        $res = Request::get($url, array(),  '');
        
        if ($res['status'] == 200 && isset($res['content'])){
            $content = $res['content']; // das ist bereits die Datei
            
        } else {
            
        }
    }

}
