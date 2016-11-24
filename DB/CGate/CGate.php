<?php
/**
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.6.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * ???
 */
class CGate extends Model
{

    /**
     * ???
     */
    public function __construct( )
    {
        parent::__construct('', dirname(__FILE__), $this, false, false, array('addRequestToParams'=>true));
        $this->run();
    }

    /**
     * ???
     */
    public function request( $callName, $input, $params = array() )
    {
        $authType = 'noAuth'; // er versteht nur public Zugänge
        
        $profile = $params['profile'];
        $component = $params['component'];
        $order = '/'.implode('/',$params['path']);
        $method = $params['request']['method']; // die Methode füllt hier Model.php hinein
        
        $positive = function($gateProfile, $method, $order, $component, $body) {
            $gateProfile = $gateProfile[0];
            $rules = $gateProfile->getRules();
            
            // nun muss geprüft werden, ob der Aufruf auch erlaubt ist
            if (in_array("Slim\\Slim", get_declared_classes())) {
                $router = new \Slim\Router();
                foreach($rules as $rule){
                    if ($rule->getType() == 'httpCall' && $rule->getComponent() == $component){
                        
                        // jetzt wird der Eintrag zerlegt in METHOD PATH
                        $path = explode(' ',$rule->getContent());
                        $callMethod = $path[0];
                        $path = $path[1];
                        
                        $route = new \Slim\Route($path, 'is_array'); // is_array wird hier benötigt, weil Route eine Funktion die er auf callable prüfen kann
                        $route->via($callMethod);
                        $router->map($route);
                    } else {
                        // die Regel gehört zu einer anderen Komponente oder kann nicht interpretiert werden
                    }
                }
                
                // der Router ist nun initialisiert und kann aufgerufen werden, damit er
                // uns sagt, ob es eine passende URL gibt
                $routes = count($router->getMatchedRoutes(strtoupper($method), $order), true);
                if ($routes === 0) {
                    // für diese Komponente ist der Aufruf nicht erlaubt
                    return Model::isRejected(); 
                }
                
                // es konnte ein passender Aufruf gefunden werden, also weiter ...
                
            } else {
                // die notwendige Klasse existiert nicht
                return Model::isRejected();
            }
            
            // der Befehl darf also ausgeführt werden, sodass wir nun noch den passenden Ausgang benötigen
            
            // wir gehen nun alle Links durch und suchen den passenden Ausgang
            $links = Model::getLinks('request');
            foreach($links as $link){
                
                // ist es die passende Komponente?
                if ($link->getTargetName() == $component){
                    // jetzt wird der Befehl aufgerufen
                    return $result = Request::routeRequest(
                                                            $method,
                                                            $order,
                                                            array(),
                                                            $body,
                                                            $link
                                                            );
                }
            }
            
            return Model::isRejected();
        };
        
        // ermittle nun, ob mit dem gegebenen authType, der Zugriff auf die Komponenten erlaubt ist
        if ($authType == 'noAuth'){
            return Model::call('getComponentProfileWithAuth', array('profName'=>$profile, 'authType'=>$authType, 'component'=>$component), '', 200, $positive, array('method'=>$method, 'order'=>$order, 'component'=>$component, 'body'=>$input), 'Model::isError', array(), 'GateProfile');
        } else {
            return Model::call('getComponentProfileWithAuthLogin', array('login'=>'???', 'profName'=>$profile, 'authType'=>$authType, 'component'=>$component), '', 200, $positive, array('method'=>$method, 'order'=>$order, 'component'=>$component, 'body'=>$input), 'Model::isError', array(), 'GateProfile');
        }
    }
}
