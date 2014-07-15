<?php 


/**
 * @file FSPdf.php contains the FSPdf class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/Pdf/tfpdf/html2pdf.php' );

\Slim\Slim::registerAutoloader( );

// runs the CConfig
$com = new CConfig( FSPdf::getBaseDir( ) );

// runs the FSPdf
if ( !$com->used( ) )
    new FSPdf( $com->loadConfig( ) );

/**
 * A class for creating, storing and loading PDF files from the file system
 */
class FSPdf
{

    /**
     * @var string $_baseDir the name of the folder where the pdf files should be
     * stored in the file system
     */
    private static $_baseDir = 'pdf';

    /**
     * the string $_baseDir getter
     *
     * @return the value of $_baseDir
     */
    public static function getBaseDir( )
    {
        return FSPdf::$_baseDir;
    }

    /**
     * the $_baseDir setter
     *
     * @param string $value the new value for $_baseDir
     */
    public static function setBaseDir( $value )
    {
        FSPdf::$_baseDir = $value;
    }

    /**
     * @var Slim $_app the slim object
     */
    private $_app;

    /**
     * @var Component $_conf the component data object
     */
    private $_conf;

    /**
     * @var Link[] $_fs links to components which work with files, e.g. FSBinder
     */
    private $_fs = array( );

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct( $_conf )
    {
        $this->_conf = $_conf;
        $this->_fs = $this->_conf->getLinks( );

        $this->_app = new \Slim\Slim( array( 'debug' => false ) );

        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
                                            );

        // POST PostPdfPermanent
        $this->_app->post( 
                          '/' . FSPdf::$_baseDir . '(/)',
                          array( 
                                $this,
                                'postPdfPermanent'
                                )
                          );

        // POST PostPdfTemporary
        $this->_app->post( 
                          '/' . FSPdf::$_baseDir . '/:filename(/)',
                          array( 
                                $this,
                                'postPdfTemporary'
                                )
                          );

        // GET GetPdfData
        $this->_app->get( 
                         '/' . FSPdf::$_baseDir . '/:hash(/)',
                         array( 
                               $this,
                               'getPdfData'
                               )
                         );

        // GET GetPdfDocument
        $this->_app->get( 
                         '/' . FSPdf::$_baseDir . '/:hash/:filename(/)',
                         array( 
                               $this,
                               'getPdfDocument'
                               )
                         );

        // DELETE DeletePdf
        $this->_app->delete( 
                            '/' . FSPdf::$_baseDir . '/:hash(/)',
                            array( 
                                  $this,
                                  'deletePdf'
                                  )
                            );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Creates a PDF file consisting of the request body and permanently
     * stores it in the file system.
     *
     * Called when this component receives an HTTP POST request to
     * /pdf/$orientation/permanent.
     * The request body should contain a JSON object representing the PDF file.
     *
     * @param string $orientation The orientation of the PDF, e.g. portrait or landscape.
     */
    public function postPdfPermanent( )
    {
        $body = $this->_app->request->getBody( );
        $data = Pdf::decodePdf($body);

        $form = new Formatierung();
        $form->Font = ($data->getFont()!==null ? $data->getFont() : 'times');
        $form->FontSize = ($data->getFontSize()!==null ? $data->getFontSize() : '12');
        $form->TextColor = ($data->getTextColor()!=null ? $data->getTextColor() : 'black');
        
        $pdf = new PDF_HTML( 
                       ($data->getOrientation()!==null ? $data->getOrientation() : 'P'),
                       'mm',
                       ($data->getFormat()!==null ? $data->getFormat() : 'A4'),
                       $form
                       );
                       
        $pdf->SetAutoPageBreak( true );

        $pdf->SetTitle($data->getTitle()!==null ? $data->getTitle() : '');
        $pdf->SetSubject($data->getSubject()!==null ? $data->getSubject() : '');
        $pdf->SetAuthor($data->getAuthor()!==null ? $data->getAuthor() : '');
        $pdf->SetCreator($data->getCreator()!==null ? $data->getCreator() : '');
        
        $pdf->AddPage( );

        $pdf->WriteHTML(utf8_decode($data->getText()));

        // stores the pdf binary data to $result
        $result = $pdf->Output( 
                               'name.pdf',
                               'S'
                               );
                               
        $fileObject = new File( );
        $fileObject->setHash( sha1( $result ) );
        $filePath = FSPdf::generateFilePath( 
                                            FSPdf::getBaseDir( ),
                                            $fileObject->getHash( )
                                            );
        $fileObject->setAddress( FSPdf::getBaseDir( ) . '/' . $fileObject->getHash( ) );
        $fileObject->setBody( base64_encode( $result ) );

        $links = FSPdf::filterRelevantLinks( 
                                            $this->_fs,
                                            $fileObject->getHash( )
                                            );
        $result = Request::routeRequest( 
                                        'POST',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        File::encodeFile( $fileObject ),
                                        $links,
                                        FSPdf::getBaseDir( )
                                        );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $tempObject = File::decodeFile( $result['content'] );
            $fileObject->setFileSize( $tempObject->getFileSize( ) );
            $fileObject->setBody( null );
            $this->_app->response->setStatus( 201 );
            $this->_app->response->setBody( File::encodeFile( $fileObject ) );
            
        } else {
            $this->_app->response->setStatus( 451 );
            $fileObject->setBody( null );
            $this->_app->response->setBody( File::encodeFile( $fileObject ) );
            $this->_app->stop( );
        }
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
     */
    public function postPdfTemporary(
                                     $filename = ''
                                     )
    {
        $body = $this->_app->request->getBody( );
        $data = Pdf::decodePdf($body);

        $form = new Formatierung();
        $form->Font = ($data->getFont()!==null ? $data->getFont() : 'times');
        $form->FontSize = ($data->getFontSize()!==null ? $data->getFontSize() : '12');
        $form->TextColor = ($data->getTextColor()!=null ? $data->getTextColor() : 'black');
        
        $pdf = new PDF_HTML( 
                       ($data->getOrientation()!==null ? $data->getOrientation() : 'P'),
                       'mm',
                       ($data->getFormat()!==null ? $data->getFormat() : 'A4'),
                       $form
                       );
                       
        $pdf->SetAutoPageBreak( true );

        $pdf->SetTitle($data->getTitle()!==null ? $data->getTitle() : '');
        $pdf->SetSubject($data->getSubject()!==null ? $data->getSubject() : '');
        $pdf->SetAuthor($data->getAuthor()!==null ? $data->getAuthor() : '');
        $pdf->SetCreator($data->getCreator()!==null ? $data->getCreator() : '');
        
        $pdf->AddPage( );

        $pdf->WriteHTML($data->getText());

        // stores the pdf binary data to $result
        $result = $pdf->Output( 
                               '',
                               'S'
                               );
                               
        $this->_app->response->setStatus( 201 );
        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/octet-stream'
                                            );
        $this->_app->response->headers->set( 
                                            'Content-Disposition',
                                            "attachment; filename=\"$filename\""
                                            );
        $this->_app->response->setBody( $result );
    }

    /**
     * Returns a PDF file.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$hash/$filename.
     *
     * @param string $hash The hash of the file which should be returned.
     * @param string $filename A freely chosen filename of the returned file.
     */
    public function getPdfDocument( 
                                   $hash,
                                   $filename
                                   )
    {
        $links = FSPdf::filterRelevantLinks( 
                                            $this->_fs,
                                            $hash
                                            );
        $filePath = FSPdf::generateFilePath( 
                                            FSPdf::getBaseDir( ),
                                            $hash
                                            );
        $result = Request::routeRequest( 
                                        'GET',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        '',
                                        $links,
                                        FSPdf::getBaseDir( )
                                        );

        if ( isset( $result['status'] ) )
            $this->_app->response->setStatus( $result['status'] );

        if ( isset( $result['content'] ) )
            $this->_app->response->setBody( $result['content'] );

        if ( isset( $result['headers']['Content-Type'] ) )
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                $result['headers']['Content-Type']
                                                );

        $this->_app->response->headers->set( 
                                            'Content-Disposition',
                                            "attachment; filename=\"$filename\""
                                            );
        $this->_app->stop( );
    }

    /**
     * Returns the PDF file infos as a JSON file object.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$hash.
     *
     * @param string $hash The hash of the requested file.
     */
    public function getPdfData( $hash )
    {
        $links = FSPdf::filterRelevantLinks( 
                                            $this->_fs,
                                            $hash
                                            );
        $filePath = FSPdf::generateFilePath( 
                                            FSPdf::getBaseDir( ),
                                            $hash
                                            );
        $result = Request::routeRequest( 
                                        'INFO',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        '',
                                        $links,
                                        FSPdf::getBaseDir( )
                                        );

        if ( isset( $result['headers']['Content-Type'] ) )
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                $result['headers']['Content-Type']
                                                );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 && 
             isset( $result['content'] ) ){
            $tempObject = File::decodeFile( $result['content'] );
            $tempObject->setAddress( FSPdf::getBaseDir( ) . '/' . $hash );
            $this->_app->response->setStatus( $result['status'] );
            $this->_app->response->setBody( File::encodeFile( $tempObject ) );
            
        } else {
            $this->_app->response->setStatus( 409 );
            $this->_app->response->setBody( File::encodeFile( new File( ) ) );
            $this->_app->stop( );
        }

        $this->_app->stop( );
    }

    /**
     * Deletes a PDF file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file/$hash.
     *
     * @param string $hash The hash of the file which should be deleted.
     */
    public function deletePdf( $hash )
    {
        $links = FSPdf::filterRelevantLinks( 
                                            $this->_fs,
                                            $hash
                                            );
        $filePath = FSPdf::generateFilePath( 
                                            FSPdf::getBaseDir( ),
                                            $hash
                                            );
        $result = Request::routeRequest( 
                                        'DELETE',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        '',
                                        $links,
                                        FSPdf::getBaseDir( )
                                        );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 && 
             isset( $result['content'] ) ){
            $tempObject = File::decodeFile( $result['content'] );
            $tempObject->setAddress( FSPdf::getBaseDir( ) . '/' . $hash );
            $this->_app->response->setStatus( $result['status'] );
            $this->_app->response->setBody( File::encodeFile( $tempObject ) );
            
        } else {
            $this->_app->response->setStatus( 452 );
            $this->_app->response->setBody( File::encodeFile( new File( ) ) );
            $this->_app->stop( );
        }
        $this->_app->stop( );
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
     */
    public static function generatepath( $path )
    {
        $parts = explode( 
                         '/',
                         $path
                         );
        if ( count( $parts ) > 0 ){
            $path = $parts[0];
            for ( $i = 1;$i <= count( $parts );$i++ ){
                if ( !is_dir( $path ) )
                    mkdir( 
                          $path,
                          0755
                          );
                if ( $i < count( $parts ) )
                    $path .= '/' . $parts[$i];
            }
        }
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
}

 
?>

