<?php
# http://www.alchemyapi.com/api/register.html
$apikey = '(Your AlchemyAPI Key)';

// Bluemix 環境であれば上記の設定は不要
if( getenv( 'VCAP_SERVICES' ) ){
  $vcap = json_decode( getenv( 'VCAP_SERVICES' ), true );
  
  $credentials1 = $vcap['alchemy_api'][0]['credentials'];
  if( $credentials1 != NULL ){
    $apikey = $credentials1['apikey'];
  }
}
?>

