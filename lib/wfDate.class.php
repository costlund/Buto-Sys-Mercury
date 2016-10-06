<?php
class wfDate {
  public static function get($date = null, $format = null){
    if($date){
      $date_time = new DateTime($date);
    }else{
      $date_time = new DateTime();
    }
    if($format == null){
      $format = wfDate::format();
    }
    if($format){
      return $date_time->format($format);
    }else{
      return $date_time->format(DateTime::ISO8601);
    }
  }
  public static function format(){
    $format = null;
    if(wfArray::get($_SESSION, 'settings/date/format/date')){
      $format = wfArray::get($_SESSION, 'settings/date/format/date');
    }elseif(wfArray::get($GLOBALS, 'sys/date/format/date')){
      $format = wfArray::get($GLOBALS, 'sys/date/format/date');
    }elseif(wfArray::get($GLOBALS, 'sys/settings/date/format/date')){
      $format = wfArray::get($GLOBALS, 'sys/settings/date/format/date');
    }
    return $format;
  }
  
  public static function diff($interval, $date_from = null, $date_to = null){
    if($interval && $date_from && $date_to){
      return wfDate::s_datediff($interval, $date_from, $date_to);
    }else{
      return null;
    }
  }
  
  
  private static function s_datediff( $str_interval, $dt_menor, $dt_maior, $relative=false){

       if( is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
       if( is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);

       $diff = date_diff( $dt_menor, $dt_maior, ! $relative);
       
       switch( $str_interval){
           case "y": 
               $total = $diff->y + $diff->m / 12 + $diff->d / 365.25; break;
           case "m":
               $total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
               break;
           case "d":
               $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
               break;
           case "h": 
               $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
               break;
           case "i": 
               $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
               break;
           case "s": 
               $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
               break;
          }
       if( $diff->invert)
               return -1 * $total;
       else    return $total;
  }  
  
  
}
