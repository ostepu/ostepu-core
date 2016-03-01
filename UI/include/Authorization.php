<?php
/**
 * @file Authorization.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2014
 */

include_once dirname(__FILE__) . '/Authentication.php';
include_once dirname(__FILE__) . '/StudIPAuthentication.php';

$auth = new Authentication();
$StudIPauth = new StudIPAuthentication();

$invalidLogin = Authentication::checkLogin() == false;
$shouldLogOut = isset($_GET['action']) && $_GET['action'] == "logout";

if ($invalidLogin == true || $shouldLogOut == true) {
    // the user's login is no longer valid or he requested to be logged out
    Authentication::logoutUser();
}
