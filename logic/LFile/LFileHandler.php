<?php
/**
 * @file LFileHandler.php contains the LFileHandler class
 *
 * @author Martin Daute
 * @author Till Uhlig
 * @date 2014
 */
 
 include_once ( '../../Assistants/Structures.php' );
 include_once ( '../../Assistants/Request.php' );

/**
 * A class, to handle the methods to add and delete a file properly.
 */
class LFileHandler
{
    /**
     * Adds a file.
     *
     * This function handles the request to the filesystem to store the file
     * as well as the reqest to the DBFile table to store the information
     * belongs to this file.
     *
     * @param string $database The link of the database.
     * @param string $filesystem The link of the filesystem.
     * @param array $header The header of the request.
     * @param array $file The file that should being deleted.
     *
     * @return array $file A file that represents the new information
     * which belongs to the added one. If there are an error, an empty array is returned.
     */
    public static function add($database, $filesystem, $path, $header, $file)
    {
    
        $displayName = $file->getDisplayName();
        //request to filesystem to save the file
        if ($file->getAddress()===null){
        $answer = Request::routeRequest( 
                                            'POST',
                                            '/file'.$path,
                                            $header,
                                            File::encodeFile( $file ),
                                            $filesystem,
                                            'file'
                                            );
        }
        else{
        $answer=array();
        $answer['status'] = 201;
        $answer['content'] = File::encodeFile( $file );
        }

        // check if file has been saved
        if ($answer['status'] >= 200 && $answer['status'] <= 299 && isset($answer['content'])) {
            $file = File::decodeFile($answer['content']);
            //request to database file table to check if the file already exists
            $answer = Request::routeRequest( 
                                            'GET',
                                           '/file'.$path.'hash/'.$file->getHash(),
                                            $header,
                                            '',
                                            $database,
                                            'file'
                                            );

            if ($answer['status'] < 200 || $answer['status'] > 299 || !isset($answer['content'])) { //if file does not exists, add it to db file table
                $answer = Request::routeRequest( 
                                                'POST',
                                                '/file'.$path,
                                                $header,
                                                File::encodeFile($file),
                                                $database,
                                                'file'
                                                );

                // check if file has been saved
                if ($answer['status'] >= 200 && $answer['status'] <= 299 && isset($answer['content'])) {
                    $returnFile = File::decodeFile($answer['content']);
                    $file->setFileId($returnFile->getFileId());
                    $file->setDisplayName($displayName);
                    return $file;
                } else { // if file has not been saved return an empty file
                    return null;
                }
            } else {
                $returnFile = File::decodeFile($answer['content']);
                $file->setFileId($returnFile->getFileId());
                $file->setDisplayName($displayName);
                return $file;
            }
        } else { // if file has not been saved return an empty file
            return null;
        }
        return null;
    }


    /**
     * Deletes a file.
     *
     * This function handles the reqest to the DBFile table to delete the information
     * belongs to this file as well as the request
     * to the filesystem to delete the file.
     *
     * @param string $database The link of the database.
     * @param string $filesystem The link of the filesystem.
     * @param array $header The header of the request.
     * @param array $file The file that should being deleted.
     */
    public static function delete($database, $filesystem, $header, $file)
    {
        if ($file !== null && $file!==array()){
            // requests to file-table of DB
            $answer = Request::routeRequest( 
                                            'DELETE',
                                            '/file/'.$file->getFileId(),
                                            $header,
                                            '',
                                            $database,
                                            'file'
                                            );
                                                
            // even if file has been deleted from db file table delete it from fs
            if ($answer['status'] >= 200 && $answer['status'] <= 299 && isset($answer['content']) && !empty($answer['content'])) {
                $file = File::decodeFile($answer['content']);
            
                // requests to filesystem
                $answer = Request::routeRequest( 
                                                'DELETE',
                                                '/'.$file->getAddress(),
                                                $header,
                                                '',
                                                $filesystem,
                                                'file'
                                                );
                                                
                if ($answer['status'] >= 200 && $answer['status'] <= 299) {
                    return File::decodeFile($answer['content']);
                }
                else
                    return null;
            }
            else
                return $file;
        }
        else
            return null;
    }
}
?>