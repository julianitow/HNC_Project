<?php
session_start();
//include('php/Functions.php');
//$currentuser=getUserLevel();
$currentuser=1;
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <!- ************ Stylesheets ******* ->
    <link href="ASSETS/CSS/Style.css" rel="stylesheet" type="text/css" />
    <link href="ASSETS/CSS/MENU.css" rel="stylesheet" type="text/css" />
    <link href="ASSETS/CSS/Shares.css" rel="stylesheet" type="text/css" />
    <link href="ASSETS/CSS/News.css" rel="stylesheet" type="text/css" />
    <!- ******************************** ->
    <!- This will change depending on user settings, but by default
        will just say the name of the site ->
    <title>
        Shareflow
    </title>
  </head>
  <body>
      <nav data-action='expand'>
          <div id='nav-icon1'>
              <span></span>
              <span></span>
              <span></span>
          </div>
            <a href="index.php">Home</a>
            <?php //if($currentuser['userlevel'==0])
                  if($currentuser==0) { ?>

              <a href="register1.html">Register New Account</a>
            <?php } ?>
            <?php //if($currentuser['userlevel']==1) {
                  if($currentuser==1) { ?>
              <a href="">Test1</a>
              <a href="">Test2</a>
              <a href="">Test3</a>
            <?php } ?>
              <a href="admin.php">Administration</a>
              <label for='Currency'>Select Currency :</label>
              <select id="Currency">
                  <option value="GBP" selected="selected">Pounds (GBP)</option>
                  <option value="EUR">Euro (EUR)</option>
                  <option value="USD">US Dollar (USD)</option>
                  <option value="ISK">Króna (ISK)</option>
                  <option value="MXN">Mexican Peso (MXN)</option>
                  <option value="CHF">Swiss Franc (CHF)</option>
                  <option value="AUD">Australian Dollar (AUD)</option>
                  <option value="CNY">Chinese Yuan (CNY)</option>
                  <option value="SEK">Swedish Krona (SEK)</option>
                </select>
              <a href="php/logout.php">Log Out</a>
              <a href="">&#9881; Settings</a>
              <a href="contactus.php">Contact Us</a>
      </nav>

      <section id='FsteScroll'>
        <aside id='Updated'><aside/>
      </section>
      <section id="NewsArticles"></section>

  </body>
  <script src="JS/Menu.js"></script>
  <script src="JS/functions.js"></script>
  <script src="JS/PrepShare.js"></script>
  <script src="JS/News.js"></script>
  <script src="JS/money.js"></script> <!- https://openexchangerates.github.io/money.js -!>
  <script>
	document.onreadystatechange = function(){
		if(document.readyState=="complete") {
			prepareMenu(); // Attaches event listener to burger btn
      mkShareData(); // Makes the fste share elements
      updateShareData(); // Sets the interval to update the share data
      UpdateCurrency(); // Adds an evrnt listener to the currency dropdown
      LoadNewsStories(); // Creates the first run of news articles
      updateNews(); // Sets interval for getting news articles
		}
	}
	</script>
</html>
