<?php
class wfRequest {
  public static $trim = true;
  public static $url_i18n = '';
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
  public static function setAll($data){
    $_GET = $data;
    $_POST = $data;
    return null;
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
      $return = $_POST[$param];
    }else{
      $return = $if_not_set;
    }
    if(!is_array($return)){
      if(wfRequest::$trim){
        $return = wfSettings::trim($return);
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
      /**
       * Apache
       */
      $url = $_SERVER['REQUEST_URI'];
    }else{
      /**
       * Windows
       */
      $url = $_SERVER["HTTP_X_ORIGINAL_URL"];
    }
    /**
     * Handle if url (REQUEST_URI or HTTP_X_ORIGINAL_URL) start with '/?' or '?', we add default class and method.
     */
    if(substr($url, 0, 2) == '/?' || substr($url, 0, 1) == '?'){
      $url = '/'.$GLOBALS['sys']['settings']['default_class'].'/'.$GLOBALS['sys']['settings']['default_method'].$url;
    }
    /**
     * Handle if.
     * /method?x=1
     * Change to.
     * /class/method?x=1
     */
    if(strstr($url, '?')){
      if(substr_count(substr($url, 0, strpos($url, '?')), '/')==1){
        $url = '/'.$GLOBALS['sys']['settings']['default_class'].$url;
      }
    }
    /**
     * 
     */
    $GLOBALS['sys']['class'] = $GLOBALS['sys']['settings']['default_class'];
    $GLOBALS['sys']['method'] = $GLOBALS['sys']['settings']['default_method'];
    /**
     * 
     */
    if($url=='/'){
      /**
       * No params is sent.
       */
    }else{
      /**
       * If question character exist and no slash before we add one.
       */
      if(strstr($url, '?') && !strstr($url, '/?')){
        $url = str_replace('?', '/?', $url);
      }
      /**
       * Handle slash.
       */
      if(strstr($url, '?')){
        $url = substr($url, 0, strpos($url, '?')).str_replace('/', '_____SLASH_____', strstr($url, '?'));
      }
      /**
       * Replace
       */
      $url = str_replace('?', '', $url);
      $url = str_replace('=', '/', $url);
      $url = str_replace('&', '/', $url);
      $url = str_replace("'", '', $url);
      $url = explode('/', $url);
      /**
       * Handle slash.
       */
      foreach($url as $k => $v){
        $url[$k] = str_replace('_____SLASH_____', '/', $v);
      }
      /**
       * i18n
       * If url like /la-en and in theme settings i18n/languages/0/en.
       */
      if(wfGlobals::get('settings/i18n/url') && wfGlobals::get('settings/i18n/url/'.$url[1])){
        /**
         * Set globas
         */
        wfGlobals::set('settings/i18n/language', wfGlobals::get('settings/i18n/url/'.$url[1]));
        /**
         * 
         */
        wfRequest::$url_i18n = '/'.$url[1];
        /**
         * Set session.
         */
        wfI18n::setLanguage(wfGlobals::get('settings/i18n/url/'.$url[1]));
        /**
         * Remove value form url
         */
        unset($url[1]);
        /**
         * Make $url array not associate again.
         */
        $temp = array();
        foreach($url as $v){
          $temp[] = $v;
        }
        $url = $temp;
      }
      /**
       * 
       */
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
                $_GET[$value] = urldecode($url[$key+1]);
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
