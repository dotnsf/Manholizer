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

    #$apiurl = 'http://access.alchemyapi.com/calls/image/ImageGetRankedImageFaceTags?apikey=' . $apikey . '&outputMode=json&knowledgeGraph=1&imagePostMode=raw';
    $apiurl = 'https://gateway-a.watsonplatform.net/visual-recognition/api/v3/detect_faces?apikey=' . $apikey . '&version=2016-05-20';
    $boundary = '-------------------'.time();
    $contentType = 'Content-Type: multipart/form-data; boundary=' . $boundary;
    $data = "--$boundary" . "\r\n";
    $data .= sprintf( 'Content-Disposition: form-data; name="%s"; filename="%s"%s', 'UploadFile', "image.png", "\r\n" );
    $data .= 'Content-Type: image/png' . "\r\n\r\n";
    $data .= $imgdata . "\r\n";
    $data .= "--$boundary--" . "\r\n";

    $headers = array( $contentType, 'Content-Length: '.strlen( $data ) );
    $options = array( 'http' => array(
      'method' => 'POST',
      'content' =>  $data,
      'header' => implode( "\r\n", $headers )
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

