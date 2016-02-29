<?php
/**
 * @file index.php
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