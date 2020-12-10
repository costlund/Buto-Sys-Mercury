<?php
class wfRequest {
  public static $trim = true;
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
      if(wfRequest::$trim){
        $return = trim($return);
      }
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
    $url = '';
    /**
     * 
     */
    if(isset($_SERVER['REQUEST_URI'])){
        //Apache
        $url = $_SERVER['REQUEST_URI'];
    }else{
        //Windows
        $url = $_SERVER["HTTP_X_ORIGINAL_URL"];
    }
    /**
     * 
     */
    if($url=='/'){
      $GLOBALS['sys']['class'] = $GLOBALS['sys']['settings']['default_class'];
      $GLOBALS['sys']['method'] = $GLOBALS['sys']['settings']['default_method'];
    }else{
      /**
       * If question character exist and no slash before we add one.
       */
      if(strstr($url, '?') && !strstr($url, '/?')){
        $url = str_replace('?', '/?', $url);
      }
      /**
       * 
       */
      $url = str_replace('?', '', $url);
      $url = str_replace('=', '/', $url);
      $url = str_replace('&', '/', $url);
      $url = explode('/', $url);
      if(sizeof($url)==2){
        /**
         * Handle one param with no name ex. "localhost/abc/".
         */
        $_GET['one_param'] = $url[1];
        $GLOBALS['sys']['class'] = $GLOBALS['sys']['settings']['default_class'];
        $GLOBALS['sys']['method'] = strtolower($url[1]);
      }else{
        /**
         * Handle class/method/param1/x/param2/y 
         */
        foreach ($url as $key => $value) {
          if($key==1){      
            /**
             * class 
             */
            $GLOBALS['sys']['class'] = strtolower($url[1]);
          }elseif($key==2){
            /**
             * method
             */
            $GLOBALS['sys']['method'] = strtolower(($url[2]));
          }elseif($key>2){  
            /**
             * params
             */
            if ($key % 2 != 0) {
              if(isset($url[$key+1])){
                $_GET[$value] = $url[$key+1];
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
    /**
     * 
     */
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
