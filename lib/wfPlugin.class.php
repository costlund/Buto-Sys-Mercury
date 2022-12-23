<?php
class wfPlugin {
  /**
   * Get plugin settings from theme settings.yml param plugin_modules.
   * Get only one...
   * @param string $plugin If call from widget.
   * @return An array or object.
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
    /**
     *
     */
    $settings = wfSettings::getSettingsFromYmlString($settings);
    /**
     *
     */
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
        $settings = wfPlugin::settings_from_yml_string($settings);
        break;
      }
    }
    return $settings;
  }
  /**
   * Get settings from plugin_modules depending on current class.
   * @return object PluginWfArray
   */
  public static function getPluginModulesByClass(){
    $settings = null;
    $class = wfGlobals::get('class');
    if(wfArray::get($GLOBALS, "sys/settings/plugin_modules/$class")){
      $settings = new PluginWfArray(wfArray::get($GLOBALS, "sys/settings/plugin_modules/$class"));
      $settings = wfPlugin::settings_from_yml_string($settings);
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
   * @param string $plugin
   * @return array
   * @throws Exception
   */
  public static function includeonce($plugin){
    $plugin_action_file = 'Plugin'.wfPlugin::to_camel_case($plugin).'.php';
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
      throw new Exception(__CLASS__.' says: Could not find plugin file ('.$plugin.')');
    }
  }
  /**
   * Plugin exist.
   */
  public static function plugin_file_exist($plugin){
    $plugin_action_file = 'Plugin'.wfPlugin::to_camel_case($plugin).'.php';
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
   * @param boolean $as_object
   * @param array $default Default values.
   * @return mixed
   */
  public static function getPluginSettings($plugin, $as_object = false, $default = array()){
    if(wfArray::get($GLOBALS, 'sys/settings/plugin/'.$plugin)){
      $settings = wfArray::get($GLOBALS, 'sys/settings/plugin/'.$plugin);
      $settings = array_merge($default, $settings);
      $settings = wfPlugin::settings_from_yml_string($settings);
      if(!$as_object){
        return $settings;
      }else{
        wfPlugin::includeonce('wf/array');
        return new PluginWfArray($settings);
      }
    }else{
      if(!$as_object){
        return array();
      }else{
        wfPlugin::includeonce('wf/array');
        return new PluginWfArray();
      }
    }
  }
  public static function settings_from_yml_string($v){
    if(is_array($v)){
      if(isset($v['data'])){
        $v['data'] = wfSettings::getSettingsFromYmlString($v['data']);
      }
    }else{
      if($v->get('settings')){
        $v->set('settings', wfSettings::getSettingsFromYmlString($v->get('settings')));
      }
    }
    return $v;
  }
  /**
   * Camelcase plugin name with no slash.
   * Example wf/doc to WfDoc.
   * @param string $str
   * @return string
   */
  public static function to_camel_case($str) {
    $a = preg_split("#/#", $str);
    $a[0] = ucfirst($a[0]);
    $a[1] = ucfirst($a[1]);
    $r = $a[0].$a[1];
    return $r;
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
  public static function event_remove($name, $plugin){
    $g = new PluginWfArray($GLOBALS);
    $events = $g->get("sys/settings/events/$name");
    if($events){
      foreach ($events as $key => $value) {
        if($value['plugin']==$plugin){
          $g->setUnset("sys/settings/events/$name/$key");
        }
      }
      $GLOBALS = $g->get();
    }
    return null;
  }
  /**
   * Validate params.
   * @param string $class, pass __CLASS__
   * @param string $function, pass __FUNCTION__
   * @param array  $validate, array('my/key' => 'My error message!')
   * @param array  $data, array data to validate.
   * @return null
   * 
   */
  public static function validateParams($class, $function, $validate, $data){
    $data = new PluginWfArray($data);
    foreach($validate as $k => $v){
      $i = new PluginWfArray($v);
      $k = str_replace('$', '/', $k);
      if($i->get('type')=='exist'){
        if(!$data->get($k)){
          exit($class.'.'.$function.' says: '.str_replace('[key]', $k, $i->get('message')));
        }
      }
    }
  }
}
