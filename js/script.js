
$(document).ready(function() {

    $('#submit').click(function()
    {
		event.preventDefault();
        get_term($('#term_input').val());
    });
	
	$(".card").on("click", function(){
	
	var output = $(this).find('#ppttitle').html();
	window.open(output + ".pptx");
	});

});

/*

<div id="portfoliolist">

<div class="portfolio card mix_all  wow bounceIn" data-wow-delay="0.4s" data-cat="card" style="display: inline-block; opacity: 1;">
	<div class="portfolio-wrapper">		
		<a href="#small-dialog" class="b-link-stripe b-animate-go  thickbox play-icon popup-with-zoom-anim">
		  <img src="images/4.png" class="img-responsive" />
		<div class="b-wrapper">
			<ul class="b-animate b-from-left    b-delay03 ">
					<li><span>PROJECT TITLE</span></li>
					<li><span>PROJECT TITLE</span></li>
					<li><span>PROJECT TITLE</span></li>
					<li><span>PROJECT TITLE</span></li>
			 </ul>
		</div>
		</a>
	</div>
</div>	
					
					*/

var fileExt = {};
    fileExt[0]=".png";
    fileExt[1]=".jpg";
    fileExt[2]=".gif";


function get_term(inputTerm)
{
  $.post("ajax.php", 
  {
		term : inputTerm,
		task:"getExtract"
  },
  function(data)
  {
    
	console.log("input term: " + inputTerm);
	console.log("Ajax return " + data);
	
	var result = data;
	
	if(result==0)
	{
		console.log("Data is 0");
	}else
	if(result==1)
	{
		location.reload();
		/*var level1 = '<div class="portfolio card mix_all  wow bounceIn" data-wow-delay="0.4s" data-cat="card" style="display: inline-block; opacity: 1;">';
		var level2 = '<div class="portfolio-wrapper">'
		var level3 = '<a href="#small-dialog' + i + '" class="b-link-stripe b-animate-go  thickbox play-icon popup-with-zoom-anim">';
		var level4 = '<img src="' + 'images/galleryimages/' + inputTerm + '" class="img-responsive" />';
		var level5 = '<div class="b-wrapper">';
		var level6 = '<ul class="b-animate b-from-left    b-delay03 ">';
		var level7 = '<li><span>' + inputTerm + '</span></li>';
		var level8 = '<li><span>Downloads:' + 1 + '</span></li>';
		var level9 = '<li><span>Shares:' + 0 + '</span></li>';
		var level10 = '</ul>' + '</div>' + '</a>' + '</div>' + '</div>';
		
		var total = level1 + level2 + level3 + level4 + level5 + level6 + level7 + level8 + level9 + level10;
		document.getElementById('portfoliolist').innerHTML += total;*/
		
		window.open(inputTerm + ".pptx");;
	}
	else if(result==2)
	{
	    location.reload();
		window.open(inputTerm + ".pptx");
	}
  });
}

