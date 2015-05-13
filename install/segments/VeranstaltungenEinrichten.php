<?php
#region VeranstaltungenEinrichten
if (!$simple)
    if ($selected_menu === 4){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('courses','description')."</td></tr>";                       
        $text .= Design::erstelleZeile($simple, Sprachen::Get('courses','createTables'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallCourses'), 'h');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('courses','details'), 'e', Design::erstelleAuswahl($simple, $data['C']['c_details'], 'data[C][c_details]', 'details', null), 'v');
        
        if ($installCourses){
            foreach ($installCoursesResult as $courseid => $dat){
                $text .= "<tr><td class='e' rowspan='1'>({$dat['course']->getId()}) {$dat['course']->getSemester()}</td><td class='v'>{$dat['course']->getName()}</td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
            }
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($simple, Sprachen::Get('courses','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['C']['c_details'], 'data[C][c_details]', null,true);
        echo $text;
    }
#endregion VeranstaltungenEinrichten