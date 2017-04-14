<?php

include_once ( dirname(__FILE__) . '/Logger.php' );
include_once ( dirname(__FILE__) . '/Model.php' );

class pageLib
{
    public static function getContent($linkName, &$model, $confFile){
        if (!file_exists($confFile)){
            return '';
        }

        $component = new Model('', dirname($confFile), null);   
        $component->_conf=CConfig::loadStaticConfig('','',dirname($confFile),basename($confFile));
        $component->_com=new CConfig('');

        // nun sollen die weiteren Inhalte gesammelt werden
        $res = array();
        
        $positive2 = function($input, &$res, &$model) {
            $input = json_decode($input,true);
            $res[] = $input['content'];
            if (isset($input['model'])){
                $model = array_merge($model, $input['model']);
            }
        };
        
        $negative2 = function(&$model, $linkName) {
            // einer der getContent Aufrufe hat nicht funktioniert
            // -> ignorieren
            Logger::Log( 
                'error on calling link: '.$linkName,
                LogLevel::ERROR
                        );
        };

        // ruft alle Views auf
        $component->callAll('getContent', array(), json_encode($model), 200, $positive2, array('res'=>&$res, 'model'=>&$model), $negative2, array('model'=>&$model, 'linkName'=>$linkName));

        return implode('',$res);
    }
}