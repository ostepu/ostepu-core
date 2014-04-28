<?php

require('tfpdf.php');

function array_top($arr){
    return $arr[count($arr)-1];
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
////////////////////////////////////

class Formatierung
{
    public $B=false;
    public $I=false;
    public $U=false;
    public $HREF='';
    public $ALIGN='';
    
    public $DrawColor=0;
    public $FillColor=0;
    public $TextColor=0;
    public $FontSize=12;
    public $Font = 'times';
    
    public function Anwenden($pdf){
        $pdf->B = $this->B;
        $pdf->I = $this->I;
        $pdf->U = $this->U;
        $pdf->HREF = $this->HREF;
        $pdf->ALIGN = $this->ALIGN;
        $pdf->SetDrawColor($this->DrawColor);
        $pdf->SetFillColor($this->FillColor);
        $pdf->SetTextColor($this->TextColor);
        
        $style='';
    foreach(array('B','I','U') as $s)
    {
        if($pdf->$s>0)
            $style.=$s;
    }
        
        //$pdf->SetFont();
        $pdf->SetFont($this->Font,$style,$this->FontSize);
    }
    
    public static function Erzeugen($formatobjekt){
        $form = new Formatierung();
        $form->B = $formatobjekt->B;
        $form->I = $formatobjekt->I;
        $form->U = $formatobjekt->U;
        $form->HREF = $formatobjekt->HREF;
        $form->ALIGN = $formatobjekt->ALIGN;
        $form->DrawColor = $formatobjekt->DrawColor;
        $form->FillColor = $formatobjekt->FillColor;
        $form->TextColor = $formatobjekt->TextColor;
        $form->FontSize = $formatobjekt->FontSize;
        $form->Font = $formatobjekt->Font;
        return $form;
    }
}

class PDF_HTML extends tFPDF
{
//variables of html parser
public $B;
public $I;
public $U;
public $HREF;
public $ALIGN;
public $fontList;

public $Format = array();

#region HTML2RGB
function HTML2RGB($c, &$r, &$g, &$b)
{
    static $colors = array('black'=>'#000000','silver'=>'#C0C0C0','gray'=>'#808080','white'=>'#FFFFFF',
                        'maroon'=>'#800000','red'=>'#FF0000','purple'=>'#800080','fuchsia'=>'#FF00FF',
                        'green'=>'#008000','lime'=>'#00FF00','olive'=>'#808000','yellow'=>'#FFFF00',
                        'navy'=>'#000080','blue'=>'#0000FF','teal'=>'#008080','aqua'=>'#00FFFF');

    $c=strtolower($c);
    if(isset($colors[$c]))
        $c=$colors[$c];
    if($c[0]!='#')
        //$this->Error('Incorrect color: '.$c);
        $c='#000000';
        
    $r=hexdec(substr($c,1,2));
    $g=hexdec(substr($c,3,2));
    $b=hexdec(substr($c,5,2));
}

function HTMLColor($c, $g=-1, $b=-1)
{
    if(is_string($c)){
    static $colors = array('black'=>'#000000','silver'=>'#C0C0C0','gray'=>'#808080','white'=>'#FFFFFF',
                        'maroon'=>'#800000','red'=>'#FF0000','purple'=>'#800080','fuchsia'=>'#FF00FF',
                        'green'=>'#008000','lime'=>'#00FF00','olive'=>'#808000','yellow'=>'#FFFF00',
                        'navy'=>'#000080','blue'=>'#0000FF','teal'=>'#008080','aqua'=>'#00FFFF');

    $c=strtolower($c);
    if(isset($colors[$c]))
        $c=$colors[$c];
    if($c[0]!='#')
        $c='#000000';
    }
    else{
    $c = "#";
    $c .= str_pad(dechex($c), 2, "0", STR_PAD_LEFT);
    $c .= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
    $c .= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
    }
        
    return $c;
}

function SetDrawColor($r, $g=-1, $b=-1)
{
    if(is_string($r))
        $this->HTML2RGB($r,$r,$g,$b);
    parent::SetDrawColor($r,$g,$b);
}

function SetFillColor($r, $g=-1, $b=-1)
{
    if(is_string($r))
        $this->HTML2RGB($r,$r,$g,$b);
    parent::SetFillColor($r,$g,$b);
}

function SetTextColor($r,$g=-1,$b=-1)
{
    if(is_string($r))
        $this->HTML2RGB($r,$r,$g,$b);
    parent::SetTextColor($r,$g,$b);
}
#endregion

function PDF_HTML($orientation='P', $unit='mm', $format='A4', $formate=null)
{
    
    //Call parent constructor
    $this->tFPDF($orientation,$unit,$format);
    
    //$this->AddFont('arial','','arial.ttf',true);
   // $this->AddFont('times','','times.ttf',true);
    
    //Initialization
    $this->B=false;
    $this->I=false;
    $this->U=false;
    $this->HREF='';
    $this->ALIGN='';
    $this->SetFont('times','',10);
    $this->fontlist=array('arial', 'times', 'courier', 'helvetica');
    
    $form = new Formatierung();
    if ($formate===null){
        $form->B = false;
        $form->I = false;
        $form->U = false;
        $form->HREF = '';
        $form->ALIGN = '';
        $form->DrawColor = 0;
        $form->FillColor = 0;
        $form->TextColor = 0;
        $form->FontSize = 12;
        $form->Font = 'times';
    }
    else
        $form = Formatierung::Erzeugen($formate);
    
    $this->Format[] = $form;
    
}

function WriteHTML($html)
{
    
    //HTML parser
    $str=array(
        '<br/>' => '<br>',
        '<hr/>' => '<hr>',
        '<br />' => '<br>',
        '<hr />' => '<hr>',
        '<tr />' => '<tr>',
        '<tr />' => '<tr>',
        '<red/>' => '<red>',
        '<blue/>' => '<blue>',
        '<green/>' => '<green>',
        '<yellow/>' => '<yellow>',
        '<red />' => '<red>',
        '<blue />' => '<blue>',
        '<green />' => '<green>',
        '<yellow />' => '<yellow>',
        '&#8220;' => '"',
        '&#8221;' => '"',
        '&#8222;' => '"',
        '&#8230;' => '...',
        '&#8217;' => '\'',
        '<li>' => "<br> - ",
        '</li>' => "",
        '</ul>' => "<br>",
        '<ul>' => "",
        '&#160;' => "\n",
        '&nbsp;' => " ",
        '&quot;' => "\"",
        '&#039;' => "'",
        '&#39;' => "'"
    );
    
    foreach ($str as $_from => $_to) $html = str_replace($_from,$_to,$html);
    
        
    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><td><table><hr><blockquote><h1><h2><h3><h4><red><green><blue><yellow><silver><gray><white><maroon><purple><fuchsia><navy><lime><olive><teal><aqua><newpage><li><ul>"); //supprime tous les tags sauf ceux reconnus
    $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            array_top($this->Format)->Anwenden($this);

            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            elseif($this->ALIGN=='center')
                $this->Cell(0,5,$e,0,1,'C');
            elseif($this->ALIGN=='left')
                $this->Cell(0,5,$e,0,1,'L');
            elseif($this->ALIGN=='right')
                $this->Cell(0,5,$e,0,1,'R');
            else
                $this->Write(5,stripslashes(txtentities($e)));
        }
        else
        {
            //Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    $form = new Formatierung();
    $form = Formatierung::Erzeugen(array_top($this->Format));
    
    //Opening tag
    switch($tag){
        case 'H1':
            $this->Ln(5);
            $form->TextColor = $this->HTMLColor(150,0,0);
            $form->FontSize = 22;
            if (isset($attr['ALIGN']))
                $form->ALIGN=$attr['ALIGN'];
            break;
        case 'H2':
            $this->Ln(5);
            $form->FontSize = 18;
            if (isset($attr['ALIGN']))
                $this->ALIGN=$attr['ALIGN'];
            break;
        case 'H3':
            $this->Ln(5);
            $form->FontSize = 16;
            if (isset($attr['ALIGN']))
                $form->ALIGN=$attr['ALIGN'];
            break;
        case 'H4':
            $this->Ln(5);
            $form->TextColor = $this->HTMLColor(102,0,0);
            $form->FontSize = 14;
            if (isset($attr['ALIGN']))
                $form->ALIGN=$attr['ALIGN'];
            break;
        case 'STRONG':
            $form->B = true;
            break;
        case 'EM':
            $form->I = true;
            break;
        case 'B':
            $form->B = true;
            break;
        case 'I':
            $form->I = true;
            break;
        case 'U':
            $form->U = true;
            break;
        case 'NEWPAGE':
            $this->AddPage(isset($attr['ORIENTATION']) ? $attr['ORIENTATION'] : '');
            $form=null;
            break;
        case 'A':
            $form->HREF=$attr['HREF'];
            break;
        case 'IMG':
            $form = null;
            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
            }
            break;
        case 'HR':
            $form=null;
            $this->Ln(7);
            $this->PutLine((isset($attr['ALIGN']) ? $attr['ALIGN'] : 'left'),(isset($attr['WIDTH']) ? $attr['WIDTH'] : '100%'),(isset($attr['COLOR']) ? $attr['COLOR']: 'black'),(isset($attr['SIZE']) ? intval($attr['SIZE'])/10 : .1));
            break;
        case 'TR':
        break;
        case 'TD':
        break;
        case 'TABLE':
        break;
        case 'BLOCKQUOTE':
            $form->TextColor = $this->HTMLColor(100,0,45);
            $this->Ln(3);
        case 'BR':
            $form=null;
            $this->Ln(5);
            break;
        case 'RED':
        case 'GREEN':
        case 'BLUE':
        case 'YELLOW':
        case 'SILVER':
        case 'MAROON':
        case 'GRAY':
        case 'WHITE':
        case 'PURPLE':
        case 'FUCHSIA':
        case 'LIME':
        case 'OLIVE':
        case 'NAVY':
        case 'TEAL':
        case 'AQUA':
            $form->TextColor = $this->HTMLColor($tag);
            break;
        case 'P':
            $this->Ln(10);
            if (isset($attr['ALIGN']))
                $form->ALIGN=$attr['ALIGN'];
            break;
        case 'FONT':
            if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                $form->TextColor = $this->HTMLColor($attr['COLOR']);
            }
            if (isset($attr['FACE'])) {
                if (in_array(strtolower($attr['FACE']), $this->fontlist)){
                    $form->Font = strtolower($attr['FACE']);
                } else
                    $form->Font = 'times';
            }
            if (isset($attr['SIZE']) && $attr['SIZE']!='') {
                $form->FontSize = $attr['SIZE'];
            }
            break;
    }
    
    if ($form !== null)
        $this->Format[] = $form;
}

function CloseTag($tag)
{
    if ($tag=='H1' || $tag=='H2' || $tag=='H3' || $tag=='H4')
        $this->Ln(6);
    if ($tag=='BLOCKQUOTE')
        $this->Ln(3);

    array_pop($this->Format);
}

function SetStyle($tag, $enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
    {
        if($this->$s>0)
            $style.=$s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}

function PutLine($align, $width, $color, $size)
{
    $this->Ln(2);
    $this->SetLineWidth($size);
    $this->SetDrawColor($color);
    
    $maxWidth = $this->w-$this->lMargin-$this->rMargin;
        
    if (ctype_digit($width)){    
    }
    elseif (strlen($width)>=3 && strtolower(substr($width,strlen($width)-2,2))==='px' && ctype_digit(substr($width,0,strlen($width)-2))){
        $width = intval(substr($width,0,strlen($width)-2));
    }
    elseif (strlen($width)>=2 && strtolower(substr($width,strlen($width)-1,1))==='%' && ctype_digit(substr($width,0,strlen($width)-1))){
        $width = ($maxWidth) / 100 * intval(substr($width,0,strlen($width)-1));
    }
    else
    $width=$maxWidth;
    

    
    if ($width>$maxWidth)
        $width = $maxWidth;
    
    if ($align==='left')
    $this->Line($this->GetX(),$this->GetY(),$this->GetX()+$width,$this->GetY());
    
    if ($align==='right')
    $this->Line($this->GetX()+$maxWidth-$width,$this->GetY(),$this->GetX()+$maxWidth,$this->GetY());
    
    if ($align==='center')
    $this->Line($this->GetX()+($maxWidth-$width)/2,$this->GetY(),$this->GetX()+$maxWidth-($maxWidth-$width)/2,$this->GetY());
    
    $this->Ln(3);
}

}
?>
