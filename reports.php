<?php
/*
 * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook, 
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan, 
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker. 
 * This program is part of RMH Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

/*
 * reports page for RMH homebase.
 * @author Jerrick Hoang
 * @version 11/5/2013
 */
session_cache_expire(30);
session_start();
//session_cache_expire(30);

include_once('header.php'); 
include_once('database/dbPersons.php');
include_once('domain/Person.php');
include_once('database/dbShifts.php');
include_once('domain/Shift.php');
?>

<html>
<head>
<title>Search for data objects</title>	
<link rel="stylesheet" href="lib\bootstrap\css\bootstrap.css" type="text/css" />
<link rel="stylesheet" href="styles.css" type="text/css" />
<link rel="stylesheet" href="lib/jquery-ui.css" />
<script type="text/javascript" src="lib/jquery-1.9.1.js"></script>
<script src="lib/jquery-ui.js"></script>
<script>
$(function() {
	$( "#from" ).datepicker({dateFormat: 'y-mm-dd',changeMonth:true,changeYear:true});
	$( "#to" ).datepicker({dateFormat: 'y-mm-dd',changeMonth:true,changeYear:true});

	$(document).on("keyup", ".volunteer-name", function() {
		var str = $(this).val();
		var target = $(this);
		$.ajax({
			type: 'get',
			url: 'reportsCompute.php?q='+str,
			success: function (response) {
				var suggestions = $.parseJSON(response);
				console.log(target);
				target.autocomplete({
					source: suggestions
				});
			}
		});
	});

	$("input[name='date']").change(function() {
		if ($("input[name='date']:checked").val() == 'date-range') {
			$("#fromto").show();
		} else {
			$("#fromto").hide();
		}
	});

	$("#report-submit").on('click', function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: 'reportsCompute.php',
			data: $('#search-fields').serialize(),
			success: function (response) {
				$("#outputs").html(response);
			}
		});
	} );
	
});
</script>
</head>
<body>
<div id="container">

<div id = "content">
<div>
	<p id="search-fields-container">
	<form id = "search-fields" method="post">
		<p class = "search-description" id="today">
		<?php date_default_timezone_set ("America/New_York");
		$venue = $_GET['venue'];
		$venues = array('portland'=>"RMH Portland",'bangor'=>"RMH Bangor");
		echo '<b>'." Angels on Wheels Volunteer Reports</b><br>Today's date: ".date("F d, Y");
		echo '</p>';
		echo '<input type="hidden" name="_form_submit" value="report'.$venue.'" />';?>
	<table>	<tr>
		<td class = "search-description" valign="top"> &nbsp;&nbsp;&nbsp;&nbsp;Select Report Type: 
		<p>	<select multiple name="report-types[]" id = "report-type" size="6"> <!-- size should = # of options -->
	  		<option value="volunteer-hours">Total Hours</option>
			<option value="individual-hours">Individual Hours</option>
	  		<option value="shifts-staffed-vacant">Shifts/Vacancies</option>
	  		<option value="emails">* Volunteer Emails</option>
	  		<option value="volunteers">* Volunteer Contact Info</option>
			<option value="information">* General Volunteer Info</option> <!-- added this report for general volunteer information for Gwyneth's Gift -->
			</select>
		</td>
		<td class = "search-description" valign="top">&nbsp;&nbsp; Date Range: 
			<p id="fromto"> from : <input name = "from" type="text" size="10" id="from"><br>
							&nbsp;&nbsp;&nbsp;&nbsp;to : <input name = "to" type="text" size="10" id="to"></p>
		</td>
		<td class = "search-description" valign="top">&nbsp;&nbsp; Last Name Range: 
			<p id="name_fromto"> from : <input name = "name_from" type="text" size="10" id="name_from"><br>
							&nbsp;&nbsp;&nbsp;&nbsp;to : <input name = "name_to" type="text" size="10" id="name_to"></p>
		</td>
		
	</tr>
	<tr>
	<td valign="top">
	To view report, click <input class="btn btn-success btn-sm" type="submit" value="Submit" id ="report-submit" class ="btn">
	</td></tr>
	<tr>
	<td>* To save the report,
	<button onclick="exportTableToCSV()">Click here</button>.</td></tr>
	<tr><td></td></tr>
	<tr><td>To run another report, please refresh the page.</td></tr>
	</table>
	</form>
	<script>
    function exportTableToCSV() {
      var csv = [];
      var rows = document.querySelectorAll("table tr");
      
      for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length; j++) 
          row.push(cols[j].innerText);
        
        csv.push(row.join(","));        
      }

      // Download CSV file
      var downloadLink = document.createElement("a");
      var blob = new Blob(["\ufeff", csv.join("\n")], { type: "text/csv;charset=utf-8" });
      downloadLink.href = URL.createObjectURL(blob);
      downloadLink.download = "my-table.csv";
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
    }
  </script>
	<p id="outputs">

	</p>
</div>
</div>
</div>
        <?PHP include('footer.php'); ?>

</body>