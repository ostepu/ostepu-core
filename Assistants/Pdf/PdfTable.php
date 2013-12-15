<?php
require('fpdf/fpdf.php');

class Pdf extends FPDF
{

    function Row($data, $width, $align, $fill)
    {
        //Calculate the height of the row
        $nb=0;
        foreach ($data as $key => $value){
            $height[$key] = $this->NbLines($width[$key],$value);
            $nb=max($nb,$height[$key]);
        }
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        foreach ($data as $key => $value)
        {
            for ($i=0;$i<$nb - $height[$key];$i++){
                $value = $value . "\n ";
            }
                
            $w=$width[$key];
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Print the text
            $this->MultiCell($w,5,utf8_decode($value),'LR',$align, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
}

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function Table($data, $orientation)
    {
        //Colors, line width and bold font
        $this->SetFillColor(0,51,102);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,51,102);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        //Header
        $width = array();
        foreach($data as $row)
        {
            foreach ($row as $key => $value){
                if (!isset($width[$key])){
                    $width[$key] = $this->GetStringWidth($value)+4;
                } elseif ($width[$key] < $this->GetStringWidth($value)+4){
                    $width[$key] = $this->GetStringWidth($value)+4;
                } elseif ($width[$key] < $this->GetStringWidth($key)+4){
                    $width[$key] = $this->GetStringWidth($key)+4;
                }
                
           }
        }
        
        $sum = array_sum($width);
        $a4width = $orientation == 'P' ? 210 : 297;
        if ($sum>$a4width - 20){
            $working = array();    
            while ($sum>$a4width - 20){
                if (count($working)==0){
                    foreach ($width as $key => $value) {
                        $working[$key]=1;
                    }
                }
            
                // find highest width
                $highest = null;
                foreach ($width as $key => $value){
                    if (isset($working[$key]))
                    if ($highest == null ||  $width[$highest] < $value){
                        $highest = $key;
                    }
                }     
                
                $sum -= $width[$highest];
                $width[$highest] -= (($a4width-20) * 0.05);
                $sum += $width[$highest];
                if ($width[$highest]<$sum/count($width)){
                    unset($working[$highest]);
                }
            }
        }
        
        foreach ($data[0] as $key => $value){
            $header[$key] =  $key;
        }
        $this->Row($header, $width, 'C', 1);
        
        //Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        //Data
        $fill=0;

        foreach($data as $row)
        {
            $this->Row($row, $width, 'L', $fill);
            $fill=!$fill;
        }
    }
}

?> 