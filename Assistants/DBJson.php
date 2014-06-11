<?php 


/**
 * @file DBJson.php contains the DBJson class
 *
 * @author Till Uhlig
 * @date 2013-2014
 */

/**
 * the DBJson class is written for several tasks, not only json tasks
 */
class DBJson
{

    /**
     * masks control characters (like the mysql mysql_real_escape_string())
     *
     * @param string $inp the text to be masked
     *
     * @return the masked text
     */
    public static function mysql_real_escape_string( $inp )
    {
        if ( is_array( $inp ) )
            return array_map( 
                             __METHOD__,
                             $inp
                             );

        if ( !empty( $inp ) && 
             is_string( $inp ) ){
            return str_replace( 
                               array( 
                                     '\\',
                                     "\0",
                                     "\n",
                                     "\r",
                                     "'",
                                     '"',
                                     "\x1a"
                                     ),
                               array( 
                                     '\\\\',
                                     '\\0',
                                     '\\n',
                                     '\\r',
                                     "\\'",
                                     '\\"',
                                     '\\Z'
                                     ),
                               $inp
                               );
        }

        return $inp;
    }

    /**
     * The function checks whether an input list of arguments, has the correct data type
     * If not, the slim instance terminates with a 412 error code
     *
     * @param Slim $app a running slim instance
     */
    public static function checkInput( )
    {
        $args = func_get_args( );

        // the first argument ist the slim instance, remove from the test list
        $app =  & $args[0];
        $args = array_slice( 
                            $args,
                            1,
                            count( $args )
                            );

        foreach ( $args as & $a ){

            // search a argument, which is not true
            if ( !$a ){

                // one of the arguments isn't true, abort progress
                Logger::Log( 
                            'access denied',
                            LogLevel::ERROR
                            );
                $app->response->setBody( '[]' );
                $app->response->setStatus( 412 );
                $app->stop( );
                break;
            }
        }
    }

    /**
     * the function reads the passed mysql object and converts direktly into json
     *
     * @param mysql $object a mysql answer
     *
     * @return an array of json (string[])
     */
    public static function getJson( $object )
    {
        if ( !$object ){
            throw new Exception( 'Invalid query. Error: ' . mysql_error( ) );
        }

        $res = array( );
        while ( $row = mysql_fetch_assoc( $object ) ){
            $res[] = $row;
        }
        return json_encode( $res );
    }

    /**
     * the function reads the passed mysql object content
     *
     * @param mysql $data a mysql answer
     *
     * @return an array, which represents the received columns (string[][])
     */
    public static function getRows( $data )
    {
        $res = array( );

        while ( $row = mysql_fetch_assoc( $data ) ){
            $res[] = $row;
        }
        return $res;
    }
    
    public static function getRows2( $data )
    {
        $res = array( );

        while ( $row = mysqli_fetch_assoc( $data ) ){
            $res[] = $row;
        }
        return $res;
    }

    /**
     * extract an array of attributes from a data array, using a list of object attributes
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param $id the primary key/keys (string/string[])
     * @param $attributes the object attributes (string[])
     * @param $extension optional, a const postfix for the column names (string)
     *
     * @return an array of assoc arrays
     */
    public static function getObjectsByAttributes( 
                                                  $data,
                                                  $id,
                                                  $attributes,
                                                  $extension = ''
                                                  )
    {
        $res = array( );

        foreach ( $data as $row ){
            $key = '';
            if ( is_array( $id ) ){
                foreach ( $id as $di ){
                    if ( isset( $row[$di . $extension] ) ){
                        $key .= $row[$di . $extension] . ',';
                    }
                }
            } else {
                if ( isset( $row[$id . $extension] ) ){
                    $key = $row[$id . $extension];
                }
            }
            if ( $key == '' ){
                continue;
            }

            foreach ( $attributes as $attrib => $value ){
                if ( isset( $row[$attrib . $extension] ) ){
                    $res[$key][$value] = $row[$attrib . $extension];
                }
            }
        }

        return $res;
    }

    /**
     * extract an array of attributes from a data array, using a list of object attributes
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param $id the primary key/keys (string/string[])
     * @param $attributes the object attributes (string[])
     * @param $extension optional, a const postfix for the column names (string)
     *
     * @return an array of arrays
     */
    public static function getResultObjectsByAttributes( 
                                                        $data,
                                                        $id,
                                                        $attributes,
                                                        $extension = ''
                                                        )
    {
        $res = array( );
        foreach ( $data as $row ){
            $temp = NULL;
            foreach ( $attributes as $attrib => $value ){
                if ( isset( $row[$attrib . $extension] ) ){
                    $temp[$value] = $row[$attrib . $extension];
                }
            }
            $res[] = $temp;
        }

        return $res;
    }

    /**
     * generates insert data from input
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param string[] $attributes the object attributes (string[])
     * @param string $seperator the seperator, e.g. ','
     *
     * @return an array, with two entrys (columns and values), the elements are comma separated strings
     * e.g. columns = a,b,c and values = '1','2','3'
     */
    public static function getInsertDataFromInput( 
                                                  $data,
                                                  $attributes,
                                                  $seperator
                                                  )
    {
        $row = $data;

        $temp = array( );
        $t1 = '';
        $t2 = '';
        foreach ( $attributes as $attrib => $value ){
            if ( isset( $row[$value] ) && 
                 !is_array( $row[$value] ) && 
                 gettype( $row[$value] ) != 'object' ){
                $t1 .= $seperator . $attrib;
                $t2 .= $seperator . "'" . $row[$value] . "'";
            }
        }

        if ( $t1 != '' && 
             $t2 != '' ){
            $t1 = substr( 
                         $t1,
                         1
                         );
            $t2 = substr( 
                         $t2,
                         1
                         );
        }
        return array( 
                     'columns' => $t1,
                     'values' => $t2
                     );
    }

    /**
     * generates update data from input
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param string[] $attributes the object attributes (string[])
     * @param string $seperator the seperator, e.g. ','
     *
     * @return a string e.g. ",a=1, b=2, c=3"
     */
    public static function getUpdateDataFromInput( 
                                                  $data,
                                                  $attributes,
                                                  $seperator
                                                  )
    {
        $data = json_decode( 
                            $data,
                            true
                            );

        $temp = '';
        foreach ( $attributes as $attrib => $value ){
            if ( isset( $data[$value] ) && 
                 !is_array( $data[$value] ) ){
                $temp .= $seperator . $attrib . '=' . "'" . $data[$value] . "'";
            }
        }
        if ( $temp != '' ){
            $temp = substr( 
                           $temp,
                           1
                           );
        }

        return $temp;
    }

    /**
     * concatenates two arrays by using attribute lists, removes assoc indizes
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param string[][] $prim the structure of objects to which you want to append the new objects
     * @param string[]/string $primKey an array or string, which represents the primary key of the objects
     * @param string[] $primAttrib the defined attributes of the primary objects
     * @param string[][] $sec the structure with objects, you want to attach
     * @param string $secKey a primary key of the objects you want to attach
     * @param string $extension optional, a const postfix for the column names of the objects you want to attach (string)
     *
     * @return string[][], the concatenated lists
     */
    public static function concatResultObjectLists( 
                                                   $data,
                                                   $prim,
                                                   $primKey,
                                                   $primAttrib,
                                                   $sec,
                                                   $secKey,
                                                   $extension = ''
                                                   )
    {
        foreach ( $prim as & $row ){
            $row[$primAttrib] = array( );
        }

        foreach ( $data as $rw ){
            $key = '';
            if ( is_array( $primKey ) ){
                foreach ( $primKey as $di ){
                    if (isset($rw[$di]))
                        $key .= $rw[$di] . ',';
                }
            } else {
                if (isset($rw[$primKey]))
                    $key = $rw[$primKey];
            }

            $key2 = '';
            if ( is_array( $secKey ) ){
                foreach ( $secKey as $di ){
                    if ( isset( $rw[$di . $extension] ) ){
                        $key2 .= $rw[$di . $extension] . ',';
                    }
                }
            } else {
                if ( isset( $rw[$secKey . $extension] ) ){
                    $key2 = $rw[$secKey . $extension];
                }
            }

            if ( isset( $sec[$key2] ) ){
                $prim[$key][$primAttrib][$key2] = $sec[$key2];
            }
        }

        foreach ( $prim as & $row ){
            $row[$primAttrib] = array_values( $row[$primAttrib] );
        }

        $prim = array_values( $prim );

        return $prim;
    }

    /**
     * concatenates two arrays by using attribute lists, assoc indizes remain
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param string[][] $prim the structure of objects to which you want to append the new objects
     * @param string[]/string $primKey an array or string, which represents the primary key of the objects
     * @param string[] $primAttrib the defined attributes of the primary objects
     * @param string[][] $sec the structure with objects, you want to attach
     * @param string $secKey a primary key of the objects you want to attach
     * @param string $extension optional, a const postfix for the column names of the objects you want to attach (string)
     *
     * @return string[][], the concatenated lists
     */
    public static function concatObjectLists( 
                                             $data,
                                             $prim,
                                             $primKey,
                                             $primAttrib,
                                             $sec,
                                             $secKey,
                                             $extension = ''
                                             )
    {
        foreach ( $prim as & $row ){
            $row[$primAttrib] = array( );
        }

        foreach ( $data as $rw ){
            $key = '';
            if ( is_array( $primKey ) ){
                foreach ( $primKey as $di ){
                    if (isset($rw[$di]))
                        $key .= $rw[$di] . ',';
                }
            } else {
                if (isset($rw[$primKey]))
                    $key = $rw[$primKey];
            }

            if ( isset( $sec[$rw[$secKey . $extension]] ) ){
                $prim[$key][$primAttrib][$rw[$secKey . $extension]] = $sec[$rw[$secKey . $extension]];
            }
        }

        return $prim;
    }

    /**
     * concatenates two arrays by using attribute lists, assoc indizes remain,
     * only one secondary object will attached to an primary object
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param string[][] $prim the structure of objects to which you want to append the new objects
     * @param string[]/string $primKey an array or string, which represents the primary key of the objects
     * @param string[] $primAttrib the defined attributes of the primary objects
     * @param string[][] $sec the structure with objects, you want to attach
     * @param string $secKey a primary key of the objects you want to attach
     * @param string $extension optional, a const postfix for the column names of the objects you want to attach (string)
     *
     * @return string[][], the concatenated lists
     */
    public static function concatObjectListResult( 
                                                  $data,
                                                  $prim,
                                                  $primKey,
                                                  $primAttrib,
                                                  $sec,
                                                  $secKey,
                                                  $extension = ''
                                                  )
    {
        foreach ( $prim as & $row ){
            $row[$primAttrib] = array( );
        }

        foreach ( $data as $rw ){
            $key = '';
            if ( is_array( $primKey ) ){
                foreach ( $primKey as $di ){
                    if (isset($rw[$di]))
                        $key .= $rw[$di] . ',';
                }
            } else {
                if (isset($rw[$primKey]))
                    $key = $rw[$primKey];
            }

            if ( isset( $rw[$secKey . $extension] ) && 
                 isset( $sec[$rw[$secKey . $extension]] ) ){
                $prim[$key][$primAttrib][$rw[$secKey . $extension]] = $sec[$rw[$secKey . $extension]];
            }
        }

        foreach ( $prim as & $row ){
            $row[$primAttrib] = array_values( $row[$primAttrib] );
        }

        return $prim;
    }

    /**
     * concatenates two arrays by using attribute lists, removes assoc indizes,
     * only one secondary object will attached to an primary object
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param string[][] $prim the structure of objects to which you want to append the new objects
     * @param string[]/string $primKey an array or string, which represents the primary key of the objects
     * @param string[] $primAttrib the defined attributes of the primary objects
     * @param string[][] $sec the structure with objects, you want to attach
     * @param string $secKey a primary key of the objects you want to attach
     * @param string $secextension optional, a const postfix for the column names of the objects you want to attach (string)
     * @param string $primextension optional, a const postfix for the column names of the objects where you want to attach (string)
     *
     * @return string[][], the concatenated lists
     */
    public static function concatObjectListsSingleResult( 
                                                         $data,
                                                         $prim,
                                                         $primKey,
                                                         $primAttrib,
                                                         $sec,
                                                         $secKey,
                                                         $secextension = '',
                                                         $primextension = ''
                                                         )
    {
        foreach ( $data as $rw ){
            $key = '';
            if ( is_array( $primKey ) ){
                foreach ( $primKey as $di ){
                    if (isset($rw[$di . $primextension]))
                        $key .= $rw[$di . $primextension] . ',';
                }
            } else {
                if (isset($rw[$primKey . $primextension]))
                    $key = $rw[$primKey . $primextension];
            }

            if ( isset( $rw[$secKey . $secextension] ) && 
                 isset( $sec[$rw[$secKey . $secextension]] ) ){
                if ( !isset( $prim[$key][$primAttrib] ) )
                    $prim[$key][$primAttrib] = $sec[$rw[$secKey . $secextension]];
            }
        }

        return $prim;
    }

    /**
     * concatenates two arrays by using attribute lists, removes assoc indizes,
     * the secondary objects will be collected in one array,
     * which will be attached to the primary object
     *
     * @param string[][] $data an array, which represents the data, received sql data
     * @param string[][] $prim the structure of objects to which you want to append the new objects
     * @param string[]/string $primKey an array or string, which represents the primary key of the objects
     * @param string[] $primAttrib the defined attributes of the primary objects
     * @param string[][] $sec the structure with objects, you want to attach
     * @param string $secKey a primary key of the objects you want to attach
     * @param string $extension optional, a const postfix for the column names of the objects you want to attach (string)
     *
     * @return string[][], the concatenated lists
     */
    public static function concatResultObjectListAsArray( 
                                                         $data,
                                                         $prim,
                                                         $primKey,
                                                         $primAttrib,
                                                         $sec,
                                                         $secKey,
                                                         $extension = ''
                                                         )
    {
        foreach ( $prim as & $row ){
            $row[$primAttrib] = array( );
        }

        foreach ( $data as $rw ){
            $key = '';
            if ( is_array( $primKey ) ){
                foreach ( $primKey as $di ){
                    if (isset($rw[$di]))
                        $key .= $rw[$di] . ',';
                }
            } else {
                if (isset($rw[$primKey]))
                    $key = $rw[$primKey];
            }

            if ( isset($rw[$secKey]) && isset( $sec[$rw[$secKey]] ) ){
                $prim[$key][$primAttrib][] = $rw[$secKey];
            }
        }

        $prim = array_merge( $prim );
        return $prim;
    }
}

 
?>

