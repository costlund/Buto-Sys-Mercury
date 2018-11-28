<?php
class wfHelp {
  /**
   * Check if host is localhost by name, address or theme/config/settings.yml(is_localhost=true).
   * @return boolean
   */
  public static function isLocalhost(){
    $is_localhost = false;
    if(isset($GLOBALS['sys']['settings']['is_localhost']) && $GLOBALS['sys']['settings']['is_localhost']){
      $is_localhost = true;
    }
    if(strstr(wfArray::get($_SERVER, 'HTTP_HOST'), 'localhost') || wfArray::get($_SERVER, 'REMOTE_ADDR') == '127.0.0.1' || $is_localhost){
      return true;
    }else{
      return false;
    }
  }
  public static function print_r($arr, $exit = false){
      if(wfHelp::isLocalhost()){
          echo "\n".'<pre>'."\n";
          print_r($arr);
          echo '</pre>'."\n";
          if($exit){exit();}
      }
      return null;
  }
  /**
   * Dump an array.
   * @param type $arr
   * @param type $exit
   * @param type $color
   * @param type $lable
   */
  public static function yml_dump($arr, $exit = false, $color = null, $lable = null){
      if(wfHelp::isLocalhost()){
        echo "\n".'<pre style="color:'.$color.'">'."\n";
        if($lable){
          echo '<b>'.$lable.'</b><br>';
        }
        if(gettype($arr)=='object'){
          $arr = $arr->get();
        }
        echo sfYaml::dump($arr, 99);      
        echo '</pre>'."\n";
        if($exit){
          exit();          
        }
      }
  }
  /**
   * Dump data in a textarea.
   * @param type $data
   * @param Boolean $exit
   */
  public static function textarea_dump($data, $exit = false){
    if(wfHelp::isLocalhost()){
      if(gettype($data)=='object'){
        $data = sfYaml::dump($data->get(), 99);
      }elseif(gettype($data)=='array'){
        $data = sfYaml::dump($data, 99);
      }
      $textarea = wfDocument::createHtmlElement('textarea', $data, array('style' => 'width:100%;height:300px'));
      wfDocument::renderElement(array($textarea));
      if($exit){
        exit();          
      }
    }
  }
  /**
   * Dump value into pre element if localhost.
   * @param string/object/array $value
   * @param bool $exit If exit in code.
   */
  public static function dump($value, $exit = false){
    if(wfHelp::isLocalhost()){
      if(gettype($value)=='object'){
        $value = sfYaml::dump($value->get(), 99);
      }elseif(gettype($value)=='array'){
        $value = wfHelp::getYmlDump($value);
      }
      $element = wfDocument::createHtmlElement('pre', $value);
      wfDocument::renderElement(array($element));
      if($exit){
        exit();          
      }
    }
  }
  /**
   * Get an array as yml data.
   * @param type $arr
   * @param type $show_for_all
   * @return string
   */
  public static function getYmlDump($arr, $show_for_all = true){
      if($show_for_all || wfHelp::isLocalhost()){
        $arr = sfYaml::dump($arr, 99);
        if($arr == 'null'){
          return '';
        }else{
          return $arr;
        }
        //return str_replace("\n", '<br>', sfYaml::dump($arr, 99));      
      }else{return '';}
  }
  /**
   * Do an echo depending on is localhost.
   * @param type $str
   * @param type $exit
   * @return type
   */
  public static function echoecho($str, $exit = false){
      if(wfHelp::isLocalhost()){
          echo $str;
          if($exit){exit();}
      }
      return null;
  }
  /**
   * Hello World!
   * @return type
   */
  public static function helloWorld(){
      return wfHelp::echoecho('Hello world.', true);
  }
  /**
   * Check if boolean or array.
   * @param type $s
   * @return string
   */
  public static function handleBoolean($s){
        if(is_bool($s)){
            return ($s)?'true':'false';
        }else{
          if(is_array($s)){
            return 'Array';
          }else{
            return $s;
          }
        }
  }
  
  public static function getHttpHost(){
      return $_SERVER['HTTP_HOST'];
  }
  public static function getHttpAdress(){
    if(wfArray::get($_SERVER, 'HTTPS')){
      if(wfArray::get($_SERVER, 'HTTPS')=='off'){
        $http = 'http://';
      }else{
        $http = 'https://';
      }
    }else{
      $http = 'http://';
    }
    return $http.$_SERVER['HTTP_HOST'];
  }
  public static function sortMultipleArray(&$array, $key, $desc = false){
    $sorter=array();
    reset($array);
    foreach ($array as $ii => $va) {
        if(isset($va[$key])){
          $sorter[$ii]=$va[$key];
        }else{
          $sorter[$ii]=null;
        }
    }
    if($desc){
      arsort($sorter);
    }else{
      asort($sorter);
    }
    $ret=array();
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    return $ret;
  }
  public static function getServerTime($exit = false){
      if(wfHelp::isLocalhost()){
          return (microtime(true)-$GLOBALS['microtime']);
          //if($exit){exit('<br>Exit in wfHelp::getServerTime().');}
      }else{
          return (microtime(true)-$GLOBALS['microtime']);
      }
      return null;
  }
  
}

?>
