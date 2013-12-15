<?php
/**
* @file (filename)
* %(description)
*/ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Pdf/PdfTable.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig(FSPdf::getBaseDir());

if (!$com->used())
    new FSPdf($com->loadConfig());

/**
 * (description)
 */
class FSPdf
{
    private static $_baseDir = "pdf";
    
    public static function getBaseDir()
    {
        return FSPdf::$_baseDir;
    }
    
    public static function setBaseDir($value)
    {
        FSPdf::$_baseDir = $value;
    }
    
    private $_app;
    private $_conf;
    private $_fs = array();

    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function __construct($_conf)
    {
        $this->_conf = $_conf;
        $this->_fs = $this->_conf->getLinks();
        
        $this->_app = new \Slim\Slim();

        $this->_app->response->headers->set('Content-Type', 'application/json');
        
        // POST PostPdfPermanent
        $this->_app->post('/'.FSPdf::$_baseDir . '/:orientation/permanent', array($this,'postPdfPermanent'));
        
        // POST PostPdfTemporary
        $this->_app->post('/'.FSPdf::$_baseDir . '/:orientation/temporary/:filename', array($this,'postPdfTemporary'));
        
        // GET GetPdfData
        $this->_app->get('/'.FSPdf::$_baseDir.'/:hash', array($this,'getPdfData'));
        
        // GET GetPdfDocument
        $this->_app->get('/'.FSPdf::$_baseDir.'/:hash/:filename', array($this,'getPdfDocument'));
        
        // DELETE DeletePdf
        $this->_app->delete('/'.FSPdf::$_baseDir.'/:hash', array($this,'deletePdf'));

         // run Slim
         $this->_app->run();

    } 
    
    /**
     * POST PostPdfPermanent
     */
    public function postPdfPermanent($orientation)
    {       
        $body = $this->_app->request->getBody();
        $data = json_decode($body, true);
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            return;
        }

        
        $pdf=new PDF($orientation, "mm", "A4");
        $pdf->SetAutoPageBreak(true);
 
        $pdf->SetFont('Courier', '', 10);
        $pdf->AddPage();
        $pdf->Table($data, $orientation);
        
        $result = $pdf->Output("test.pdf", "S");
        $fileObject = new File();
        $fileObject->setHash(sha1($body));
        $filePath = FSPdf::generateFilePath(FSPdf::getBaseDir(), $fileObject->getHash());
        $fileObject->setAddress(FSPdf::getBaseDir() . '/' . $fileObject->getHash());
        $fileObject->setBody(base64_encode($result));
        
        $links = FSPdf::filterRelevantLinks($this->_fs, $fileObject->getHash());
        $result = Request::routeRequest("POST",
                                        '/'.$filePath,
                                        $this->_app->request->headers->all(),
                                        File::encodeFile($fileObject),
                                        $links,
                                        FSPdf::getBaseDir());
        
        if ($result['status']>=200 && $result['status']<=299){
            $tempObject = File::decodeFile($result['content']);
            $fileObject->setFileSize($tempObject->getFileSize());
            $fileObject->setBody(null);
            $this->_app->response->setStatus($result['status']);
            $this->_app->response->setBody(File::encodeFile($fileObject));
        } else{
            $this->_app->response->setStatus(451);
            $fileObject->setBody(null);
            $this->_app->response->setBody(File::encodeFile($fileObject));
            $this->_app->stop();
        }
    }

    /**
     * POST PostPdfTemporary
     */
    public function postPdfTemporary($orientation, $filename = "")
    {       
        $body = $this->_app->request->getBody();
        $data = json_decode($body, true);
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            return;
        }

        
        $pdf=new PDF($orientation, "mm", "A4");
        $pdf->SetAutoPageBreak(true);
 
        $pdf->SetFont('Courier', '', 10);
        $pdf->AddPage();
        $pdf->Table($data, $orientation);

        $result = $pdf->Output("test.pdf", "S");
        $this->_app->response->setStatus(200);
        $this->_app->response->headers->set('Content-Type', 'application/octet-stream');
        $this->_app->response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
        $this->_app->response->setBody($result);
    }
    
    /**
     *  GET GetPdfDocument
     *
     * @param $hash (description)
     * @param $filename (description)
     */
    public function getPdfDocument($hash, $filename)
    {      
        $links = FSPdf::filterRelevantLinks($this->_fs, $hash);
        $filePath = FSPdf::generateFilePath(FSPdf::getBaseDir(), $hash);
        $result = Request::routeRequest("GET",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FSPdf::getBaseDir());
        
        if (isset($result['status']))
            $this->_app->response->setStatus($result['status']);
        
        if (isset($result['content']))
            $this->_app->response->setBody($result['content']);

        if (isset($result['headers']['Content-Type']))
            $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
            
        $this->_app->response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
        $this->_app->stop();
    }

    /**
     * GET GetPdfData
     *
     * @param $hash (description)
     */
    public function getPdfData($hash)
    {  
        $links = FSPdf::filterRelevantLinks($this->_fs, $hash);
        $filePath = FSPdf::generateFilePath(FSPdf::getBaseDir(), $hash);
        $result = Request::routeRequest("INFO",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FSPdf::getBaseDir());
                                      
        if (isset($result['headers']['Content-Type']))
            $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
            
        if ($result['status']>=200 && $result['status']<=299 && isset($result['content'])){
            $tempObject = File::decodeFile($result['content']);
            $tempObject->setAddress(FSPdf::getBaseDir() . '/' . $hash);
            $this->_app->response->setStatus($result['status']);
            $this->_app->response->setBody(File::encodeFile($tempObject));
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }                              

        $this->_app->stop();
    }
    
    /**
     * DELETE DeletePdf
     *
     * @param $hash (description)
     */
    public function deletePdf($hash)
    {
        $links = FSPdf::filterRelevantLinks($this->_fs, $hash);
        $filePath = FSPdf::generateFilePath(FSPdf::getBaseDir(), $hash);
        $result = Request::routeRequest("DELETE",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FSPdf::getBaseDir());
                                      
        if ($result['status']>=200 && $result['status']<=299 && isset($result['content'])){
            $tempObject = File::decodeFile($result['content']);
            $tempObject->setAddress(FSPdf::getBaseDir() . '/' . $hash);
            $this->_app->response->setStatus($result['status']);
            $this->_app->response->setBody(File::encodeFile($tempObject));
        } else{
            $this->_app->response->setStatus(452);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }
        $this->_app->stop();  
    }
    
    /**
     * (description)
     *
     * @param $type (description)
     * @param $file (description)
     */
    public static function generateFilePath($type,$file)
    {
       if (strlen($file)>=4){
           return $type . "/" . $file[0] . "/" . $file[1] . "/" . $file[2] . "/" . substr($file,3);
       } else
           return "";
    }
    
    /**
     * (description)
     *
     * @param $path (description)
     */
    public static function generatepath($path)
    {
        $parts = explode("/", $path);
        if (count($parts)>0){
            $path = $parts[0];
            for($i=1;$i<=count($parts);$i++){
                if (!is_dir($path))
                    mkdir($path,0755);
                if ($i<count($parts))
                    $path = $path . '/' . $parts[$i];
            }
        }
    }  
    
    /**
     * (description)
     *
     * @param $linkedComponents (description)
     * @param $hash (description)
     */
    public static function filterRelevantLinks($linkedComponents, $hash)
    {
        $result = array();
        foreach ($linkedComponents as $link){
            $in = explode('-', $link->getRelevanz());
            if (count($in)<2){
                array_push($result,$link);
            } elseif (FSPdf::isRelevant($hash, $in[0],$in[1])) {
                array_push($result,$link);
            }
        }
        return $result;
    }
    
    /**
     * (description)
     *
     * @param $hash (description)
     * @param $_relevantBegin (description)
     * @param $_relevantEnd (description)
     */
    public static function isRelevant($hash,$_relevantBegin,$_relevantEnd){
        $begin = hexdec(substr($_relevantBegin,0,strlen($_relevantBegin)));
        $end = hexdec(substr($_relevantEnd,0,strlen($_relevantEnd)));
        $current = hexdec(substr($hash,0,strlen($_relevantEnd)));
        if ($current>=$begin && $current<=$end){
            return true;
        } else
            return false;  
    }
}

?>