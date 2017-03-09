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
include_once ( dirname(__FILE__) . '/../../UI/include/Authentication.php' );

/**
 * die Klasse CGate stellt gesicherte Zugriffe auf interne Komponenten bereit
 * hierzu gehört die Komponente DBGate, deren Tabellen die Profile, Benutzer und Regeln definieren
 *
 * Es gibt die Zugangsarten
 * -> noAuth = keine Authentifizierung
 * -> httpAuth = die HTTP-Standardanmeldung im Header unter "Authorization"
 *      siehe https://de.wikipedia.org/wiki/HTTP-Authentifizierung
 * -> tokenAuth = die Angabe eines Tokens im Header unter "PRIVATE-TOKEN" (ist in der gateauth-Tabelle dann der "GA_login")
 */
class CGate extends Model
{

    /**
     * Konstruktor
     */
    public function __construct( )
    {
        // hier wird die Model-Klasse gestartet
        parent::__construct('', dirname(__FILE__), $this, false, false, array('addRequestToParams'=>true));
        $this->run();
    }

    /**
     * diese Methode bearbeitet die Anfragen
     */
    public function request( $callName, $input, $params = array() )
    {
        $authType = 'noAuth'; // das ist der default-Anmeldetyp, wenn kein anderer erkannt wird     
        $profile = $params['profile'];
        $component = $params['component'];
        $order = '/'.implode('/',$params['path']);
        $method = $params['request']['method']; // die Methode füllt hier Model.php hinein
        $headers = $params['request']['headers'];
        $login = '';
        $passwd = '';

        // ermittle nun den korrekten Zugang
        if (isset($headers['PHP_AUTH_USER'])){
            // wir prüfen nun eine HTTP-Authentifizierung
            $login = $headers['PHP_AUTH_USER'];
            $passwd = (isset($headers['PHP_AUTH_PW']) ? $headers['PHP_AUTH_PW'] : '');
            $authType = 'httpAuth';
        } elseif (isset($headers['HTTP_PRIVATE_TOKEN'])){
            // wir prüfen nun ob eine Authentifizierung über einen Token gewollt ist
            $login = $headers['HTTP_PRIVATE_TOKEN'];
            $passwd = null;
            $authType = 'tokenAuth';
        }
        
        $positive = function($gateProfile, $method, $order, $component, $body, $authType, $login, $passwd) {
            // wenn diese Funktion aufgerufen wird, dann existiert das Profil und dazu der Login.
            // Wir wissen aber noch nicht, ob er sich wirklich korrekt angemeldet hat, sofern noch ein Passwort
            // erforderlich ist
            
            $gateProfile = $gateProfile[0]; // dieses Profil entstammt der Datenbank, wenn dort Profil und Nutzername gefunden wurden
            
            $auths = $gateProfile->getAuths();
            $accepted = false; // dieses Flag gibt an, ob der Zugang erfolgreich gewährt wurde
            
            foreach($auths as $auth){
                $authType = $auth->getType();
                if ($authType == 'tokenAuth'){
                    // wir gelangen nur an diesen Punkt, wenn es den entsprechenden Token in der Datenbank als
                    // 'login' gibt, daher ist der Zugang dann erlaubt
                    $accepted = true;
                    break;
                } elseif ($authType == 'noAuth'){
                    // es ist keine weitere Prüfung erforderlich
                    $accepted = true;
                    break;
                } elseif ($authType == 'httpAuth'){
                    //$params = $auth->getParams();
            
                    if ($auth->getLogin() == $login && $auth->getPasswd() == $passwd){
                        $accepted = true;
                        break;
                    } else {
                        // falsches Passwort
                    }
                } else {
                    // diese Methode ist unbekannt
                }
            }
            
            if (!$accepted){
                // es wurde kein gültiger Login gefunden
                return Model::isRejected();
            } else {
                //der Zugang ist erlaubt
            }
            
            $rules = $gateProfile->getRules();

            // nun muss geprüft werden, ob der Aufruf auch erlaubt ist
            if (in_array("Slim\\Slim", get_declared_classes())) {
                
                // hier wird slim vorbereitet, um später für uns zu prüfen, ob eine der Regelpfade auf
                // unsere Anfrage passt
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
            return Model::call('getComponentProfileWithAuth', array('profName'=>$profile, 'authType'=>$authType, 'component'=>$component), '', 200, $positive, array('method'=>$method, 'order'=>$order, 'component'=>$component, 'body'=>$input, 'authType'=>$authType, 'login'=>$login, 'passwd'=>$passwd), 'Model::isRejected', array(), 'GateProfile');
        } else {
            // es ist eine andere Zugangsart, sodass auch geprüft wird, ob es eine Anmeldung mit dem entsprechenden Nutzernamen gibt
            return Model::call('getComponentProfileWithAuthLogin', array('login'=>$login, 'profName'=>$profile, 'authType'=>$authType, 'component'=>$component), '', 200, $positive, array('method'=>$method, 'order'=>$order, 'component'=>$component, 'body'=>$input, 'authType'=>$authType, 'login'=>$login, 'passwd'=>$passwd), 'Model::isRejected', array(), 'GateProfile');
        }
    }
}
