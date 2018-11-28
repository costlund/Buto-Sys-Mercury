<?php
class wfRequest {
  /**
   * Get all params.
   * @return Array Get and Post variables merged.
   */
  public static function getAll(){
    $all = array();
    foreach ($_GET as $key => $value) {
      $all[$key] = wfRequest::get($key);
    }
    foreach ($_POST as $key => $value) {
      $all[$key] = wfRequest::get($key);
    }
    return $all;
  }
  public static function set($param, $value){
    $_GET[$param] = $value;
    $_POST[$param] = $value;
    return null;
  }
  public static function get($param, $if_not_set = null){
    $return = null;
    if(isset($_GET[$param])){
      $return = $_GET[$param];
    }elseif(isset($_POST[$param])){
      if(get_magic_quotes_gpc()){
        if(!is_array($_POST[$param])){
          $return = stripslashes($_POST[$param]);
        }else{
          // Take care of the array...
          $return = $_POST[$param];
        }
      }else{
        $return = $_POST[$param];
      }
    }else{
      $return = $if_not_set;
    }
    if(!is_array($return)){
      $return = trim($return);
    }
    return $return;
  }
  public static function getInt($param){
    $i = wfRequest::get($param);
    if(!wfRequest::isInteger($i)){$i = null;}
    return $i;
  }
  private static function isInteger($input){
      return(ctype_digit(strval($input)));
  }  
  
  public static function isPost(){
    if($_SERVER['REQUEST_METHOD']=='POST'){
      return true;
    }else{
      return false;
    }
  }
  
  public static function rewrite(){
    //$class = null;
    //$method = null;
    if(isset($_SERVER['REQUEST_URI'])){
        //Apache
        $temp = $_SERVER['REQUEST_URI'];
    }else{
        //Windows
        $temp = $_SERVER["HTTP_X_ORIGINAL_URL"];
    }
    if($temp=='/'){
      $GLOBALS['sys']['class'] = $GLOBALS['sys']['settings']['default_class'];
      $GLOBALS['sys']['method'] = $GLOBALS['sys']['settings']['default_method'];
    }else{
      /**
       * If question character exist and no slash before we add one.
       */
      if(strstr($temp, '?') && !strstr($temp, '/?')){
        $temp = str_replace('?', '/?', $temp);
      }
      /**
       * 
       */
      $temp = str_replace('?', '/', $temp);
      $temp = str_replace('=', '/', $temp);
      $temp = str_replace('&', '/', $temp);
      $temp = explode('/', $temp);
      if(sizeof($temp)==2){ //Handle one param with no name ex. "localhost/abc/".
        $_GET['one_param'] = $temp[1];
        if(true || $settings['default_class']!='doc'){
            $GLOBALS['sys']['class'] = $GLOBALS['sys']['settings']['default_class'];
            $GLOBALS['sys']['method'] = strtolower($temp[1]);
        }
      }else{ //Handle class/method/param1/x/param2/y
        foreach ($temp as $key => $value) {
          if($key==1){      //Class
            $GLOBALS['sys']['class'] = strtolower($temp[1]);
          }elseif($key==2){ //Method
            $GLOBALS['sys']['method'] = strtolower(($temp[2]));
          }elseif($key>2){  //Params
            if ($key % 2 != 0) {
              if(isset($temp[$key+1])){
                $_GET[$value] = $temp[$key+1];
              }else{
                $_GET[$value] = null;
              }
            }
          }
        }
      }
    }
    /**
     * Trying to solve issue when using url below where no method is set by setting method to index.
     * http://www.site.com/folder/?Param=1234
     * http://www.site.com/folder/(index)?Param=1234
     */
    if(sizeof($_GET) > 0 && !$GLOBALS['sys']['method']){
      $GLOBALS['sys']['method'] = 'index';
    }
    return null;
  }
  /**
   * Function to split params to only check for values in specific position.
   * @return array
   */
  public static function splitParams(){
    $temp = array();
    if(isset($_GET)){
      foreach ($_GET as $key => $value) {
        $temp[] = $key;
        $temp[] = $value;
      }
    }
    return $temp;
  }
  
}

?>
