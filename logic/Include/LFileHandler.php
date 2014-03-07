<?php
/**
 * @file LFileHandler.php contains the LFileHandler class
 *
 * @author Martin Daute
 */

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
     * @param string $lURL The address of the logic-controller.
     * @param array $header The header of the request.
     * @param array $file The file that should being deleted.
     *
     * @return array $file A file that represents the new information
     * which belongs to the added one. If there are an error, an empty array is returned.
     */
    public static function add($lURL, $header, $file)
    {
        $URL = $lURL.'/FS/file';
        $displayName = $file['displayName'];
        //request to filesystem to save the file
        $answer = Request::custom('POST', $URL, $header, json_encode($file));
        // check if file has been saved
        if ($answer['status'] >= 200 and $answer['status'] < 300) {
            $file = json_decode($answer['content'], true);
            //request to database file table to check if the file already exists
            $URL = $lURL.'/DB/file/hash/'.$file['hash'];
            $answer = Request::custom('GET', $URL, $header, "");
            $returnFile = json_decode($answer['content'], true);
            if (empty($returnFile)) { //if file does not exists, add it to db file table
                $URL = $lURL.'/DB/file';
                $file['displayName'] = $displayName;
                $answer = Request::custom('POST', $URL, $header, json_encode($file));
                // check if file has been saved
                if ($answer['status'] >= 200 and $answer['status'] < 300) {
                    $returnFile = json_decode($answer['content'], true);
                    $returnFile['displayName'] = $displayName;
                } else { // if file has not been saved return an empty file
                    $returnFile = array();
                }
            } else {
                $returnFile['displayName'] = $displayName;
            }
        } else { // if file has not been saved return an empty file
            $returnFile = array();
        }
        return $returnFile;
    }


    /**
     * Deletes a file.
     *
     * This function handles the reqest to the DBFile table to delete the information
     * belongs to this file as well as the request
     * to the filesystem to delete the file.
     *
     * @param string $lURL The address of the logic-controller.
     * @param array $header The header of the request.
     * @param array $file The file that should being deleted.
     */
    public static function delete($lURL, $header, $file)
    {
        if (!empty($file)){
            // requests to file-table of DB
            $URL = $lURL.'/DB/file/'.$file['fileId'];
            $answer = Request::custom('DELETE', $URL, $header, "");
            // even if file has been deleted from db file table delete it from fs
            if ($answer['status'] >= 200 and $answer['status'] < 300) {
                // requests to filesystem
                $URL = $lURL.'/FS/'.$file['address'];
                $answer = Request::custom('DELETE', $URL, $header, "");
            }
        }
    }
}
?>