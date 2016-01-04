<?php
require( './credentials.php' );

$name = $_FILES["image"]["name"];
$mimetype = $_FILES["image"]["type"];
$filesize = $_FILES["image"]["size"];
$tmpname = $_FILES["image"]["tmp_name"];

if( $tmpname ){
  try{
    $fp = fopen( $tmpname, "rb" );
    $imgdata = fread( $fp, $filesize );
    fclose( $fp );

    $apiurl = 'http://access.alchemyapi.com/calls/image/ImageGetRankedImageFaceTags?apikey=' . $apikey . '&outputMode=json&knowledgeGraph=1&imagePostMode=raw';
    $options = array( 'http' => array(
      'method' => 'POST',
      'content' =>  $imgdata
    ));
    $json_text = file_get_contents( $apiurl, false, stream_context_create( $options ) );

    header( 'HTTP/1.1 200' );
    print( '{"status":"OK","result":' . $json_text .'}' );
  }catch( Exception $e ){
    header( 'HTTP/1.1 500' );
    print( '{"status":"NG","message":"' . $e->getMessage() . '"}' );
  }
}else{
  header( 'HTTP/1.1 500' );
  print( '{"status":"NG","message":"No tmpname"}' );
}
?>

