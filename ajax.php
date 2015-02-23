<?php
include_once 'Header.php';
use PhpOffice\PhpPowerpoint\PhpPowerpoint;
use PhpOffice\PhpPowerpoint\Style\Alignment;
use PhpOffice\PhpPowerpoint\Style\Bullet;
use PhpOffice\PhpPowerpoint\Style\Color;

function summary($title) {
      $url = baseUrl($title).'action=query&prop=extracts&exintro=&explaintext=';
      
      $result = file_get_contents($url);
      ///remove all \n
      
      $json = json_decode($result, true);
      $title = $json['query']['pages'];
 
      foreach ($title as $item) {
      return $item['extract'];
      }
      return $result;
}

function sentencesFromExtract($title) {
  $summary = summary($title);
    $summary = preg_replace("/\\n/", "", $summary);
    $sentences = explode('.', $summary);

    return $sentences;
}

function slideContent($title) {
      $sentences = sentencesFromExtract($title);
      $counter = 1;
      $slideContents = array();
      $ndx = 0;
      $countSentences = count($sentences) - 1;

      for($i = 0; $i < $countSentences; $i+=3) {
            if($i + 2 < $countSentences) {
                  $slideContents[] = array('• '.$sentences[$i]."\n\n", '• '.$sentences[$i+1]."\n\n", '• '.$sentences[$i+2]."\n\n");
            }
            elseif ($i + 1 < $countSentences) {
                  $slideContents[] = array('• '.$sentences[$i]."\n\n", '• '.$sentences[$i+1]."\n\n");
            }
             elseif ($i < $countSentences) {
                  $slideContents[] = array('• '.$sentences[$i]."\n\n");
            }
      }
      return $slideContents;
}



// this method gets a json of all the sections all you need to do is get how many sections there is and create a slide per section with getTextFromSections with the title name and number
function getListOfSections($title)
{
      $url = "http://en.wikipedia.org/w/api.php?format=json&action=parse&page=" .$title . "&prop=sections";
}

function getTextFromSections($title, $sectionno)
{
      $url = "http://en.wikipedia.org/w/api.php?format=json&action=parse&page=" . $title . "&section=" . $sectionno . "&prop=text";
      $result = file_get_contents($url);
      $json = json_decode($result, true);
      $textarr = $json['parse']['text'];
      foreach ($textarr as $text)
      {
            // removes all html nonsense 
            $text  = preg_replace('/\p{Cc}+/u', '', $text);
            $text =  preg_replace('#<h3.*?>([^>]*)</h3>#i', '$1', $text);
            $text =  preg_replace('#<a.*?>([^>]*)</a>#i', '$1', $text);
            $text =  preg_replace('#<li.*?>([^>]*)</li>#i', '$1', $text);
            $text = preg_replace('/\[.+\]/', '', $text); 
            $text = preg_replace('/[[:^print:]]/', '', $text);

            // needs to save each <p> </p> in array to parse
            // strip_tags($text)

            //echo($text);
      }
}

function baseUrl($title) {
      return 'http://en.wikipedia.org/w/api.php?format=json&continue=&titles='.$title.'&';
}

function getContent($title) {
      $url = 'http://en.wikipedia.org/w/api.php?action=parse&format=json&page='.$title;
 
      $result = file_get_contents($url);
 
      $pattern = '/<span class=\"toctext\".*span>/';
      //$pattern = '<span class=\\\"toctext\\\">([\w\ ]+)</span>';
      
      $json = json_decode($result, true);
      $data = $json['parse']['text']['*'];
 
      preg_match_all($pattern, $data, $matches);
 
      return $matches;
}

function getExtract($term)
{
	echo "Getting Extract";
}


function imagelookup($search, $tags, $size, $type)
{
	$searchterm = str_replace(' ', '', $search);
	$tagadd = "";
	$count = 0;	
	foreach ($tags as $item)
	{
		if ($count = 0)
		{
			$tagadd = $tagadd;
		}
		else 
		{
			$tagadd = $item . "+" . $tagadd;
		}
		$count++;
	}
	// search image id based on the search term and the tags
	$searchurl = "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=4710e34bbc5a48ccbfb50a2d62925285&tags=" . $tagadd . "&text=" . $searchterm . "&sort=relevance&accuracy=&safe_search=1&content_type=1&per_page=1&page=1&format=json&nojsoncallback=1";
	$imageidjson = json_decode(file_get_contents($searchurl), true);
	$imageid = $imageidjson['photos']['photo'][0]['id'];
	// get all the sizes of the image
	$imageurl = "https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=4710e34bbc5a48ccbfb50a2d62925285&photo_id=" . $imageid ."&format=json&nojsoncallback=1";
	$obj = json_decode(file_get_contents($imageurl), true);
	$item = $obj['sizes']['size'];
	foreach ($item as $asset)
	{
		if ($asset['label'] == $size)
		{
			$uuid = uniqid('', true);
			$input = $asset['source'];
						
			if($type==-1)
			{
				$output = 'images/galleryimages/' . $search . '.jpg';
			}
			else
				$output = 'images/' .$search . '_' . $type . '.jpg';
			
			
			file_put_contents($output, file_get_contents($input));
			return $uuid;	
		}
	}
}


function createTitleSlide($term, $objPHPPowerPoint)
{
	  imagelookup($term, [], "Square", 0);
	  $imgPath = 'images/' . $term . '_0.jpg';
	// Create slide
	$currentSlide = $objPHPPowerPoint->getActiveSlide();

	$shape = $currentSlide->createDrawingShape();
	$shape->setName('PHPPowerPoint logo')
		  ->setDescription('PHPPowerPoint logo')
		  ->setPath($imgPath)
		  ->setHeight(225)
		  ->setOffsetX(370)
		  ->setOffsetY(300);

	$shape = $currentSlide->createRichTextShape()
		  ->setHeight(300)
		  ->setWidth(600)
		  ->setOffsetX(170)
		  ->setOffsetY(180);
	$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
	$textRun = $shape->createTextRun($term);
	$textRun->getFont()->setBold(true)
					   ->setSize(60)
					   ->setColor( new Color( 'FFE06B20' ) );
  
}
	
function generate_powerpoint($term)
{
	imagelookup($term, [], "Large Square",-1);
	
	$colorBlack = new Color( 'FF000000' );

	$objPHPPowerPoint = new PhpPowerpoint();
	
	$objPHPPowerPoint->getProperties()->setCreator('PHPOffice')
                                  ->setLastModifiedBy('PHPPowerPoint Team')
                                  ->setTitle('HackPoly 2015')
                                  ->setSubject('Dynamically Generated PowerPoints')
                                  ->setDescription('Sample 02 Description')
                                  ->setKeywords('office 2007 openxml libreoffice odt php')
                                  ->setCategory('Sample Category');
	
	createTitleSlide($term, $objPHPPowerPoint);
	
$bulletpoints = slideContent($term);

	foreach($bulletpoints as $bullet) {
	  $thing = "";
	  foreach ($bullet as $item) {
		$thing = $thing."$item";
	  }

	$currentSlide = $objPHPPowerPoint->getActiveSlide();
	
	$currentSlide = $objPHPPowerPoint->createSlide();

	$shape = $currentSlide->createRichTextShape()
		  ->setHeight(500)
		  ->setWidth(800)
		  ->setOffsetX(60)
		  ->setOffsetY(60);

	  $textRun = $shape->createTextRun($thing);
	  $textRun->getFont()->setBold(true)
					   ->setSize(14)
					   ->setColor( new Color( 'FFE06B20' ) );
	}


	insertTerm($term);
	$writers = array('PowerPoint2007' => 'pptx');
	@write($objPHPPowerPoint, $term, $writers);
}

function insertTerm($term)
{
    $sql ="INSERT INTO `justclickpresent`.`files` (`fileID`, `fileContents`, `fileDownloads`, `fileTerm`, `fileShares`) VALUES (NULL, 'no content', 1, '$term', 0)";
	$query = @mysql_query($sql);
}

function sanitize($data)
{
	return @mysql_real_escape_string($data);
}

function powerpoint_exists($term)
{
	$term = sanitize($term);
	$query = @mysql_query("select COUNT(`fileID`) from `files` where `fileTerm` = '$term'");
	return (@mysql_result($query,0)==1) ? true : false;
}

function connect()
{
	$connect = @mysql_connect('localhost','root','yoomadlol123');
	$db = @mysql_select_db('justclickpresent');
	return $connect;
}

$method = $_SERVER['REQUEST_METHOD'];

if(strtolower($method)=='post')
{
	if(isset($_POST['term']) && $_POST['task']=='getExtract')
	{
		$term = addslashes($_POST['term']);			
		if($term=="")
		{
			echo 0;
		}
		else
		{
			$connect = connect();
			
			if($connect)
			{
				if(powerpoint_exists($term))
				{
					echo 2;
				}
				else
				{
					generate_powerpoint($term);
					echo 1;
				}
			}
		}
	}
}
?>