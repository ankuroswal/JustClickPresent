<?php 
// Sizes
//--------------------------------------
// Square
// Large Square
// Thumbnail
// Small
// Small 320
// Medium
// Medium 640
// Medium 800
// Large
// Large 1600
// Large 2048
function imagelookup($search, $tags, $size)
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
	$searchurl = "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=636a22c1fe3366612a10538b04f41b18&tags=" . $tagadd . "&text=" . $searchterm . "&sort=relevance&accuracy=&safe_search=1&content_type=1&per_page=1&page=1&format=json&nojsoncallback=1";
	$imageidjson = json_decode(file_get_contents($searchurl), true);
	$imageid = $imageidjson['photos']['photo'][0]['id'];
	// get all the sizes of the image
	$imageurl = "https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=636a22c1fe3366612a10538b04f41b18&photo_id=" . $imageid ."&format=json&nojsoncallback=1";
	$obj = json_decode(file_get_contents($imageurl), true);
	$item = $obj['sizes']['size'];
	foreach ($item as $asset)
	{
		if ($asset['label'] == $size)
		{
			$uuid = uniqid('', true);
     		$input = $asset['source'];
			$output = $uuid . '.jpg';
			file_put_contents($output, file_get_contents($input));
			return $uuid;	
		}
	}
}
imagelookup("dog", ["medical"], "Large Square");