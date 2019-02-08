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

              <a href="php/logout.php">Log Out</a>
              <a href="">Settings &#9881</a>
              <a href="contactus.php">Contact Us</a>
      </nav>
      <section id='FsteScroll'>

      </section>
  </body>
  <script src="JS/Menu.js"></script>

</html>
