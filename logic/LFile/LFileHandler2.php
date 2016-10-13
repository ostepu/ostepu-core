<?php
/**
 * @file LFileHandler2.php contains the LFileHandler2 class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

 include_once ( dirname(__FILE__). '/../../Assistants/Structures.php' );
 include_once ( dirname(__FILE__). '/../../Assistants/Request.php' );

/**
 * A class, to handle the methods to add and delete a file properly.
 */
class LFileHandler2
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
        if ($file->getAddress() == null || $file->getHash() == null){
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
            if ($file->getFileId() != null){
                return $file;
            }

            //request to database file table to check if the file already exists
            /*$answer = Request::routeRequest(
                                            'GET',
                                           '/file'.$path.'/hash/'.$file->getHash(),
                                            $header,
                                            '',
                                            $database,
                                            'file'
                                            );*/
            $answer = array('status'=>404); // überspringt das Abfragen über den Hash der Datei

            if ($answer['status'] < 200 || $answer['status'] > 299 || !isset($answer['content'])) { //if file does not exists, add it to db file table
                $answer = Request::routeRequest(
                                                'POST',
                                                '/file'.$path,
                                                $header,
                                                File::encodeFile($file),
                                                $database,
                                                'file'
                                                );
///echo File::encodeFile($file);return;
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
                                            '/file/file/'.$file->getFileId(),
                                            $header,
                                            '',
                                            $database,
                                            'file'
                                            );

            // even if file has been deleted from db file table delete it from fs
            if ($answer['status'] >= 200 && $answer['status'] <= 299 && isset($answer['content']) && !empty($answer['content'])) {
                $file = File::decodeFile($answer['content']);

                if (is_object($file) && $file->getAddress() !== null){
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
                } else {
                    return null;
                }
            }
            else
                return $file;
        }
        else
            return null;
    }
}