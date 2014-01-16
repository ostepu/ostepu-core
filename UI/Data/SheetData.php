<?php
if (isset($_GET['id'])) {
    print $_GET['id'];
} else {
    // possible exercise types
    $exerciseTypes = array("Theorie", "Praxis", "Theorie Bonus",
                           "Praxis Bonus");

    // generate 1-10 exercise sheets
    $sheets = array();
    $sheetCount = rand(1,10);

    for ($i=0; $i < $sheetCount; $i++) { 
        $sheet = array();

        $sheetName = $i + 1;
        $sheet['sheetName'] = "Serie {$sheetName}";

        $sheet['exercises'] = array();

        // generate 1-8 exercises for this sheet
        $exerciseCount = rand(1,8);

        // remember how many points/max points this sheet has
        $sheetMaxPoints = 0;
        $sheetPoints = 0;

        for ($j=0; $j < $exerciseCount; $j++) { 
            $exercise = array();

            // pick a random type
            $exerciseTypeId = rand(0, count($exerciseTypes) - 1);
            $exercise['exerciseType'] = $exerciseTypes[$exerciseTypeId];

            // pick a random amount of maximum points
            $maxPoints = rand(5,20);
            $sheetMaxPoints += $maxPoints;
            $exercise['maxPoints'] = $maxPoints;

            // pick a random amount of points <= maximumPoints
            $points = rand(0,$maxPoints);
            $sheetPoints += $points;
            $exercise['points'] = $points;

            $sheet['exercises'][] = $exercise;


        }

        // calculate reached points
        $sheet['exerciseSheetInfo'] = round($sheetPoints / $sheetMaxPoints * 100, 1);

        // crate a random end date
        $now = time() + 60 * 60 * 24 * 7;
        $before = time() - 60 * 60 * 24 * 180;
        $sheetEndTime = rand($before, $now);

        $sheetEnd = date("d.m.Y H:i", $sheetEndTime);
        $sheet['endTime'] = $sheetEnd;

        $sheets[] = $sheet;
    }

    $sheets = array_reverse($sheets);
    $sheets = array("sheets" => $sheets);

    // return all sheets as a json object
    $sheets = json_encode($sheets);

    print $sheets;
}
?>