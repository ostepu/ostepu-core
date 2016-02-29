<?php
/**
 * @file DataObject.php
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */


class DataObject
{

    /**
     * Der Konstruktor
     *
     * @param string $_content Der Inhalt
     * @param int $_status Der Status
     * @param string $_eTag Ein optionaler Hashwert
     */
    public function __construct($_content, $_status, $_eTag = null)
    {
        $this->content = $_content;
        $this->status = $_status;
        $this->eTag = $_eTag;
    }

    /**
     * @var string $content Der Antwortinhalt
     */
    public $content = null;

    /**
     * @var int $status Der HTTP Status der Antwort (Bsp.: 200,409)
     */
    public $status = null;

    /**
     * @var string $eTag Der Hash der Anfrage (MD5)
     */
    public $eTag = null;
}
