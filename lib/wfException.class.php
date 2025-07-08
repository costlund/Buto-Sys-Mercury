<?php
class wfException{
  public static function getText($class, $function, $message){
    return "$class::$function says: $message";
  }
  public static function getException($class, $function, $message){
    throw new Exception(wfException::getText($class, $function, $message));
  }
}
