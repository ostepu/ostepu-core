<?php 
/**
    * 
    */
class GroupSheet
{
    protected $template;
    protected $content;

    public function __construct()
    {
        
    }

    public function show()
    {
        print $this->template;
    }
}
?>