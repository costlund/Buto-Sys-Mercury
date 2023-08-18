<?php
class wfPhpfunc{
  public static function str_replace($search, $replace, $subject){
    if(is_null($replace)){
      $replace = '';
    }
    if(is_null($subject)){
      $subject = '';
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
    /**
     * 
     */
    if(is_array($string)){
      throw new Exception(__CLASS__.'::'.__FUNCTION__.' says: Param $string can not be an array!');
    }
    /**
     * 
     */
    if(is_null($string)){
      $string = '';
    }
    /**
     * 
     */
    if(is_null($length)){
      /**
       * Created for php version 7.4.21.
       */
      return substr($string, $offset);
    }else{
      /**
       * 
       */
      return substr($string, $offset, $length);
    }
  }
  public static function strlen($string){
    /**
     * 
     */
    if(is_null($string)){
      $string = '';
    }
    /**
     * 
     */
    return strlen($string);
  }
}
