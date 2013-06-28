<?php
require_once("mysql_db.php");

//connect to database: server, username, password, database
$mydb = new MySqlDB("localhost", "", "","test"); //enter your parameters


$sql="SELECT Color FROM Colors";
$results=$mydb->query($sql);

$total_rows=$mydb->numRows($results);

?>

<!DOCTYPE html>
<head>
<title>Colors and Votes</title>
<link rel="stylesheet" type="text/css" href="ajax3600.css">
<script src="jquery-1.7.2.min.js" type="text/javascript"></script>

<script type="text/javascript">

var total_row =<?=$total_rows?>;

$(document).ready(function(){ 
  $(".myClick").click(function(){ 
	    var title = $(this).text();
	    var vote_id = "." + title;
		$.post(
				"getVotes.php", 
				{color:title},
				function(data){
					$(vote_id).html(data);
					totalVotes();
				}
		);

		
	});
});

function totalVotes()
{
	var t=0;
	for(i=0; i<total_row; i++)
	{
		row_id="vote" + i.toString();
		
		if(!isNaN(parseInt(document.getElementById(row_id).innerHTML)))
			t = t + parseInt(document.getElementById(row_id).innerHTML);
	}
	document.getElementById("myTotal").innerHTML= t;
}

</script>
</head>

<body>

<table align="center" width="600">

	<tr><td colspan="2" class="title"><p class="myTotal">Votes for Colors</p></td></tr>
	<tr><td colspan="2">Click on the Color Name to see how many votes for that color. When you do click on the Total, the sum of above numbers will show</td></tr>

	<tr><td width="300"><p class="head">Color</p></td><td width="300"><p class="head">Votes</p></td></tr>
<?php
$i = 0;
	while ($myRow=$mydb->fetchAssoc($results)) {
		$voteRow='vote' . $i;
?>
		<tr><td><a href="#" class="myClick"><?=$myRow['Color']?></a></td><td><div class="<?=$myRow['Color']?>" id="<?=$voteRow?>"></div></td></tr>
<?php	
		$i++;	
	}
?>

	<tr><td><a href="#" onClick="totalVotes()">Total</a></td><td><div id="myTotal">&nbsp;</div></td></tr>	
</table>
<br>

</body>
</html>
