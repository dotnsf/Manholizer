<?php
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
<meta name="keywords" content="Manhole,マンホール,画像認識,顔認識,人工知能,IBM,Watson,Visual Recognition"/>

<script type="text/javascript" src="//code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
var ratio = 0;
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
    var i_x = x * ratio - ( 112.0 * ratio );
    var i_y = y * ratio - ( 101.0 * ratio );
    var i_w = 349.0 * ratio;
    var i_h = 349.0 * ratio;

    //. ageRangeの位置
    var a_x = i_x + ( 158.0 * ratio );
    var a_y = i_y + ( 21.0 * ratio );
    var a_w = 34.0 * ratio;
    var a_h = 16.0 * ratio;

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

  $(".imgSelect").css({'display':'none'});
}

$(function(){
  window.addEventListener( 'dragover', function( event ){
    event.preventDefault(); // ブラウザのデフォルトの画像表示処理をOFF
  }, false );

  window.addEventListener( 'drop', function( event ){
    event.preventDefault(); // ブラウザのデフォルトの画像表示処理をOFF
    var file = event.dataTransfer.files[0];
    // ファイルタイプ(MIME)で対応しているファイルか判定
    if( !file.type.match( /image\/\w+/ ) ){
      alert( '画像ファイル以外は利用できません' );
      return;
    }

    var reader = new FileReader(); 
    reader.onload = function(){
      var img = new Image();
      img.src = reader.result;
      img.addEventListener( 'load', function(){
        $("#base").attr( 'src', img.src );

        var w = img.width;
        var h = img.height;
//        if( w > h ){
          ratio = 800 / w;
          h = Math.round( 800 * h / w );
          w = 800;
//        }else{
//          ratio = 800 / h;
//          w = Math.round( 800 * w / h );
//          h = 800;
//        }

        $("#base").attr( 'width', w );
        $("#base").attr( 'height', h );
        $(".relative").attr( 'width', w );

        imageFileUpload( file );
      }, false );
    };
    reader.onerror = function( e ){
      console.log( 'error: ' + e );
    };
    reader.readAsDataURL( file );
  }, false );

  // ファイルを選択した場合にもプレビュー表示させる
  var selfInput = $(this).find( 'input[type=file]' );
  selfInput.change( function(){
    var file = $(this).prop('files')[0];
    if( !file.type.match( /image\/\w+/ ) ){
      alert( '画像ファイル以外は利用できません' );
      return;
    }

    var reader = new FileReader();
    if( this.files.length ){
      if( file.type.match('image.*') ){
        reader.onload = function(){
          var img = new Image();
          img.src = reader.result;
          img.addEventListener( 'load', function(){
            $("#base").attr( 'src', img.src );

            var w = img.width;
            var h = img.height;
//            if( w > h ){
              ratio = 800 / w;
              h = Math.round( 800 * h / w );
              w = 800;
//            }else{
//              ratio = 800 / h;
//              w = Math.round( 800 * w / h );
//              h = 800;
//            }

            $("#base").attr( 'width', w );
            $("#base").attr( 'height', h );
            $(".relative").attr( 'width', w );

            imageFileUpload( file );
          }, false );
        };
        reader.onerror = function( e ){
          console.log( 'error: ' + e );
        };
        reader.readAsDataURL( file );
      }else{
        if( 0 < selfImg.size() ){
          return;
        }
      }
    }
  });
});

// ファイルアップロード
function imageFileUpload( f ){
  var formData = new FormData();
  formData.append( 'image', f );
  $.ajax({
    type: 'POST',
    contentType: false,
    processData: false,
    url: './upload.php',
    data: formData,
    dataType: 'json',
    success: function( data ){
      //console.log( data );
      // メッセージ出したり、DOM構築したり。
      if( data.result.images ){
        arr = new Array();
        for( i = 0; i < data.result.images.length; i ++ ){
          var image = data.result.images[i];
          for( j = 0; j < image.faces.length; j ++ ){
            var imageFace = image.faces[j];
            var positionX = imageFace.face_location.left;
            var positionY = imageFace.face_location.top;
            var width = imageFace.face_location.width;
            var height = imageFace.face_location.height;
            var age_max = imageFace.age.max;
            var age_min = imageFace.age.min;
            var age = ( age_min ? age_min : '' ) + '-' + ( age_max ? age_max : '' );
            var gender_s = imageFace.gender.gender;
            var gender = ( gender_s == 'MALE' ? 1 : 2 );
            addFace( j, positionX, positionY, width, height, gender, age );
          }
        }
        drawFace();
      }else if( data.result.status == "ERROR" ){
        console.log( data.result.statusInfo );
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      // メッセージ出したり、DOM構築したり。
      console.log( "XMLHttpRequest: " + XMLHttpRequest.status );
      console.log( "textStatus: " + textStatus );
      console.log( "errorThrown: " + errorThrown.message );
    }
  });
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

html, body, {
  height: 100%;
  margin: 0px;
  padding: 0px
}

</style>
</head>
<body>
<h1>Manholizer</h1>
<div class="imgSelect">
<input type="file" name="file1"/>
</div>
<hr/>
<div id="cvs" class="relative">
  <img id="base"/>
</div>
</body>
</html>


