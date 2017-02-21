<?php

include_once dirname(__FILE__) . '/Helpers.php';
include_once dirname(__FILE__) . '/AbstractAuthentication.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';
if (file_exists(dirname(__FILE__) . '/Config.php'))
    include_once dirname(__FILE__) . '/Config.php';
include_once dirname(__FILE__) . '/../../Assistants/Logger.php';

/**
 * LDAPAuthentication class.
 *
 * Class for LDAP Loginsystem.
 */
class LDAPAuthentication extends AbstractAuthentication {

    private $server = null;
    private $base = null;
    private $admin = null;
    private $adminPasswd = null;
    private $filter = null;
    private $dn = null;
    private $username = null;
    private $password = null;

    /**
     * The default constructor
     */
    public function __construct() {
        global $ldapServer;
        global $ldapBase;
        global $ldapAdmin;
        global $ldapPasswd;
        global $ldapFilter;
        global $tempPath;

        $this->server = $ldapServer;
        $this->base = $ldapBase;
        $this->admin = $ldapAdmin;
        $this->adminPasswd = $ldapPasswd;
        $this->filter = $ldapFilter;

        // hier wird das Cache-System initialisiert
        phpFastCache::$config['path'] = $tempPath;
        phpFastCache::$config['securityKey'] = "ldapCache";
    }

    /**
     * Create User in DB
     *
     * @param User $data UserData which contains the created User
     * @return true if user is created
     */
    private function createUser($data) {
        global $databaseURI;
        $data = User::encodeUser($data);

        $url = "{$databaseURI}/user";
        $message = null;
        $answer = http_post_data($url, $data, false, $message);
        if ($message == '201') {
            $user = User::decodeUser($answer);
            if ($user->getStatus() == '201') {
                return $user;
            }
        }
        return false;
    }

    private $connection = null;

    /**
     * prüft, ob wir verbunden sind
     * @return boolean false = nein, true = ja
     */
    private function isConnected() {
        // Either false or null means "disconnected"
        if (!$this->connection) {
            return false;
        }
        return true;
    }

    /**
     * stellt eine Verbindung zum LDAP-Server her
     * @return boolean true = Verbindung besteht, false = Fehler
     */
    private function connect() {
        if ($this->isConnected()) {
            return true;
        }

        //ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);

        $this->connection = @ldap_connect($this->server);
        if (!$this->connection) {
            Logger::Log('der LDAP-Server ist ungültig',LogLevel::ERROR);
            return false;
        }

        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);

        // This actually creates the connection to the LDAP server
        if (!@ldap_bind($this->connection, $this->admin, $this->adminPasswd)) {
            $this->disconnect();
            Logger::Log('der LDAP-Admin konnte sich nicht anmelden',LogLevel::ERROR);
            return false;
        }

        return true;
    }

    /**
     * trennt eine Verbindung
     * @return boolean true = Trennung erfolgreich, false = Fehler
     */
    private function disconnect() {
        if (!$this->isConnected()) {
            $this->connection = null;
            return true;
        }

        if (!ldap_unbind($this->connection)) {
            $this->connection = null;
            return false;
        }

        $this->connection = null;
        return true;
    }

    /**
     * liefert die DN des Nutzers
     * @return mixed false = Fehler, sonst = die dn
     */
    private function getDnForUser() {
        if (!$this->connect()) {
            return false;
        }
        
        if ($this->dn){
            return $this->dn;
        }

        $filter2 = str_replace('$username', $this->username, $this->filter);
        $results = $this->search($this->base, $filter2, array('dn'));

        if (!$results or ! isset($results['count']) or $results['count'] === 0) {
            return false;
        }
        if ($results['count'] > 1) {
            // es gibt mehrere Treffer (das ist problematisch)
            Logger::Log('es gab mehrere LDAP-Treffer zu diesem Filter',LogLevel::ERROR);
            return false;
        }
        return $results[0]['dn'];
    }

    /**
     * sucht in einer Verbindung anhad der basedn und des Filters
     * @param  String $basedn              die basis dn
     * @param  String $filter              der Suchfilter
     * @param  String[] [$attributes = null] die Attribute, welche gesucht werden sollen (als Liste)
     * @return mixed false = Fehler, sonst = die Resultate
     */
    private function search($basedn, $filter, $attributes = null) {
        if (!$this->connect()) {
            return false;
        }

        // Not sure at the moment how ldap_search() would react if $attributes
        // were empty or null, so...
        if ($attributes === null) {
            $search = ldap_search($this->connection, $basedn, $filter);
        } else {
            $search = ldap_search(
                    $this->connection, $basedn, $filter, $attributes
            );
        }
        if (!$search) {
            return false;
        }
        return ldap_get_entries($this->connection, $search);
    }

    /**
     * prüft, ob Nutzername und Passwort als korrekte Anmeldung dienen
     * @return boolean true = Anmeldung war korrekt, false = Fehler oder falsches Passwort
     */
    private function checkAuthWithLdap() {
        $keyword = "ldap_" . sha1($this->username . '_' . $this->password); // hier soll die Dn zwischengespeicher werden
        $cache = phpFastCache();
        $cachedDn = $cache->get($keyword);
        if ($cachedDn !== null) {
            $this->dn = $cachedDn;
            return true; // wenn die DN schonmal gespeichert wurde, dann ist die Anmeldung korrekt
        }

        if (!$this->connect()) {
            return false;
        }

        if (!$this->dn) {
            $this->dn = $this->getDnForUser();
            if (!$this->dn) {
                return false;
            }
        }

        // es wurde eine dn gefunden und die Anmeldung muss nun noch geprüft wernde
        if (!@$this->bindUser()) {
            return false;
        }

        // wenn die DN gefunden wurde, dann wird sie zwischengespeichert (24h)
        $cache->set($keyword, $this->dn, 86400, array('skipExisting' => true));

        return true;
    }

    /**
     * liefert die Daten des Nutzers im LDAP
     * @return mixed false = Fehler, sonst = ein Nutzerobjekt
     */
    private function getUser() {
        if (!$this->connect()) {
            return false;
        }

        if (!$this->dn) {
            return false;
        }

        // Get back everything that LDAP will give us
        $results = ldap_read($this->connection, $this->dn, "(objectclass=*)");

        if (!$results) {
             Logger::Log('die Nutzerdaten konnten nicht gelesen werden',LogLevel::ERROR);
            return false;
        }

        $results = ldap_get_entries($this->connection, $results);

        if (!$results) {
            Logger::Log('es konnten keine Daten des Eintrages ermittelt werden',LogLevel::ERROR);
            return false;
        }

        $user = User::createUser(NULL, $this->username, $results[0]['mail'][0], $results[0]['givenname'][0], $results[0]['sn'][0], NULL, "1", "noPassword", "noSalt", "0");

        return $user;
    }

    /**
     * @brief  Open a new LDAP connection and attempt to bind with given DN and password
     * @return              boolean true indicating successful LDAP bind
     */
    private function bindUser() {
        // Make a new connection; don't mess with $this->connection
        $conn = @ldap_connect($this->server);

        if (!$conn) {
            Logger::Log('der LDAP-Server ist ungültig',LogLevel::ERROR);
            return false;
        }

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (!@ldap_bind($conn, $this->dn, $this->password)) {
            ldap_unbind($conn);
            return false;
        }

        if (!ldap_unbind($conn)) {
            return false;
        }

        return true;
    }

    /**
     * Logs in a user.
     *
     * @param string $username
     * @param string $password
     * @return true if login is successful
     */
    public function loginUser($username, $password) {
        // prüfe den Wartungsmodus
        global $maintenanceMode;
        global $maintenanceText;
        global $maintenanceAllowedUsers;
        if ($maintenanceMode === '1' && !in_array($username, explode(',', str_replace(' ', '', $maintenanceAllowedUsers)))) {
            $text = $maintenanceText;
            if (trim($maintenanceText) == '')
                $text = "Wartungsarbeiten!!!";
            set_error("error", $text);
            exit();
        }

        global $databaseURI;
        global $logicURI;
        global $serverURI;

        // prüfe zunächst, ob dieser Nutzer so auch im LDAP existiert
        $this->username = $username;
        $this->password = $password;
        $ldapLogin = $this->checkAuthWithLdap();

        if ($ldapLogin === true) {
            // wenn der Nutzer existiert, prüfe ob er bei uns existiert
            $url = "{$databaseURI}/user/user/{$this->username}";
            $message = null;
            $this->userData = http_get($url, false, $message);
            $this->userData = json_decode($this->userData, true);

            if ($message != "404" && empty($this->userData) === false) {
                // save logged in uid
                $_SESSION['UID'] = $this->userData['id'];

                // refresh Session in UI and DB
                $this->disconnect();
                return $this->refreshSession();
            } else {
                // wenn er bei uns fehlt, dann wird er bei uns angelegt
                $newUser = $this->getUser();
                // if user is a valid user
                if ($newUser !== false) {
                    $response = $this->createUser($newUser);

                    // if successful try to login new user
                    if ($response !== false) {
                        $_SESSION['UID'] = $response->getId();
                        $this->disconnect();
                        return $this->refreshSession();
                    }
                }

                // der Nutzer konnte nicht angelegt werden
                $this->disconnect();
                return false;
            }
        } else {
            // der Nutzer kann so nicht im LDAP angemeldet werden
        }

        $this->disconnect();
        return false;
    }

}
