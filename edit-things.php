<?php
require 'engine.php';
$thingsResult = $db->query('SELECT * FROM things');
$things = array();
if ($thingsResult !== FALSE && $thingsResult->num_rows > 0) {
    while ($row = $thingsResult->fetch_assoc()) {
	$things[$row['id']] = $row;
    }
}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<title>Thing Editor</title>
<head>
  <style>
   body {
     font-family: "Helvetica Neue", Helvetica, Arial;
     font-size: 14px;
     line-height: 20px;
     font-weight: 400;
     color: #3b3b3b;
     -webkit-font-smoothing: antialiased;
     font-smoothing: antialiased;
     background: #2b2b2b;
   }

   .wrapper {
     margin: 0 auto;
     padding: 40px;
     max-width: 800px;
   }

   .table {
     margin: 0 0 40px 0;
     width: 100%;
     box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
     display: table;
   }
   @media screen and (max-width: 580px) {
     .table {
       display: block;
     }
   }

   .row {
     display: table-row;
     background: #f6f6f6;
   }
   .row:nth-of-type(odd) {
     background: #e9e9e9;
   }
   .row.header {
     font-weight: 900;
     color: #ffffff;
     background: #ea6153;
   }
   .row.green {
     background: #27ae60;
   }
   .row.blue {
     background: #2980b9;
   }
   @media screen and (max-width: 580px) {
     .row {
       padding: 8px 0;
       display: block;
     }
   }

   .cell {
     padding: 6px 12px;
     display: table-cell;
   }
   @media screen and (max-width: 580px) {
     .cell {
       padding: 2px 12px;
       display: block;
     }
   }
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="table">
      <div class="row header blue">
	<div class="cell">
          ID
	</div>
	<div class="cell">
          Name
	</div>
	<div class="cell">
          Edit
	</div>
      </div>

      <?php
      foreach ($things as &$thing) {
	  echo '<div class="row">'.
	       '<div class="cell">'.$thing['name_id'].'</div>'.
	       '<div class="cell">'.$thing['pretty_name'].'</div>'.
	       '<div class="cell"><a href="edit-thing.php?id='.$thing['id'].'">Edit</a></div></div>';
      }
      ?>

    </div>
  </div>
</body>
