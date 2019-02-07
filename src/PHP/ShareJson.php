<?php
// http://www.amateurprotagonist.com/a-104-using-google-spreadsheets-to-stream-ftse-data.php -- Data sort
// http://ftse.richardallen.co.uk/#ftse100 -- Json Data
// Based on testing the load time of this below page
// it was faster to scrape the required data then re make the array than just use the data

$url = 'https://spreadsheets.google.com/feeds/list/0AhySzEddwIC1dEtpWF9hQUhCWURZNEViUmpUeVgwdGc/1/public/basic?alt=json ';

// You could be fancy here but I am making a point, just get the content of this URL
$file = file_get_contents($url);

// This is a JSON file so you can expect decode to work
$json = json_decode($file);

// We only care about the rows
$rows = $json->{'feed'}->{'entry'};

// Loop each row and print it out as we go
foreach($rows as $row) {
  $pieces = explode(",",$row->{'content'}->{'$t'});
  $ShareData[]=array (
    'Sym' => $row->{'title'}->{'$t'},
    'Name' => substr($pieces[0], strpos($pieces[0], ":")+1),
    'CurPrice'=> substr($pieces[1], strpos($pieces[1], ":")+1),
    'Chg' => substr($pieces[2], strpos($pieces[2], ":")+1)
  );
}

echo json_encode($ShareData);
?>
