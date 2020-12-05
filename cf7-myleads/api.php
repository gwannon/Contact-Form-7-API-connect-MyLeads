<?php 

/* LIBs ---------------------------------------- */

function insertLead($payload_data) { //Inserta un lead
  $token = getToken();
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => MY_LEADS_API_URL."/campaign/lead",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $payload_data,
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer ".$token,
      "Content-Type: application/json",
      "cache-control: no-cache"
    ),
  ));
  $response = curl_exec($curl); 
  $err = curl_error($curl);
  $curl_info = curl_getinfo($curl);
  curl_close($curl); 

  if ($err) {
    writeErrorLog ("INSERTLEAD CURL ERROR", $payload_data, $err); 
  } else if ($curl_info['http_code'] != '201') {
    writeErrorLog ("INSERTLEAD API ERROR", $payload_data, $response); 
  } else {
    writeLog ("INSERTLEAD API OK", $payload_data, $response);     
  }

  return json_decode($response);
}

function getToken() { //Logea al usuario u genera un token
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => MY_LEADS_API_URL."/auth",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "Username=".MY_LEADS_API_USER."&Password=".MY_LEADS_API_PASS,
    CURLOPT_HTTPHEADER => array (
      "Content-Type: application/x-www-form-urlencoded",
      "cache-control: no-cache"
    ),
  ));
  
  $response = curl_exec($curl); 
  $err = curl_error($curl);
  curl_close($curl);
  
  if ($err) {
    writeErrorLog ("AUTH CURL ERROR", "", $response); 
  } 
  
  return json_decode($response)->response->Token;
}


function writeLog ($title, $data, $response) {
  $fp = fopen(dirname(__FILE__)."/logs/log.txt", 'a+');
  fwrite($fp, date("Y-m-d H:i:s")."|".$title."|".$response."|".$data);
  fwrite($fp, "-------------------------------------\n");
  fclose($fp);
  return;
}

function writeErrorLog ($title, $data, $response) {
  $fp = fopen(dirname(__FILE__)."/logs/error.txt", 'a+');
  fwrite($fp, date("Y-m-d H:i:s")."|".$title."|".$response."|".$data."\n");
  fwrite($fp, "-------------------------------------\n");
  fclose($fp);
  if(MY_LEADS_EMAIL != '') {
    wp_mail(MY_LEADS_EMAIL, $title, $data.$response);
  }
  return;
}

function transformString ($string) {
  $string = mb_strtoupper(chop($string));
  return $string;
}

?>
