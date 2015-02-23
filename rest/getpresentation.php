<?php
$connect_error = 'Sorry. We are experiencing connection problems.';
mysql_connect('127.0.0.1','root','yoomadlol123') or die($connect_error);
mysql_select_db('justclickpresent');

function sanitize($data)
{
	return mysql_real_escape_string($data);
}

function term_exists($term)
{
	$term = sanitize($term);
	$query = mysql_query("select COUNT(`fileID`) from `files` where `fileTerm` = '$term'");

	return (mysql_result($query,0)==1) ? true : false;
}

function get_downloads($term)
{
	$sql= "select `fileDownloads` from `files` where `fileTerm`='$term'";
	$query = mysql_query($sql);
	$row = mysql_fetch_row($query);
	return $row[0];
}

if(!empty($_GET['term']))
{
	$term = sanitize($_GET['term']);

	if(term_exists($term))
	{
		$downloads = get_downloads($term);
		$downloads +=1;
		mysql_query("update `files` set `fileDownloads`='$downloads' where `fileTerm`='$term'");
		
		//download the file
		$filename = $term;
		ob_end_clean();
		header("Content-Type: application/octet-stream; "); 
		header("Content-Transfer-Encoding: binary"); 
		header("Content-Length: ". filesize($filename).";"); 
		header("Content-disposition: attachment; filename=" . $filename);
		readfile($filename);
		die();
	}
 }


mysql_close();
?>