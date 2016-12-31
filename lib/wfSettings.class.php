<?php
class wfSettings {
  
  /**
   * Set pre settings in /a/config/pre_settings.yml if exist!
   */
  public static function loadConfigSettings(){
    
    $path_to_file = wfSettings::getAppDir().'/config/settings.yml';
    if(file_exists($path_to_file)){
      $array = sfYaml::load($path_to_file);
      


      // Domain rewrite.
      if(wfArray::isKey($array, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/rewrite')){
        $array = wfArray::set($array, '_rewrite', wfArray::get($array, 'domain/'.wfArray::get($_SERVER, 'SERVER_NAME').'/rewrite'));
      }
      
      
      $array = wfArray::rewrite($array);
    
      foreach ($array as $key => $value) {
        $GLOBALS['sys'][$key] = $value;
      }
    }else{
        exit("File $path_to_file does not exist.");
    }
    
    
    //echo wfArray::get($GLOBALS, 'sys/test');
    
    if(isset($_SESSION['theme'])){
      $GLOBALS['sys']['theme'] = $_SESSION['theme'];
    }
    
  }
  
  public static function getPre($path_to_key = null){
    //$path_to_file = wfSettings::getAppDir().'/a/config/pre_settings.yml';
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
  
  public static function setPre($path_to_key, $value){
    //$path_to_file = wfSettings::getAppDir().'/a/config/pre_settings.yml';
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
  
  public static function get($path_to_key = null){
    exit("New name of wfSettings::get is wfSettings::loadThemeConfigSettings()");
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
      $settings = wfArray::rewrite($settings);
      if($GLOBALS['sys']['cache']){
        file_put_contents($serialize, serialize($settings));
      }
    }
    //echo wfHelp::getServerTime();
    if($path_to_key){
      $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
      eval("\$settings = \$settings$path_to_key;");
    }
    return $settings;
  }
  public static function getModuleSecure($class){
    //Not in user anymore...
    echo 'getModuleSecure...'; exit;
    
    $return = array();
    
    $settings_b = null;
    $filename = dirname(__FILE__).'/../../b/module/'.$class.'/config/secure.yml';
    if(file_exists($filename)){
        $settings_b = sfYaml::load($filename);
    }
    //wfHelp::print_r($settings_b, true);
    
    $settings_a = null;
    //$filename = dirname(__FILE__).'/../../a/module/'.$class.'/config/secure.yml';
    $filename = dirname(__FILE__).'/../../theme/'.wfSettings::getTheme().'/module/'.$class.'/config/secure.yml';
    if(file_exists($filename)){
        $settings_a = sfYaml::load($filename);
    }
    
    if($settings_b && $settings_a){
        $return = array_merge($settings_b, $settings_a);
    }elseif($settings_b){
        $return = $settings_b;
    }elseif($settings_a){
        $return = $settings_a;
    }
    return $return;
    
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
    //$path_to_file = wfSettings::getAppDir().'/a/cache/'.$class.'_settings.yml.serialize';
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
      //$filename = dirname(__FILE__).'/../../a/module/'.$class.'/config/'.$file.'.yml';
      $filename = dirname(__FILE__).'/../../theme/'.wfSettings::getTheme().'/module/'.$class.'/config/'.$file.'.yml';
      if(file_exists($filename)){
          $settings_a = sfYaml::load($filename);
      }
      if($settings_b && $settings_a){
          //$return = array_merge($settings_b, $settings_a);
          $return = wfArray::mergeMultiple($settings_b, $settings_a, 2);
          //wfHelp::yml_dump($return, true);
      }elseif($settings_b){
          $return = $settings_b;
      }elseif($settings_a){
          $return = $settings_a;
      }
      if($GLOBALS['cache']){
        file_put_contents($path_to_file, serialize($return));
      }
      //wfHelp::print_r($return);
      $return = wfArray::rewrite($return);
      
      //Run rewrite in root settings.
      if(wfArray::isKey($return, '_rewrite_globals')){
        wfArray::set($GLOBALS, '_rewrite', wfArray::get($return, '_rewrite_globals'));
        $return = wfArray::setUnset($return, '_rewrite_globals');
        $GLOBALS = wfArray::rewrite($GLOBALS);
      }
      
      
    }
    
    //echo wfHelp::getServerTime();
    
    if($path_to_key){
      $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
      eval("\$return = \$return$path_to_key;");
    }
    
    
    return $return;
    
  }
  
  /** 2_0
   * Application root folder.
   * @return type
   */
  public static function getAppDir(){return $GLOBALS['sys']['app_dir'];}
  
  public static function getWebDir(){
    return str_replace("\\", '/', $GLOBALS['web_dir']);
  }
  
  /**
   * Witch folder a or b action file is running.
   * @return type
   */
  public static function getFolder(){return $GLOBALS['folder'];}
  public static function getClass(){return strtolower($GLOBALS['class']);}
  public static function getMethod(){return strtolower($GLOBALS['method']);}
  
  public static function getYml($file){
    $return = array();
    $settings_b = null;
    //$filename = dirname(__FILE__).'/../../b/module/'.$class.'/config/'.$file.'.yml';
    $filename = wfSettings::getAppDir().'/b/config/'.$file.'.yml';
    if(file_exists($filename)){
        $settings_b = sfYaml::load($filename);
    }
    
    $settings_a = null;
    //$filename = dirname(__FILE__).'/../../a/module/'.$class.'/config/'.$file.'.yml';
    //$filename = wfSettings::getAppDir().'/a/config/'.$file.'.yml';
    $filename = wfSettings::getAppDir().'/theme/'.wfSettings::getTheme().'/config/'.$file.'.yml';
    if(file_exists($filename)){
        $settings_a = sfYaml::load($filename);
    }
    
    if($settings_b && $settings_a){
        $return = array_merge($settings_b, $settings_a);
    }elseif($settings_b){
        $return = $settings_b;
    }elseif($settings_a){
        $return = $settings_a;
    }
    return $return;
    
  }
  
  public static function cleanzzz($str){
    $str = str_replace("\r", '', $str);
    //$str = str_replace(':', '', $str);
    return $str;
  }

  public static function getFileContentzzz($myFile){
      
    $path = $_SERVER["SCRIPT_FILENAME"];
    $path = str_replace('index.php', '', $path);
    $path .= $myFile;
    //echo $path; exit;
    $fh = fopen($path, 'r');
    $theData = fread($fh, filesize($path));
    fclose($fh);
    return $theData;
  }
  
  public static function setLayoutBlank(){
    $settings = $GLOBALS['settings'];
    $settings['layout'] = 'blank';
    $GLOBALS['settings'] = $settings;
    return null;
  }
  public static function setContent($content){
    $GLOBALS['content'] = $content;
    return true;
  }
  public static function getUsers(){
    $filename = wfArray::get($GLOBALS, 'sys/theme_dir').'/config/users.yml';
    if(file_exists($filename)){
      return sfYaml::load($filename);
    }else{
      return null;
    }
  }
  
  /**
   * 
   * @param string $path ex:/a/module/filename.yml
   * @return array
   */
  public static function getSettings($path, $path_to_key = null, $set_globals = true){
    $settings = array();
    $filename = wfArray::get($GLOBALS, 'sys/app_dir').wfSettings::replaceTheme($path);
    // Put content i GLOBALS to speed up server time.
    if(isset($GLOBALS['sys']['yml_files'][$filename])){
      $settings = $GLOBALS['sys']['yml_files'][$filename];
    }else{
      if(file_exists($filename)){
        $settings = sfYaml::load($filename);
        if($set_globals){
          $GLOBALS['sys']['yml_files'][$filename] = $settings;
        }
      }else{
        throw new Exception("Could not find fil $filename.");
      }
    }
    if($path_to_key){
      $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
      eval("if(isset(\$settings$path_to_key)){ \$settings = \$settings$path_to_key; }else{\$settings = null;} ");
    }
    return $settings;
  }
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
    
    // Handle if one is trying to save php code.
    if(strstr($array, '<?php')){
      throw new Exception("Could not save to file $filename because of illegal text.");
    }
    
    file_put_contents($filename, $array);
  }
  
  
  public static function getTheme(){
    return wfArray::get($GLOBALS, 'sys/theme');
  }
  
  
  /**
   * Replace [theme] with current theme.
   * @param type $str
   * @return type
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
    
    if(substr($str, 0, 4)=='yml:'){
      $temp = preg_split('/:/', $str);
      // If third param not set it will be set anyway.
      if(sizeof($temp)==2){
        $temp[2] = null;
      }
      //wfHelp::yml_dump($temp);
      if(sizeof($temp)==3){
        $temp[1] = wfSettings::replaceTheme( trim($temp[1]));
        $temp[2] = trim($temp[2]);
        
        //wfHelp::yml_dump($temp);
        
        return wfSettings::getSettings($temp[1], $temp[2]);
      }else{
        throw new Exception('Params is missing when using yml: in innerHTML.');
      }
    }else{
      return $str;
    }
  }
  
  
  public static function getGlobalsFromString($str){
    if($str == 'globals:sys/plugin_wf_webadmin/menu'){
      //exit($str);
    }
    if(substr($str, 0, 8)=='globals:'){
      $temp = preg_split('/:/', $str);
      $str = wfArray::get($GLOBALS, $temp[1]);
    }
    return $str;
  }
  
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
  public static function isHttps(){
    //SERVER_PROTOCOL: HTTP/1.1
    if(isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.1'){
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
    return new $temp($buto); // Add true to handle in __construct($buto = false) method because of ReflectionClass usage in PluginWfEditor..
  }
  public static function getPluginMethod(){
    return wfArray::get($GLOBALS, 'sys/method');
  }
  
  /**
  <p>
  Rewrite array.
  </p>
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
