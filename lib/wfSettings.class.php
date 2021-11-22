<?php
class wfSettings {
  /**
   * Load ini settings.
   */
  public static function loadIniSettings(){
    $path_to_file = wfSettings::getAppDir().'/config/settings.yml';
    if(file_exists($path_to_file)){
      $array = sfYaml::load($path_to_file);
      if(wfArray::isKey($array, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/ini_set')){
        wfSettings::ini_set(wfArray::get($array, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/ini_set'));
      }
    }else{
      exit("File $path_to_file does not exist.");
    }
  }
  /**
   * Set pre settings in /a/config/pre_settings.yml if exist!
   */
  public static function loadConfigSettings(){
    $path_to_file = wfSettings::getAppDir().'/config/settings.yml';
    if(file_exists($path_to_file)){
      $array = sfYaml::load($path_to_file);
      /**
       * Domain rewrite.
       */
      if(wfArray::isKey($array, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/rewrite')){
        $array = wfArray::set($array, '_rewrite', wfArray::get($array, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/rewrite'));
      }
      $array = wfArray::rewrite($array);
      /**
       * http_user_agent rewrite.
       */
      if(wfArray::isKey($array, 'http_user_agent')){
        $item = wfArray::get($array, 'http_user_agent');
        foreach ($item as $key => $value) {
          if(wfSettings::match_wildcard($key, wfArray::get($_SERVER, 'HTTP_USER_AGENT'))){
            if(wfArray::isKey($array, 'http_user_agent/'.$key.'/rewrite')){
              $array = wfArray::set($array, '_rewrite', wfArray::get($array, 'http_user_agent/'.$key.'/rewrite'));
              $array = wfArray::rewrite($array);
            }
          }
        }
      }
      /**
       * 
       */
      foreach ($array as $key => $value) {
        $GLOBALS['sys'][$key] = $value;
      }
    }else{
      exit("File $path_to_file does not exist.");
    }
    if(isset($_SESSION['theme'])){
      $GLOBALS['sys']['theme'] = $_SESSION['theme'];
    }
    $GLOBALS['sys']['theme_buto_data_dir'] = wfGlobals::getAppDir().'/../buto_data/theme/'.$GLOBALS['sys']['theme'];
  }
  /**
   * Find in string with wildcards *.
   * Example find if "*chrome*" is in string "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36".
   * @param string $sw
   * @param string $haystack
   * @return int 0 if no match and <0 if match.
   */
  public static function match_wildcard($sw, $haystack){
     $regex = str_replace(array("\*", "\?"), array('.*','.'), preg_quote($sw));
     return preg_match('#^'.$regex.'$#is', $haystack);
  }
  /**
   * Not in use?
   */
  public static function getPre($path_to_key = null){
    $path_to_file = wfSettings::getAppDir().'/config/settings.yml';
    if(file_exists($path_to_file)){
      $return = sfYaml::load($path_to_file);
      if($path_to_key){
        $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
        eval("\$return = \$return$path_to_key;");
      }
      return $return;
    }else{
      exit('File '.$path_to_file.' does not exist.');
    }
  }
  /**
   * Not in use?
   */
  public static function setPre($path_to_key, $value){
    $path_to_file = wfSettings::getAppDir().'/config/settings.yml';
    if(file_exists($path_to_file)){
      $return = sfYaml::load($path_to_file);
      $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
      eval("\$return$path_to_key = \$value;");
      file_put_contents($path_to_file, sfYaml::dump($return, 99));
      return $return;
    }else{
      exit('File '.$path_to_file.' does not exist.');
    }
  }
  /**
   * Merge system config and theme config.
   */
  public static function loadThemeConfigSettings($path_to_key = null){
    $serialize = wfSettings::getAppDir().'/theme/'.wfSettings::getTheme().'/cache/settings.yml.serialize';
    if($GLOBALS['sys']['cache'] && file_exists($serialize)){
      $settings = unserialize(file_get_contents($serialize));
    }else{
      $filename = wfArray::get($GLOBALS, 'sys/sys_dir').'/config/settings.yml';
      $settings = sfYaml::load($filename);
      $filename = wfArray::get($GLOBALS, 'sys/theme_dir').'/config/settings.yml';
      if(file_exists($filename)){
        $temp = sfYaml::load($filename);
        if(!isset($temp['default_class'])){
          throw new Exception("Param default_class is not set in $filename!");
        }
        if(!isset($temp['default_method'])){
          throw new Exception("Param default_method is not set in $filename!");
        }
        $settings = array_merge($settings, $temp);
      }else{
        throw new Exception("Could not find $filename!");
      }
      /**
       * ini_set
       */
      if(wfArray::isKey($settings, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/ini_set')){
        wfSettings::ini_set(wfArray::get($settings, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/ini_set'));
      }
      /** 
       * Domain rewrite.
       */
      if(wfArray::isKey($settings, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/rewrite')){
        $settings = wfArray::set($settings, 'rewrite', wfArray::get($settings, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/rewrite'));
      }
      $settings = wfArray::rewrite($settings);
      /**
       * Rewrite from buto_data folder
       */
      $filename = wfGlobals::getAppDir().'/../buto_data/theme/'.wfArray::get($GLOBALS, 'sys/theme').'/settings.yml';
      if(file_exists($filename)){
        $buto_data_settings = sfYaml::load($filename);
        if(wfArray::isKey($buto_data_settings, 'rewrite')){
          $settings = wfArray::set($settings, 'rewrite', wfArray::get($buto_data_settings, 'rewrite'));
          $settings = wfArray::rewrite($settings);
        }
      }
      /**
       * Save cache file
       */
      if($GLOBALS['sys']['cache']){
        file_put_contents($serialize, serialize($settings));
      }
    }
    if($path_to_key){
      $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
      eval("\$settings = \$settings$path_to_key;");
    }
    return $settings;
  }
  /**
   * Set ini params via /config/settings.yml or /theme/folder/folder/config/settings.yml.
   * @param Array $data
   */
  private static function ini_set($data){
    foreach ($data as $key => $value) {
      ini_set($value['name'], $value['value']);
    }
    return null;
  }
  /**
   * Get settings file for a module.
   * @param string $class Class name.
   * @param string $file Optional, if yml file is not settings.yml.
   * @param string $path_to_key Optional, if direct access to array key, example parent_key/child_key.
   * @return array
   */
  public static function getModuleSettings($class, $file = 'settings', $path_to_key = null){
    $return = array();
    $path_to_file = wfSettings::getAppDir().'/theme/'.wfSettings::getTheme().'/cache/'.$class.'_settings.yml.serialize';
    if($GLOBALS['cache'] && file_exists($path_to_file)){
      $return = unserialize(file_get_contents($path_to_file));
    }else{
      $settings_b = null;
      $filename = dirname(__FILE__).'/../../b/module/'.$class.'/config/'.$file.'.yml';
      if(file_exists($filename)){
          $settings_b = sfYaml::load($filename);
      }
      $settings_a = null;
      $filename = dirname(__FILE__).'/../../theme/'.wfSettings::getTheme().'/module/'.$class.'/config/'.$file.'.yml';
      if(file_exists($filename)){
        $settings_a = sfYaml::load($filename);
      }
      if($settings_b && $settings_a){
        $return = wfArray::mergeMultiple($settings_b, $settings_a, 2);
      }elseif($settings_b){
        $return = $settings_b;
      }elseif($settings_a){
        $return = $settings_a;
      }
      if($GLOBALS['cache']){
        file_put_contents($path_to_file, serialize($return));
      }
      $return = wfArray::rewrite($return);
      /**
       * Run rewrite in root settings.
       */
      if(wfArray::isKey($return, '_rewrite_globals')){
        wfArray::set($GLOBALS, '_rewrite', wfArray::get($return, '_rewrite_globals'));
        $return = wfArray::setUnset($return, '_rewrite_globals');
        $GLOBALS = wfArray::rewrite($GLOBALS);
      }
    }
    if($path_to_key){
      $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
      eval("\$return = \$return$path_to_key;");
    }
    return $return;
  }
  /**
   * Get app dir.
   * @return string
   */
  public static function getAppDir(){return $GLOBALS['sys']['app_dir'];}
  /**
   * Get web dir.
   * @return string
   */
  public static function getWebDir(){
    return str_replace("\\", '/', $GLOBALS['web_dir']);
  }
  /**
   *
   */
  public static function getFolder(){return $GLOBALS['folder'];}
  public static function getClass(){return strtolower($GLOBALS['class']);}
  public static function getMethod(){return strtolower($GLOBALS['method']);}
  /**
   * Not in usage?
   */
  public static function setLayoutBlank(){
    $settings = $GLOBALS['settings'];
    $settings['layout'] = 'blank';
    $GLOBALS['settings'] = $settings;
    return null;
  }
  /**
   * Not in usage?
   */
  public static function setContent($content){
    $GLOBALS['content'] = $content;
    return true;
  }
  /**
   * Not in usage?
   */
  public static function getUsers(){
    $filename = wfArray::get($GLOBALS, 'sys/theme_dir').'/config/users.yml';
    if(file_exists($filename)){
      return sfYaml::load($filename);
    }else{
      return null;
    }
  }
  /**
   * Get yml file. 
   * @param string $path ex:/a/module/filename.yml
   * @return array
   */
  public static function getSettings($path, $path_to_key = null, $set_globals = true){
    $settings = array();
    $filename = wfArray::get($GLOBALS, 'sys/app_dir').wfSettings::replaceTheme($path);
    /**
     * Put content i GLOBALS to speed up server time.
     */
    if(isset($GLOBALS['sys']['yml_files'][$filename])){
      $settings = $GLOBALS['sys']['yml_files'][$filename];
    }else{
      if(file_exists($filename)){
        $settings = wfFilesystem::getCacheIfExist($path);
        if($set_globals){
          $GLOBALS['sys']['yml_files'][$filename] = $settings;
        }
      }else{
        /**
         * File not exist.
         */
      }
    }
    /**
     * Get from key.
     */
    if($path_to_key){
      $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
      eval("if(isset(\$settings$path_to_key)){ \$settings = \$settings$path_to_key; }else{\$settings = null;} ");
    }
    /**
     * 
     */
    return $settings;
  }
  /**
   * Get settings as object.
   * @param type $path
   * @param type $path_to_key
   * @return \PluginWfArray
   */
  public static function getSettingsAsObject($path, $path_to_key = null){
    wfPlugin::includeonce('wf/array');
    return new PluginWfArray(wfSettings::getSettings($path, $path_to_key));
  }
  /**
   * Set yml setting file.
   * @param type $path
   * @param type $array
   */
  public static function setSettings($path, $array, $root = false){
    $path = wfSettings::replaceTheme($path);
    if($root){
      $filename = wfSettings::getAppDir().$path;
    }  else {
      $filename = $path;
    }
    if(!file_exists($filename)){
      throw new Exception("Could not find file $filename.");
    }
    $array = sfYaml::dump($array, 99);
    /**
     * Handle if one is trying to save php code.
     */
    if(strstr($array, '<?php')){
      throw new Exception("Could not save to file $filename because of illegal text.");
    }
    file_put_contents($filename, $array);
  }
  /**
   * Get theme.
   * @return string
   */
  public static function getTheme(){
    return wfArray::get($GLOBALS, 'sys/theme');
  }
  /**
   * Replace [theme] with current theme.
   * @param string $str
   * @return string
   */
  public static function replaceTheme($str){
    return str_replace('[theme]', wfSettings::getTheme(), $str);
  }
  /**
   * Add root to path if start with "/theme".
   * @param string $file
   * @return string
   */
  public static function addRoot($file){
    if(substr($file, 0, 6) == '/theme'){
      return wfArray::get($GLOBALS, 'sys/app_dir'). $file;
    }elseif(substr($file, 0, 7) == '/plugin'){
      return wfArray::get($GLOBALS, 'sys/app_dir'). $file;
    }  else {
      return $file;
    }
  }
  /**
   * Replace dir.
   * @param string $str
   * @return string
   */
  public static function replaceDir($str){
    $str = str_replace('[app_dir]', wfArray::get($GLOBALS, 'sys/app_dir'), $str);
    $str = str_replace('[web_dir]', wfArray::get($GLOBALS, 'sys/web_dir'), $str);
    $str = str_replace('[class]', wfArray::get($GLOBALS, 'sys/class'), $str);
    $str = str_replace('[theme]', wfSettings::getTheme(), $str);
    return $str;
  }
  /**
   * Replace [class].
   * @param type $str
   * @return type
   */
  public static function replaceClass($str){
    return str_replace('[class]', wfArray::get($GLOBALS, 'sys/class'), $str);
  }
  /**
   * If string contain yml:file_name:key.
   * @param type $str
   * @return type
   * @throws Exception
   */
  public static function getSettingsFromYmlString($str){
    if(is_array($str)){
      return $str;
    }
    if(is_object($str)){
      return $str;
    }
    if(substr($str, 0, 4)=='yml:'){
      $temp = preg_split('/:/', $str);
      /**
       * If third param not set it will be set anyway.
       */
      if(sizeof($temp)==2){
        $temp[2] = null;
      }
      if(sizeof($temp)==3){
        $temp[1] = wfSettings::replaceTheme( trim($temp[1]));
        $temp[2] = trim($temp[2]);
        return wfSettings::getSettings($temp[1], $temp[2]);
      }else{
        throw new Exception('Params is missing when using yml: in innerHTML.');
      }
    }else{
      return $str;
    }
  }
  /**
   * Run method via string 'method:_plugin_:_method_:data(optional)'.
   * If using this when render elements one must set param settings/method as true because of security reasons.
   * Example of usage: 
   * Param innerHTML in wfDocument.
   * Param options in PluginWfForm_v2.
   * Example of usage with data (colon is replaced with $ in this json string).
   * innerHTML: 'method:_plugin_:_method_:{"id"$ 123, "order"$ [1, 3, 55], "customer"$ {"name"$ "World Inc"}}'
   * @param String $str
   * @return String/Array
   * @throws Exception
   */
  public static function getSettingsFromMethod($str){
    /**
      -
        type: p
        innerHTML: 'method:wf/example:method_test:{"id"$ 1234}'
        settings:
          method: true
     */
    if(is_array($str)){
      return $str;
    }
    if(substr($str, 0, 7)=='method:'){
      $temp = preg_split('/:/', $str);
      /**
       * Check parts.
       */
      if(sizeof($temp)==3){
        $temp[3] = null;
      }
      if(sizeof($temp) != 4){
        throw new Exception('There has to be three parts separated with colon in "'.$str.'".');
      }
      /**
       * Include plugin.
       */
      wfPlugin::includeonce($temp[1]);
      /**
       * Call method.
       */
      $obj = wfSettings::getPluginObj($temp[1]);
      $method = $temp[2];
      $data = $temp[3];
      $data = str_replace("$", ":", $data);
      $data = json_decode($data, true);
      return $obj->$method($data);
    }else{
      return $str;
    }
    return $str;
  }
  /**
   * Get globals from string.
   * @param string $str
   * @return string
   */
  public static function getGlobalsFromString($str){
    if(substr($str, 0, 8)=='globals:'){
      $temp = preg_split('/:/', $str);
      $str = wfArray::get($GLOBALS, $temp[1]);
    }
    return $str;
  }
  /**
   * Get server from string.
   * @param string $str
   * @return string
   */
  public static function getServerFromString($str){
    if(substr($str, 0, 7)=='server:'){
      $temp = preg_split('/:/', $str);
      $str = wfArray::get($_SERVER, $temp[1]);
    }
    return $str;
  }
  /**
   * Get url.
   * @return string
   */
  public static function getUrl(){
    $temp = null;
    if(isset($_SERVER['REQUEST_URI'])){
      //Apache
      $temp = $_SERVER['REQUEST_URI'];
    }else{
      //Windows
      $temp = $_SERVER["HTTP_X_ORIGINAL_URL"];
    }
    return $temp;
  }
  /**
   * Get http address.
   * @param boolean $root
   * @return string
   */
  public static function getHttpAddress($root = false){
    $protocol = 'http://';
    if(self::isHttps()){
      $protocol = 'https://';
    }
    if(!$root){
      return $protocol.$_SERVER['HTTP_HOST'].self::getUrl();
    }else{
      return $protocol.$_SERVER['HTTP_HOST'];
    }
  }
  /**
   * Check if https.
   * @return boolean
   */
  public static function isHttps(){
    if(isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'HTTPS/1.1'){
      return true;
    }else if(isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1'){
      return false;
    }else if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on'){
      return true;
    }else{
      return false;
    }
  }
  /**
   * Get plugin object.
   * @param string $plugin
   * @return object
   */
  public static function getPluginObj($plugin = null, $buto = true){
    if($plugin){
      $temp = 'plugin/'.$plugin;
    }else{
      $temp = 'plugin/'.wfArray::get($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/plugin');
    }
    $temp = str_replace('/', ' ', $temp);
    $temp = ucwords($temp);
    $temp = str_replace(' ', '', $temp);
    /**
     * Add true to handle in __construct($buto = false) method because of ReflectionClass usage in PluginWfEditor..
     */
    return new $temp($buto);
  }
  /**
   * Get plugin method.
   * @return string
   */
  public static function getPluginMethod(){
    return wfArray::get($GLOBALS, 'sys/method');
  }
  /**
   * Rewrite array.
   * @param array $yml
   * @param array $rewrite
   * @return array
   */
  public static function rewrite($yml, $rewrite){
    if(is_array($rewrite)){
      foreach ($rewrite as $key => $value) {
        $yml = wfArray::set($yml, $key, $value);
      }
    }
    return $yml;
  }
}
?>
