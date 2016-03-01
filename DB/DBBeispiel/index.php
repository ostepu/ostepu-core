<?php
/**
 * @file index.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.4
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

require_once ( dirname( __FILE__ ) . '/DBBeispiel.php' );

Logger::Log(
            'begin DBBeispiel',
            LogLevel::DEBUG
            );

// ruft die Beispielkomponente auf
new DBBeispiel();

Logger::Log(
            'end DBBeispiel',
            LogLevel::DEBUG
            );