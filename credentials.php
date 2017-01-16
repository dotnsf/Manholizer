<?php
# https://www.ibm.com/watson/developercloud/visual-recognition.html
$apikey = '(Your Visual Recognition Key)';

// Bluemix 環境であれば上記の設定は不要
if( getenv( 'VCAP_SERVICES' ) ){
  $vcap = json_decode( getenv( 'VCAP_SERVICES' ), true );
  
  $credentials1 = $vcap['watson_vision_combined'][0]['credentials'];
  if( $credentials1 != NULL ){
    $apikey = $credentials1['apikey'];
  }
}
?>

