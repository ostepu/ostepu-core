<?php    
/**
 * @file phplatex.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

# Written by scarfboy@gmail.com. Use at your own risk. See README for intro and usage.

$imgfmt="png"; #literally used in all extensions, so either gif or png

function phplatex_cleantmp($tempfname,$todir) {
  global $imgfmt;
  #Cleans the various files that probably got created for a specific run, based on the run's filename.
  if (chdir($todir)===FALSE) { return '[directory access error, fix permissions (and empty tmp manually this time)]'; }
  error_reporting(0); #at least one of these probably won't exist, but disable the error reporting related to that.
  unlink($tempfname);     #the longer/cleaner way would be check for existance for each
  unlink($tempfname.".tex");  unlink($tempfname.".log");
  unlink($tempfname.".aux");  unlink($tempfname.".dvi");
  unlink($tempfname.".ps");   unlink($tempfname.".".$imgfmt);
  error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
  #try-catch wouldn've been nice. This is rather overkill too, the way I use it.
  return '';
}

function phplatex_colorhex($r,$g,$b) {
  #there must be a better way of doing this. It's not even particularly clean.
  $hex=array("","","");
  if(strlen($hex[0]=dechex(min(256*$r,255)))==1){ $hex[0]="0".$hex[0]; }
  if(strlen($hex[1]=dechex(min(256*$g,255)))==1){ $hex[1]="0".$hex[1]; }
  if(strlen($hex[2]=dechex(min(256*$b,255)))==1){ $hex[2]="0".$hex[2]; }
  return implode("",$hex);
}


function texify($string,$dpi='90', $r=0.0,$g=0.0,$b=0.0, $br=1.0,$bg=1.0,$bb=1.0,$extraprelude="", $sharpen=FALSE) {
  global $imgfmt;
  if ($dpi>300) $dpi=300;

  $back=phplatex_colorhex($br,$bg,$bb);
  $fore=phplatex_colorhex($r,$g,$b);

  # Figure out TeX, either to get the right cache entry or to, you know, compile
  # Semi-common (ams) symbol packages are included.
  $totex = "\\documentclass[14pt,landscape]{extarticle}\n".
           "\\usepackage{color}\n".
           "\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\usepackage{amssymb}\n".
           $extraprelude."\n".
           "\\pagestyle{empty}\n".  #removes header/footer; necessary for trim
           "\\begin{document}\n".
           "\\color[rgb]{".$r.",".$g.",".$b."}\n". 
           "\\pagecolor[rgb]{".$br.",".$bg.",".$bb."}\n".
           $string."\n".
           "\\end{document}\n";
  
  $strhash = sha1($totex).'.'.$dpi; #file cache entry string:  40-char hash string plus size
  $stralt = str_replace("&","&amp;", preg_replace("/[\"\n]/","",$string)); #stuck in the alt and title attributes
                                                                           #May need some extra safety.
  $heredir=dirname(__FILE__);

  #Experiment: Tries to guess vertical positioning fix that will look short rendered works look natural inside HTML text.
  #Only descenders are really a problem since HTML's leeway is upwards.
  #TODO: avoid using letters that are part of TeX commands.
  # the slash varies per font. In the default CM it is, in Times and others it isn't.
  $ascenders ="/(b|d|f|h|i|j|k|l|t|A|B|C|D|E|F|G|H|I|J|L|K|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|\[|\]|\\{|\\}|\(|\)|\/|0|1|2|3|4|5|6|7|8|9|\\#|\*|\?|'|\\\\'|\\\\`|\\\\v)/";
  $monoliners="/(a|c|e|m|n|o|r|s|u|v|w|x|z|-|=|\+|:|.)/";
  $descenders="/(g|j|p|\/|q|y|Q|,|;|\[|\]|\\{|\\}|\(|\)|\#|\\\\LaTeX|\\\\TeX|\\\\c\{)/"; 

  $deepdescenders="/(\[|\]|\\{|\\}|\(|\)|\\int)/";
  $ba = preg_match_all($ascenders,  $string,$m); 
  $bm = preg_match_all($monoliners, $string,$m); 
  $bd = preg_match_all($descenders, $string,$m); 
  $dd = preg_match_all($deepdescenders, $string,$m); 
  if      ($dd>0)            $verticalalign="vertical-align: -27%";   # descenders only: move down
  else if ($bd>0 && $ba==0)  $verticalalign="vertical-align: -20%";   # descenders only: move down
  else if ($bd==0 && $ba>0)  $verticalalign="vertical-align: -1%";    # ascenders only: move up/do nothing?
  else if ($bd==0 && $ba==0) $verticalalign="vertical-align: -2%";    # neither    vertical-align: 0%
  else                       $verticalalign="vertical-align: -19%";   # both ascender and regular descender

  #check image cache, return link if exists
  #the vertical-align is to fix text baseline/descender(/tail) details in and on-average sort of way
  if (file_exists($heredir.'/texImages/'.$strhash.'.'.$imgfmt)) 
    return '<img style="'.$verticalalign.'" title="'.$stralt.'" alt="LaTeX formula: '.$stralt.'" src="texImages/'.$strhash.'.'.$imgfmt.'">';

 
  #chdir to have superfluous file be created in tmp.
  if (chdir("/tmp")===FALSE) { return false; } #I should chech whether file creation is allowed to give a nice error for that problem case
  
  $tfn = tempnam("/tmp", 'PTX'); #file in tmp dir

  #write temporary .tex file
  if ( ($tex = fopen($tfn.'.tex', "w"))==FALSE) { return false; }
  fwrite($tex, $totex); fclose($tex);

  #Run latex to create a .dvi. Try to fix minor errors instead of breaking/pausing on them.
  exec('latex --interaction=nonstopmode '.$tfn.'.tex');
  if (!file_exists($tfn.".dvi")) {
    phplatex_cleantmp($tfn,$heredir);
    return false;
  }

  exec('dvipng -bg Transparent -q -T tight '.$tfn.' -o '.$tfn.'.'.$imgfmt );
  if ( !file_exists($tfn.'.'.$imgfmt))  {
    phplatex_cleantmp($tfn,$heredir);
    return false;
  }
  
  #Copy result image to chache.
  copy($tfn.'.'.$imgfmt, $heredir.'/texImages/'.$strhash.'.'.$imgfmt);

  #Clean up temporary files, and return link to just-created image
  phplatex_cleantmp($tfn,$heredir);
  return '<img style="'.$verticalalign.'" title="'.$stralt.'" alt="LaTeX formula: '.$stralt.'" src="texImages/'.$strhash.'.'.$imgfmt.'">';
} 