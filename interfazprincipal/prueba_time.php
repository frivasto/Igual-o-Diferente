<?php
	echo $timestamp=strtotime("00:00:56"), "\n";
	echo date('H:i:s'.$timestamp);
	
	$date = "06/10/2011 14:28";
	$stamp = strtotime($date);
	echo date('H:i:s', $stamp); // outputs 10-06
	
	echo "<br /><br />";
	$timeStamp            =    mktime(0,0,0,27,03,2012);    //Create time stamp of the first day from the give date.
    $firstDay            =     date('D',$timeStamp);
	
	echo "tiempo:: ".$firstDay;
	
	echo "<br />iempoo!: ".strtotime("0000-00-00 00:00:00");
	$timestamp=strtotime("0000-00-00 00:00:00");
	echo "<br />DATE:::::".date('H:i:s'.$timestamp);
	
	echo '<br />'.date("Y-m-d H:i:s", strtotime(' ', mktime(0,0,50,1,31,2010))); //+0 day
	
	
	//esteeeeeeeee
	echo '<br />Dtae TIME: '.date("H:i:s", strtotime(' ', mktime('00','02',50,0,0,0)));	
?>