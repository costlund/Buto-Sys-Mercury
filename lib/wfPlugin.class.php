<?php
class wfPlugin {
    

  
  
  /**
   * Get plugin settings from theme settings.yml param plugin_modules.
   * Get only one...
   * @param string $plugin If call from widget.
   * @return array
   */
  public static function getModuleSettings($plugin = null, $as_object = false){
    $settings = null;
    if(!$plugin){
      $plugin = wfArray::get($GLOBALS, 'sys/plugin');
    }
    foreach (wfArray::get($GLOBALS, 'sys/settings/plugin_modules') as $key => $value) {
      if(wfArray::get($value, 'plugin') == $plugin){
        if(!$as_object){
          $settings = wfArray::get($value, 'settings');
        }else{
          wfPlugin::includeonce('wf/array');
          $settings = new PluginWfArray(wfArray::get($value, 'settings'));
        }
        break;
      }
    }
    return $settings;
  }
  /**
   * Get first occured plugin_modules in theme settings file depending on param plugin.
   * The key is inserted along with other params.
   * @param string $plugin
   * @return object PluginWfArray
   */
  public static function getPluginModulesOne($plugin){
    $settings = null;
    foreach (wfArray::get($GLOBALS, 'sys/settings/plugin_modules') as $key => $value) {
      if(wfArray::get($value, 'plugin') == $plugin){
        wfPlugin::includeonce('wf/array');
        $settings = new PluginWfArray(array_merge($value, array('key' => $key)));
        break;
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
  /**
   * Include plugin.
   */
  public static function includeonce($plugin){
    $plugin_action_file = 'Plugin'.wfPlugin::to_camel_case($plugin, true).'.php';
    if(file_exists(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/'.$plugin_action_file)){
      /**
       * New name roule PluginOrgName.php.
       */
      include_once wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/'.$plugin_action_file;
      return wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/'.$plugin_action_file;
    }elseif(file_exists(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/action.class.php')){
      /**
       * Trying to get file with the old name roule action.class.php.
       */
      include_once wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/action.class.php';
      return wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/action.class.php';
    }else{
      throw new Exception('Could not find plugin file ('.$plugin.')');
    }
  }
  /**
   * Plugin exist.
   */
  public static function plugin_file_exist($plugin){
    $plugin_action_file = 'Plugin'.wfPlugin::to_camel_case($plugin, true).'.php';
    if(file_exists(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/'.$plugin_action_file)){
      return true;
    }elseif(file_exists(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$plugin.'/action.class.php')){
      return true;
    }else{
      return false;
    }
  }
  /**
   * Enable plugin for widget usage.
   */
  public static function enable($plugin){
    $GLOBALS = wfArray::set($GLOBALS, "sys/settings/plugin/$plugin/enabled", true);
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
    $func = create_function('$c', 'return strtoupper($c[1]);'); //PHP version 7.1.12 but not 7.2.1?
    return preg_replace_callback('/\/([a-z])/', $func, $str);
  }
  /**
   * Set flash.
   * @param string $plugin
   * @param string $key
   * @param array $element Multiple elements.
   */
  public static function flashSet($plugin, $key, $element){
    $_SESSION = wfArray::set($_SESSION, "plugin/$plugin/flash/$key", $element);
  }
  /**
   * Check if flash exist.
   * @param string $plugin
   * @param string $key
   * @return boolean
   */
  public static function flashHas($plugin, $key){
    if(wfArray::get($_SESSION, "plugin/$plugin/flash/$key")){
      return true;
    }else{
      return false;
    }
  }
  /**
   * Get flash.
   * @param string $plugin
   * @param string $key
   * @return type
   */
  public static function flashGet($plugin, $key){
    if(wfArray::get($_SESSION, "plugin/$plugin/flash/$key")){
      $element = wfArray::get($_SESSION, "plugin/$plugin/flash/$key");
      $_SESSION = wfArray::setUnset($_SESSION, "plugin/$plugin/flash/$key");
      return $element;
    }else{
      return null;
    }
  }
  /**
   * Get widget default data from /plugin/xx/yy/default folder.
   * @param array $data Widget method data.
   * @return PluginWfArray
   */
  public static function getWidgetDefault($data){
    wfPlugin::includeonce('wf/array');
    $data = new PluginWfArray($data);
    return wfSettings::getSettingsAsObject('/plugin/'.$data->get('plugin').'/default/widget.'.$data->get('method').'.yml');
  }
  /**
   * Get all plugins.
   */
  public static function getPluginForAll(){
    wfPlugin::includeonce('wf/array');
    /**
     * Plugin folder.
     */
    $plugin_folders = wfFilesystem::getScandir(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin');
    /**
     * Get all plugins.
     */
    $plugins = new PluginWfArray();
    foreach ($plugin_folders as $key => $value) {
      if(substr($value, 0, 1)=='.'){
        continue;
      }
      foreach (wfFilesystem::getScandir(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.$value) as $key2 => $value2) {
        $plugins->set("$value/$value2/name", "$value/$value2");
      }
    }
    return $plugins;
  }
  /**
   * Get all plugins.
   */
  public static function getPluginForTheme(){
    wfPlugin::includeonce('wf/array');
    /**
     * Plugin folder.
     */
    $plugin_folders = wfFilesystem::getScandir(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin');
    /**
     * Get all plugins.
     */
    $plugins = new PluginWfArray();
    $settings = new PluginWfArray(wfArray::get($GLOBALS, 'sys/settings'));
    foreach ($settings->get('plugin_modules') as $key => $value) {
      /**
       * Plugin modules.
       */
      $item = new PluginWfArray($value);
      $plugins->set($item->get('plugin')."/name", $item->get('plugin'));
    }
    foreach ($settings->get('plugin') as $key => $value) {
      /**
       * Plugin.
       */
      foreach ($value as $key2 => $value2) {
        $plugins->set("$key/$key2/name", "$key/$key2");
      }
    }
    foreach ($settings->get('events') as $key => $value) {
      /**
       * Event.
       */
      foreach ($value as $key2 => $value2) {
        $item = new PluginWfArray($value2);
        $plugins->set($item->get('plugin')."/name", $item->get('plugin'));
      }
    }
    return $plugins;
  }
}

?>
