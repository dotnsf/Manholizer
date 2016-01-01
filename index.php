<?php
$apikey = '(Your AlchemyAPI Key)'; # http://www.alchemyapi.com/api/register.html
$servername = $_SERVER['SERVER_NAME'];
?>
<html>
<head>
<title>Manholizer</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<link rel="shortcut icon" href="./icon.png" type="image/png"/>
<link rel="icon" href="./icon.png" type="image/png"/>
<link rel="apple-touch-icon" href="./icon.png"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>

<meta property="og:title" content="Manholizer（マンホライザー）"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="http://<?php echo $servername; ?>/"/>
<meta property="og:image" content="http://<?php echo $servername; ?>/tokyo001.png"/>
<meta property="og:site_name" content="Manholizer（マンホライザー）"/>
<meta property="og:description" content="Manholizer（マンホライザー） - 技術のムダ使いで誰でもマンホールに早変わり！画像を人工知能で顔認識し、顔の位置に合わせてマンホライズします（性別や年齢判定も行います）。"/>
<meta name="description" content="Manholizer（マンホライザー） - 技術のムダ使いで誰でもマンホールに早変わり！画像を人工知能で顔認識し、顔の位置に合わせてマンホライズします（性別や年齢判定も行います）。"/>
<meta name="keywords" content="Manhole,マンホール,画像認識,顔認識,人工知能,AlchemyAPI"/>

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
var manhole_image1 = new Image();
manhole_image1.src = './tokyo001.png';
var manhole_image2 = new Image();
manhole_image2.src = './tokyo002.png';

var arr = new Array();
function addFace( id, x, y, w, h, g, a ){
  var o = new Array( id, x, y, w, h, g, a );
  arr.push( o );
}

function drawFace(){
  while( arr.length > 0 ){
    var o = arr.shift();
    var id = o[0];
    var x = o[1];
    var y = o[2];
    var w = o[3];
    var h = o[4];
    var g = o[5];
    var a = o[6];

    //. 画像の縮尺（縦横の短い方に合わせる）
    var z = 1.0;
    if( w < h ){
      z = w / 126.0;
    }else{
      z = h / 133.0;
    }

    //. 画像の位置
    var i_x = x - ( 112.0 * z );
    var i_y = y - ( 101.0 * z );
    var i_w = 349.0 * z;
    var i_h = 349.0 * z;

    //. ageRangeの位置
    var a_x = i_x + ( 158.0 * z );
    var a_y = i_y + ( 21.0 * z );
    var a_w = 34.0 * z;
    var a_h = 16.0 * z;

    //. img タグ追加
    $("#cvs").append( "<img id='face_" + id + "' class='absolute'/>" );

    if( g == 1 ){
      $("#face_"+id).attr( 'src', manhole_image1.src );
    }else{
      $("#face_"+id).attr( 'src', manhole_image2.src );
    }
    $("#face_"+id).css({
      'left': i_x,
      'top': i_y,
      'height': i_h,
      'width': i_w
    });

    //. p タグ追加
    if( g == 1 ){
      $("#cvs").append( "<p id='p_" + id + "' class='absolute male'>" + a + "</p>" );
    }else{
      $("#cvs").append( "<p id='p_" + id + "' class='absolute female'>" + a + "</p>" );
    }

    $("#p_"+id).css({
      'left': a_x,
      'top': a_y,
      'height': a_h,
      'width': a_w
    });
  }
}
</script>
<style>
.relative{
  position: relative;
}
.absolute{
  position: absolute;
}
.male{
  text-align: center;
  font-size: xx-small;
  background-color: #ccccff;
}
.female{
  text-align: center;
  font-size: xx-small;
  background-color: #ffcccc;
}
</style>
</head>
<body>
<?php
if( isset( $_POST['url'] ) ){
  $url = $_POST['url'];
?>
<script type="text/javascript">
$(function(){
  var img = new Image();
  img.src = '<?php echo $url; ?>';
  img.addEventListener( 'load', function(){
    $("#base").attr( 'src', img.src );
    $("#base").attr( 'width', img.width );
    $("#base").attr( 'height', img.height );
    $(".relative").attr( 'width', img.width );

    setTimeout( 'drawFace()', 1000 );
  }, false );
});
</script>
<?php
  $apiurl = 'http://access.alchemyapi.com/calls/url/URLGetRankedImageFaceTags?apikey=' . $apikey . '&outputMode=json&knowledgeGraph=1&url=' . urlencode( $url );
  $text = file_get_contents( $apiurl );
?>
<!-- $text
<?php echo $text; ?>
-->
<?php
  $json = json_decode( $text );
  $imageFaces = $json->imageFaces;
  if( count( $imageFaces ) ){
?>
<script type="text/javascript">
<?php
    for( $i = 0; $i < count( $imageFaces ); $i ++ ){
      $imageFace = $imageFaces[$i];
      $positionX = $imageFace->positionX;
      $positionY = $imageFace->positionY;
      $width = $imageFace->width;
      $height = $imageFace->height;
      $age = $imageFace->age->ageRange;
      $gender_s = $imageFace->gender->gender;
      $gender = ( $gender_s == 'MALE' ? 1 : 2 );
?>
addFace( <?php echo $i; ?>, <?php echo $positionX; ?>, <?php echo $positionY; ?>, <?php echo $width; ?>, <?php echo $height; ?>, <?php echo $gender; ?>, '<?php echo $age; ?>' );
<?php
    }
?>
</script>
<?php
  }
?>
<div id="cvs" class="relative">
  <img id="base"/>
</div>
<?php
}else{
?>
<h1>Manholizer</h1>
<form action="./index.php" method="post">
URL: <input type="text" name="url" size="80"/>
<input type="submit" value="Submit"/>
</form>
<?php
}
?>
</body>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-71901920-1', 'auto');
  ga('send', 'pageview');

</script>
</html>


