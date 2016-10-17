<?php
class wfPlugin {
    

  
  
  /**
   * Get plugin settings from theme settings.yml param plugin_modules.
   * Get only one...
   * @param string $plugin If call from widget.
   * @return array
   */
  public static function getModuleSettings($plugin = null){
    $settings = null;
    if(!$plugin){
      $plugin = wfArray::get($GLOBALS, 'sys/plugin');
    }
    foreach (wfArray::get($GLOBALS, 'sys/settings/plugin_modules') as $key => $value) {
      if(wfArray::get($value, 'plugin') == $plugin){
        $settings = wfArray::get($value, 'settings'); break;
      }
    }
    return $settings;
  }
  /**
   * Get plugins settings from theme settings.yml param plugin_modules.
   * @param type $plugin
   * @return type
   */
  public static function getModulesSettings($plugin){
    $settings = array();
    foreach (wfArray::get($GLOBALS, 'sys/settings/plugin_modules') as $key => $value) {
      if(wfArray::get($value, 'plugin') == $plugin){
        $settings[$key] = wfArray::get($value, 'settings');
      }
    }
    return $settings;
  }
  
  public static function includeonce($plugin){
    if(file_exists(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/action.class.php')){
      include_once wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/action.class.php';
      return wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/action.class.php';
    }else{
      $temp = 'Plugin'.wfPlugin::to_camel_case($plugin, true).'.php';
      if(file_exists(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/'.$temp)){
        include_once wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/'.$temp;
        return wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/'.$temp;
      }else{
        exit('Could not find plugin file...('.$temp.')');
      }
    }
  }
  
  
  /**
   * Get plugin settings from theme settings.yml param plugin.
   * @param string $plugin
   * @return array
   */
  public static function getPluginSettings($plugin, $as_object = false){
    if(wfArray::get($GLOBALS, 'sys/settings/plugin/'.$plugin)){
      if(!$as_object){
        return wfArray::get($GLOBALS, 'sys/settings/plugin/'.$plugin);
      }else{
        wfPlugin::includeonce('wf/array');
        return new PluginWfArray(wfArray::get($GLOBALS, 'sys/settings/plugin/'.$plugin));
      }
    }else{
      return null;
    }
    
  }
  public static function to_camel_case($str, $capitalise_first_char = false) {
    if($capitalise_first_char) {
      $str[0] = strtoupper($str[0]);
    }
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return preg_replace_callback('/\/([a-z])/', $func, $str);
  }
  
    
}

?>
