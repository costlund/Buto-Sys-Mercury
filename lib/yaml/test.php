<?php

//echo log(26, 10);exit;
//echo pow(2, 3); exit;


require('sfYaml.php');
$temp = sfYaml::load(dirname(__FILE__).'/test.yml');

echo '<pre>';
print_r($temp);


echo json_encode($temp);


//echo '<pre>';
//print_r(get());
//function get(){
//  $content = getFileContent('settings.txt');
//  $temp = explode("\n", $content);
//  $array = array();
//  foreach ($temp as $key => $value){
//    $str = trim($value);
//    if(strlen($str) && substr($str, 0, 1)!='#'){
//      $str = clean($str);
//      $temp2 = explode(":", $str);
//      $str_value = null;
//      if(isset($temp2[1])){
//        $str_value = trim($temp2[1]);
//      }
//      $array[$temp2[0]] = $str_value;
//    }
//  }
//  return $array;
//}
//function clean($str){
//  $str = str_replace("\r", '', $str);
//  return $str;
//}
//function getFileContent($myFile) 
//{
//  $fh = fopen($myFile, 'r');
//  $theData = fread($fh, filesize($myFile));
//  fclose($fh);
//  return $theData;
//}



?>