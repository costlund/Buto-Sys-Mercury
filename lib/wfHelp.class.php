<?php
class wfHelp {
  public static function isLocalhost(){if($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '89.221.253.94'){return true;}else{return false;}} // Why not REMOTE_HOST, 150219???
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
        if(gettype($arr)=='object'){$arr = $arr->get();}
        echo sfYaml::dump($arr, 99);      
          echo '</pre>'."\n";
        if($exit){exit();}
      }
  }
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
  public static function echoecho($str, $exit = false){
      if(wfHelp::isLocalhost()){
          echo $str;
          if($exit){exit();}
      }
      return null;
  }
  public static function helloWorld(){
      return wfHelp::echoecho('Hello world.', true);
  }
  
  public static function detectScreen(){
      if(isset($_COOKIE['screen'])){
          if($_COOKIE['screen']=='mobile'){
              return 'mobile';
          }else{
              return 'pc';
          }
      }else{
        if(strstr($_SERVER['HTTP_USER_AGENT'], 'Android') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone')){
            return 'mobile';
        }else{
            return 'pc';
        }
      }
  }
  
  public static function detectScreenViaCookie(){
      if(isset($_COOKIE['screen'])){
          if($_COOKIE['screen']=='mobile'){
              return 'mobile';
          }else{
              return 'pc';
          }
      }else{
        return null;
      }
  }
  public static function detectScreenViaBrowser(){
    if(strstr($_SERVER['HTTP_USER_AGENT'], 'Android') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone')){
        return 'mobile';
    }else{
        return 'pc';
    }
  }
  
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
