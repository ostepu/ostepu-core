<?php
/**
 * @file FSPdf.php contains the FSPdf class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2016
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class for creating, storing and loading PDF files from the file system
 */
class FSPdf
{
    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    private $_component = null;
    private $config = array();
    public function __construct( )
    {
        if (file_exists(dirname(__FILE__).'/config.ini')){
            $this->config = parse_ini_file(
                                           dirname(__FILE__).'/config.ini',
                                           TRUE
                                           );
        }
       
        $component = new Model('pdf', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Creates a PDF file consisting of the request body and then returns it.
     *
     * Called when this component receives an HTTP POST request to
     * /pdf/$orientation/temporary/$filename.
     * The request body should contain a JSON object representing the PDF file.
     *
     * @param string $orientation The orientation of the PDF file, e.g. portrait or landscape.
     * @param string $filename A freely chosen filename of the PDF file which should be stored.
     * @see http://wiki.spipu.net/doku.php
     */
    public function addPdfPermanent( $callName, $input, $params = array() )
    {
        $name = sha1( $input->getText() );
       
        // generate pdf
        $filePath = FSPdf::generateFilePath(
                                            $params['folder'],
                                            $name
                                            );
                                          
        if (!file_exists( $this->config['DIR']['files'].'/'.$filePath ) ){
            FSPdf::generatepath( $this->config['DIR']['files'].'/'.dirname( $filePath ) );
           
            $result = FSPdf::createPdf($input);

            // writes the file to filesystem
            $file = fopen(
                          $this->config['DIR']['files'].'/'.$filePath,
                          'w'
                          );

            if ($file){
                fwrite(
                       $file,
                       $result
                       );
                fclose( $file );
             
            }else{
                $fileObject = new File( );
                $fileObject->addMessage("Datei konnte nicht im Dateisystem angelegt werden.");
                $fileObject->setStatus(409);
                Logger::Log(
                        'POST postPdf failed',
                        LogLevel::ERROR
                        );
                return Model::isProblem($fileObject);
            }
        }       
                              
        if (isset($params['filename'])){
            if (isset($result)){
                $this->_app->response->setBody($result);
           	    Model::header('Content-Length',strlen($result));
                Model::header('Content-Type','application/pdf');
                Model::header('Content-Disposition',"filename=\"".$params['filename']."\"");
                Model::header('Accept-Ranges','none');
                return Model::isCreated();
            } else {
                readfile($this->config['DIR']['files'].'/'.$filePath);
           	    Model::header('Content-Length',filesize($this->config['DIR']['files'].'/'.$filePath));
                Model::header('Content-Type','application/pdf');
                Model::header('Content-Disposition',"filename=\"".$params['filename']."\"");
                Model::header('Accept-Ranges','none');
                return Model::isCreated(file_get_contents($this->config['DIR']['files'].'/'.$filePath));
            }
           
        } else {
            $pdfFile = new File( );
            $pdfFile->setStatus(201);
            $pdfFile->setAddress( $filePath );
            $pdfFile->setMimeType('application/pdf');
           
            if (file_exists($this->config['DIR']['files'].'/'.$filePath)){
                $pdfFile->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
                $hash = sha1(file_get_contents($this->config['DIR']['files'].'/'.$filePath));  
                $pdfFile->setHash( $hash );
            }
            return Model::isCreated($pdfFile);
        }
    }
   
    public function addPdfFromFile( $callName, $input, $params = array() )
    {
        $name = sha1($params['type'].'/'.$params['a'].'/'.$params['b'].'/'.$params['c'].'/'.$params['file']);
        $targetPath = FSPdf::generateFilePath(
                                            $params['folder'],
                                            $name
                                            );
                                           
        if (!file_exists( $this->config['DIR']['files'].'/'.$targetPath ) ){
            $sourcePath = implode( '/',array_slice(array($params['type'],$params['a'],$params['b'],$params['c'],$params['file']),0));
           
            FSPdf::generatepath( $this->config['DIR']['files'].'/'.dirname( $targetPath ) );
            $body = file_get_contents($this->config['DIR']['files'].'/'.$sourcePath);
           
            $data = new Pdf();
            $data->setText($body);
            $result = FSPdf::createPdf($data);
           
            // writes the file to filesystem
            $file = fopen(
                          $this->config['DIR']['files'].'/'.$targetPath,
                          'w'
                          );

            if ($file){
                fwrite(
                       $file,
                       $result
                       );
                fclose( $file );
             
            }else{
                $fileObject = new File( );
                $fileObject->addMessage("Datei konnte nicht im Dateisystem angelegt werden.");
                $fileObject->setStatus(409);
                Logger::Log(
                        'POST postPdf failed',
                        LogLevel::ERROR
                        );
                return Model::isProblem($fileObject);
            }
        }
       
        $pdfFile = new File( );
        $pdfFile->setStatus(201);
        $pdfFile->setAddress( $targetPath );
        $pdfFile->setMimeType('application/pdf');
       
        if (file_exists($this->config['DIR']['files'].'/'.$targetPath)){
            $pdfFile->setFileSize( filesize( $this->config['DIR']['files'].'/'.$targetPath ) );
            $hash = sha1(file_get_contents($this->config['DIR']['files'].'/'.$targetPath));  
            $pdfFile->setHash( $hash );
        }
       
        return Model::isCreated($pdfFile);
    }
   
    public function addPdfFromFile2( $callName, $input, $params = array() )
    {
        // convert all file objects to pdf's
    }
   
    public function addPdfFromFile3( $callName, $files, $params = array() )
    {
        // merge all file objects to one pdf

        $hashArray = array( );
        foreach ( $files as $part ){
            if ( $part->getBody( ) !== null ){
                $hashArray[] = $part->getBody( );
               
            } else
                $hashArray[] = $part->getAddress( ) . $part->getDisplayName( );
        }
     
        $name = sha1( implode(
                              "\n",
                              $hashArray
                              ) );

        $targetPath = FSPdf::generateFilePath(
                                            $params['folder'],
                                            $name
                                            );

        if (!file_exists( $this->config['DIR']['files'].'/'.$targetPath ) ){
       
            $body="";
            foreach($files as $part){
                if ( $part->getBody( ) !== null ){
                    // use body
                    $body.=$part->getBody( true ).'<br>';
                } else {
                    $file = $this->config['DIR']['files']. '/' . $part->getAddress( );
                    if (file_exists($file)){
                        $text = file_get_contents($file);
                        if (mb_detect_encoding($text, 'UTF-8', true) === false) {
                            $text = utf8_encode($text);
                        }
                        $text = htmlentities(htmlentities($text));
                        $body.= $text.'<br>';
                    } else {
                        // failure
                    }
                }
            }
        ///echo $body;
            FSPdf::generatepath( $this->config['DIR']['files'].'/'.dirname( $targetPath ) );
           
            $data = new Pdf();
            $data->setText($body);
            $result = FSPdf::createPdf($data);
           
            // writes the file to filesystem
            $file = fopen(
                          $this->config['DIR']['files'].'/'.$targetPath,
                          'w'
                          );

            if ($file){
                fwrite(
                       $file,
                       $result
                       );
                fclose( $file );
             
            }else{
                $fileObject = new File( );
                $fileObject->addMessage('Datei konnte nicht im Dateisystem angelegt werden.');
                $fileObject->setStatus(409);
                Logger::Log(
                        'POST postPdf failed',
                        LogLevel::ERROR
                        );
                return Model::isProblem($fileObject);
            }
        }
       
        $pdfFile = new File( );
        $pdfFile->setStatus(201);
        $pdfFile->setAddress( $targetPath );
        $pdfFile->setMimeType('application/pdf');
       
        if (file_exists($this->config['DIR']['files'].'/'.$targetPath)){
            $pdfFile->setFileSize( filesize( $this->config['DIR']['files'].'/'.$targetPath ) );
            $hash = sha1(file_get_contents($this->config['DIR']['files'].'/'.$targetPath));  
            $pdfFile->setHash( $hash );
        }
        return Model::isCreated($pdfFile);
    }
    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /pdf/$a/$b/$c/$file/$filename.
     *
     * @param string $hash The hash of the file which should be returned.
     * @param string $filename A freely chosen filename of the returned file.
     */
    public function getPdfDocument( $callName, $input, $params = array() )
    {
        $path = array($params['folder'],$params['a'],$params['b'],$params['c'], $params['file']);

        $filePath = implode(
                            '/',
                            array_slice(
                                        $path,
                                        0
                                        )
                            );

        if ( strlen( $this->config['DIR']['files'].'/'.$filePath ) > 1 &&
             file_exists( $this->config['DIR']['files'].'/'.$filePath ) ){

            // the file was found
            Model::header('Content-Type','application/pdf');
            Model::header('Content-Disposition',"filename=\"".$params['filename']."\"");  
            Model::header('Content-Length',filesize($this->config['DIR']['files'].'/'.$filePath));
            Model::header('Accept-Ranges','none');
            return Model::isOk(file_get_contents($this->config['DIR']['files'].'/'.$filePath));
           
        }
        return Model::isProblem();
    }

    /**
     * Returns the file infos as a JSON file object.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the requested file.
     */
    public function getPdfData( $callName, $input, $params = array() )
    {
        $path = array($params['folder'],$params['a'],$params['b'],$params['c'], $params['file']);

        $filePath = implode(
                            '/',
                            array_slice(
                                        $path,
                                        0
                                        )
                            );

        if ( strlen( $this->config['DIR']['files'].'/'.$filePath ) > 0 &&
             file_exists( $this->config['DIR']['files'].'/'.$filePath ) ){

            // the file was found
            $file = new File( );
            $file->setAddress( $filePath );
            $file->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
            $file->setHash( sha1_file( $this->config['DIR']['files'].'/'.$filePath ) );
            $file->setMimeType('application/pdf');
            return Model::isOk($file);
           
        }
        return Model::isProblem(new File( ));
    }

    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the file which should be deleted.
     */
    public function deletePdf( $callName, $input, $params = array() )
    {
        $path = array($params['folder'],$params['a'],$params['b'],$params['c'], $params['file']);

        $filePath = implode(
                            '/',
                            array_slice(
                                        $path,
                                        0
                                        )
                            );

        if ( strlen( $filePath ) > 0 &&
             file_exists( $this->config['DIR']['files'] . '/' . $filePath ) ){

            // after the successful deletion, we want to return the file data
            $file = new File( );
            $file->setAddress( $filePath );
            $file->setFileSize( filesize( $this->config['DIR']['files'] . '/' . $filePath ) );
            $file->setHash( sha1_file( $this->config['DIR']['files'] . '/' . $filePath ) );
            $file->setMimeType('application/pdf');

            // removes the file
            unlink( $this->config['DIR']['files'] . '/' . $filePath );

            // the removing/unlink process failed, if the file still exists.
            if ( file_exists( $this->config['DIR']['files'] . '/' . $filePath ) ){
            return Model::isProblem(new File( ));
            }

            // the file is removed
            return Model::isCreated($file);
           
        } else {

            // file does not exist
            return Model::isProblem(new File( ));
        }
    }
   
    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts GET GetExistsPlatform',
                    LogLevel::DEBUG
                    );
                   
        if (!file_exists(dirname(__FILE__).'/config.ini')){
            return Model::isProblem();
        }
      
        return Model::isOk();
    }
   
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts DELETE DeletePlatform',
                    LogLevel::DEBUG
                    );
        if (file_exists(dirname(__FILE__).'/config.ini') && !unlink(dirname(__FILE__).'/config.ini')){
            return Model::isProblem();
        }
       
        return Model::isCreated();
    }
   
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts POST AddPlatform',
                    LogLevel::DEBUG
                    );
       
        $file = dirname(__FILE__).'/config.ini';
        $text = "[DIR]\n".
                "temp = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$input->getTempDirectory()))."\"\n".
                "files = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$input->getFilesDirectory()))."\"\n";
               
        if (!@file_put_contents($file,$text)){
            Logger::Log(
                        'POST AddPlatform failed, config.ini no access',
                        LogLevel::ERROR
                        );

            return Model::isProblem();
        }  

        $platform = new Platform();
        $platform->setStatus(201);
       
        return Model::isCreated($platform);
    }
   
    /**
     * Creates a file path by splitting the hash.
     *
     * @param string $type The prefix of the file path.
     * @param string $hash The hash of the file.
     */
    public static function generateFilePath(
                                            $type,
                                            $file
                                            )
    {
        if ( strlen( $file ) >= 4 ){
            return $type . '/' . $file[0] . '/' . $file[1] . '/' . $file[2] . '/' . substr(
                                                                                           $file,
                                                                                           3
                                                                                           );
           
        } else
            return'';
    }

    /**
     * Creates the path in the filesystem, if necessary.
     *
     * @param string $path The path which should be created.
     * @see http://php.net/manual/de/function.mkdir.php#83265
     */
    public static function generatepath( $path, $mode = 0755 )
    {
        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
        $e = explode("/", ltrim($path, "/"));
        if(substr($path, 0, 1) == "/") {
            $e[0] = "/".$e[0];
        }
        $c = count($e);
        $cp = $e[0];
        for($i = 1; $i < $c; $i++) {
            if(!is_dir($cp) && !@mkdir($cp, $mode)) {
                return false;
            }
            $cp .= "/".$e[$i];
        }
        return @mkdir($path, $mode);
    }

    /**
     * Selects the components which are responsible for handling the file with
     * the given hash.
     *
     * @param link[] $linkedComponents An array of links to components which could
     * possibly handle the file.
     * @param string $hash The hash of the file.
     */
    public static function filterRelevantLinks(
                                               $linkedComponents,
                                               $hash
                                               )
    {
        $result = array( );
        foreach ( $linkedComponents as $link ){
            $in = explode(
                          '-',
                          $link->getRelevanz( )
                          );
            if ( count( $in ) < 2 ){
                $result[] = $link;
               
            }elseif ( FSPdf::isRelevant(
                                        $hash,
                                        $in[0],
                                        $in[1]
                                        ) ){
                $result[] = $link;
            }
        }
        return $result;
    }

    /**
     * Decides if the given component is responsible for the specific hash.
     *
     * @param string $hash The hash of the file.
     * @param string $_relevantBegin The minimum hash the component is responsible for.
     * @param string $_relevantEnd The maximum hash the component is responsible for.
     */
    public static function isRelevant(
                                      $hash,
                                      $relevant_begin,
                                      $relevant_end
                                      )
    {

        // to compare the begin and the end, we need an other form
        $begin = hexdec( substr(
                                $relevant_begin,
                                0,
                                strlen( $relevant_begin )
                                ) );
        $end = hexdec( substr(
                              $relevant_end,
                              0,
                              strlen( $relevant_end )
                              ) );

        // the numeric form of the test hash
        $current = hexdec( substr(
                                  $hash,
                                  0,
                                  strlen( $relevant_end )
                                  ) );

        if ( $current >= $begin &&
             $current <= $end ){
            return true;
           
        } else
            return false;
    }
   
    public static function createPdf($data)
    {
        require_once(dirname(__FILE__).'/_tcpdf_6.0.095/tcpdf_autoconfig.php');
        require_once(dirname(__FILE__).'/_tcpdf_6.0.095/tcpdf.php');

        $pdf = new TCPDF(
                       ($data->getOrientation()!==null ? $data->getOrientation() : 'P'),
                       'mm',
                       ($data->getFormat()!==null ? $data->getFormat() : 'A4')
                       );
                      
        $pdf->SetAutoPageBreak( true );

        $pdf->SetTitle($data->getTitle()!==null ? $data->getTitle() : '');
        $pdf->SetSubject($data->getSubject()!==null ? $data->getSubject() : '');
        $pdf->SetAuthor($data->getAuthor()!==null ? $data->getAuthor() : '');
        $pdf->SetCreator($data->getCreator()!==null ? $data->getCreator() : '');
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage( );
        $text=htmlspecialchars_decode($data->getText());

        $pdf->WriteHTML($text);

        // stores the pdf binary data to $result
        $result = $pdf->Output(
                               '',
                               'S'
                               );
        return $result;
    }
}


