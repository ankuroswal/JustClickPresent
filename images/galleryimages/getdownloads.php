<?php
Header("content-type: application/x-javascript");
$connect_error = 'Sorry. We are experiencing connection problems.';
mysql_connect('127.0.0.1','root','yoomadlol123') or die($connect_error);
mysql_select_db('justclickpresent');

function get_downloads($term)
{
	$sql= "select `fileDownloads` from `files` where `fileTerm`='$term'";
	$query = @mysql_query($sql);
	$row = @mysql_fetch_row($query);
	return $row[0];
}

//This function gets the file names of all images in the current directory
//and ouputs them as a JavaScript array
function returndownloads($dirname=".") {
    $pattern="(\.jpg$)|(\.png$)|(\.jpeg$)|(\.gif$)"; //valid image extensions
    $files = array();
    $curimage=0;
    if($handle = opendir($dirname)) {
        while(false !== ($file = readdir($handle))){
            if(eregi($pattern, $file)){ //if this file is a valid image
                //Output it as a JavaScript array element
                echo 'downloadsarray['.$curimage.']="'.get_downloads($file) .'";';
                $curimage++;
            }
        }
 
        closedir($handle);
    }
    return($files);
}

echo 'var downloadsarray=new Array();';
returndownloads()
?>