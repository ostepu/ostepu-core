﻿<?php
    include 'Header.php';
?>

<!DOCTYPE HTML>
<html>
    <head>
	<link rel="stylesheet" type="text/css" href="CSSReset.css">	
	<link rel="stylesheet" type="text/css" href="Uebungsplattform.css">
	<title>Übungsplattform</title>
    </head>
    <body>
	<div id="body-wrapper">

<?php
    $h = new Header("Datenstrukturen", "", "Florian Lücke", "211221492");
    $h->show();
?>
	    <div id="content-wrapper">
		<div class="issue-wrapper">
		    <div class="issue-header">
			<div class="issue-title">Serie 2</div>
			<div class="issue-end">23.11.2013 20:003</div>
			<div class="issue-percent">100%</div>			
		    </div>

		    <div class="issue-body-wrapper">
			<div class="issue-body-left issue-body">
			    <ol class="exercise-list">
				<li><div class="exercise-type">Normal</div> <div class="exercise-points">0/10</div> </li>
				<li><div class="exercise-type">Bonus</div> <div class="exercise-points">0/10</div> </li>				    
			    </ol>
			</div>

			<div class="issue-body-extras issue-body">
			    <ol class="body-options">
				<li><a class="body-option" href="#">Serie ansehen</a></li>
				<li><a class="body-option" href="#">Musterlösung</a></li>
				<li><a class="body-option" href="#">Gruppe</a></li>
				<li><a class="body-option" href="#">Gruppe austreten</a></li>
			    </ol>
			</div>
		    </div> <!-- end: issue-body-wrapper -->
		</div> <!-- end: issue-wrapper -->
	    </div> <!-- end: content-wrapper -->
	</div>
    </body>
</html>

