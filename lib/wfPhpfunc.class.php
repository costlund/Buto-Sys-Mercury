<?php
class wfPhpfunc{
  public static function str_replace($search, $replace, $subject){
    if(is_null($replace)){
      $replace = '';
    }
    return str_replace($search, $replace, $subject);
  }
  public static function strstr($haystack, $needle){
    if(is_null($haystack)){
      $haystack = '';
    }
    return strstr($haystack, $needle);
  }
  public static function substr($string, $offset, $length = null){
    if(is_null($string)){
      $string = '';
    }
    return substr($string, $offset, $length);
  }
}
