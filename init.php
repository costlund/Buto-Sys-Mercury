<?php
/**
 * Version check.
 */
if(!isset($GLOBALS['sys']['version'])){
  throw new Exception('Buto says: Param $GLOBALS[\'sys\'][\'version\'] must be set in index.php!');
}
/**
 * i18n (should be removed?)
 */
function __($str, $params = null){
  if($params){
    foreach ($params as $key => $value) {
      $str = str_replace($key, $value, $str);
    }
  }
  return $str;
}
/**
 * Globals.
 */
$GLOBALS['sys']['sys']['name'] = $GLOBALS['sys']['version'];
$GLOBALS['sys']['sys']['version'] = null;
$GLOBALS['sys']['php'] = array('version' => phpversion());
$GLOBALS['sys']['widget'] = null;
$GLOBALS['sys']['microtime']['start'] = microtime(true);
$GLOBALS['sys']['cache'] = false;
$GLOBALS['sys']['timezone'] = 'Europe/Paris';
$GLOBALS['sys']['web_dir'] = str_replace("\\", "/", str_replace(array('\index.php', '/index.php'), '', $_SERVER['SCRIPT_FILENAME']));
$GLOBALS['sys']['web_folder'] = substr($GLOBALS['sys']['web_dir'], strlen(dirname( $GLOBALS['sys']['web_dir']))+1);
$GLOBALS['sys']['app_dir'] = dirname($GLOBALS['sys']['web_dir']);
$GLOBALS['sys']['host_dir'] = dirname($GLOBALS['sys']['app_dir']);
$GLOBALS['sys']['sys_dir'] = dirname($GLOBALS['sys']['web_dir']).'/sys/'.$GLOBALS['sys']['version'];
$GLOBALS['sys']['theme_dir'] = null;
$GLOBALS['sys']['theme_buto_data_dir'] = null;
$GLOBALS['sys']['theme_data_web_dir'] = null;
$GLOBALS['sys']['theme_data_dir'] = null;
$GLOBALS['sys']['error_reporting'] = E_ALL;
$GLOBALS['sys']['display_errors'] = 1;
$GLOBALS['sys']['enabled'] = 'widget';
$GLOBALS['sys']['session_id'] = null;
/**
 * Error settings.
 * This settings can be changed in settings.yml.
 */
error_reporting($GLOBALS['sys']['error_reporting']);
ini_set('display_errors', $GLOBALS['sys']['display_errors']);
/**
 * Include files.
 */
foreach (glob("../sys/".$GLOBALS['sys']['version']."/lib/*.php") as $filename)
{
  include_once $filename;
}
wfEvent::run('sys_start');
/**
 * Set sys/version
 */
$path_to_file = wfSettings::getAppDir().'/sys/'.$GLOBALS['sys']['version'].'/manifest.yml';
if(file_exists($path_to_file)){
  $array = sfYaml::load($path_to_file);
  if(isset($array['version'])){
    $GLOBALS['sys']['sys']['version'] = $array['version'];
  }
}
/**
 * Load ini settings from /config/settings.yml for a specific host.
 */
wfEvent::run('load_ini_settings_before');
wfSettings::loadIniSettings();
wfEvent::run('load_ini_settings_after');
/**
 * Session start.
 */
session_start();
$GLOBALS['sys']['session_id'] = session_id();
/**
 * Webmaster can change theme.
 */
if(isset($_REQUEST['loadtheme'])){
  if(wfUser::hasRole('webmaster')){
    $loadtheme = urldecode($_REQUEST['loadtheme']);
    $theme_dir = wfArray::get($GLOBALS, 'sys/app_dir').'/theme/'.$loadtheme;
    if(wfFilesystem::fileExist($theme_dir)){
      if(isset($_REQUEST['same_user'])){
        $role = wfArray::get($_SESSION, 'role');
        $username = wfArray::get($_SESSION, 'username');
        $user_id = wfArray::get($_SESSION, 'user_id');
      }
      session_destroy();
      session_start();
      $_SESSION['theme'] = $loadtheme;
      if(isset($_REQUEST['same_user'])){
        $_SESSION['role'] = $role;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user_id;
      }
      if(isset($_REQUEST['same_user'])){
        unset($role);
        unset($username);
        unset($user_id);
        wfHelp::yml_dump($_SESSION);
        exit('You are now on theme '.$_SESSION['theme'].' with same roles! Go to <a href="/">start page</a>!');
      }else{
        exit('You are now on theme '.$_SESSION['theme'].'! Go to <a href="/">start page</a>!');
      }
    }else{
      exit('Could not find theme '.$theme_dir.'! Go to <a href="/">start page</a>!');
    }
    unset($theme_dir);
  }
}
/**
 * Sign out.
 * /?signout=1
 */
if(isset($_REQUEST['signout'])){
  session_destroy();
  exit('You are signed out! Go to <a href="/">start page</a>!');
}
/**
 * Load pre settings from /config/settings.yml.
 */
wfEvent::run('load_config_settings_before');
wfSettings::loadConfigSettings();
wfEvent::run('load_config_settings_after');
/**
 * Globals.
 */
$GLOBALS['sys']['theme_dir'] = dirname($GLOBALS['sys']['web_dir']).'/theme/'.$GLOBALS['sys']['theme'];
$GLOBALS['sys']['theme_web_dir'] = $GLOBALS['sys']['web_dir'].'/theme/'.$GLOBALS['sys']['theme'];
$GLOBALS['sys']['theme_data_web_dir'] = $GLOBALS['sys']['web_dir'].'/data/theme/'.$GLOBALS['sys']['theme'];
$GLOBALS['sys']['theme_data_dir'] = dirname($GLOBALS['sys']['web_dir']).'/data/theme/'.$GLOBALS['sys']['theme'];
date_default_timezone_set(wfArray::get($GLOBALS, 'sys/timezone'));
if(wfArray::get($GLOBALS, 'sys/error_reporting')){
  eval('error_reporting('.wfArray::get($GLOBALS, 'sys/error_reporting').');');
}
if(strlen(wfArray::get($GLOBALS, 'sys/display_errors'))){
  ini_set('display_errors', wfArray::get($GLOBALS, 'sys/display_errors'));
}
$GLOBALS['sys']['class'] = null;
$GLOBALS['sys']['method'] = null;
/**
 * Load theme settings.
 */
wfEvent::run('load_theme_config_settings_before');
$GLOBALS['sys']['settings'] = wfSettings::loadThemeConfigSettings();
wfEvent::run('load_theme_config_settings_after');
/**
 * Auto set i18n/language if match in settings.
 * 
 */
wfI18n::autoSelectLanguage();
/**
 * Shutdown method.
 */
register_shutdown_function("shutdown");
function shutdown(){
  wfEvent::run('shutdown');
}
/**
 * Handle request params.
 * Set class and method.
 */
wfEvent::run('request_rewrite_before');
wfRequest::rewrite();
wfEvent::run('request_rewrite_after');
/**
 * webmaster or webadmin can view phpinfo via /?phpinfo=_param_name_.
 * or if localhost.
 */
wfPhpinfo::show_info();
/**
 * Webmaster plugin page (or localhost).
 */
if(isset($_REQUEST['webmaster_plugin']) && $_REQUEST['webmaster_plugin'] && (wfUser::hasRole('webmaster') || wfHelp::isLocalhost())){
  wfGlobals::set('settings/plugin_modules/webmaster_plugin', array('plugin' => $_REQUEST['webmaster_plugin']));
  wfGlobals::set('class', 'webmaster_plugin');
  wfGlobals::set('method', $_REQUEST['page']);
}
/**
 * Check if param sys/settings/plugin_modules is set.
 */
if(!wfArray::get($GLOBALS, 'sys/settings/plugin_modules')){
  throw new Exception('Buto says: Param sys/settings/plugin_modules must be set!');
}
/**
 * Plugin param.
 * Set sys/plugin.
 */
wfGlobals::setSys('plugin', wfArray::get($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/plugin'));
/**
 * If no secure param is set in plugin_modules for theme we check in file /config/secure.yml exist in plugin dir.
 */
if(!wfArray::isKey($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/secure')){
  $temp = wfFilesystem::loadYml(wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/'.wfArray::get($GLOBALS, 'sys/plugin').'/config/secure.yml', false);
  if($temp){
    wfGlobals::setSys('settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/secure', $temp);
  }
  unset($temp);
}
if(wfArray::get($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/plugin')){
  /**
   * If param secure is set user must pass throw validation, one false is enough.
   */
  $stop = true;
  if(wfArray::isKey($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/secure')){
    $secure = wfArray::get($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/secure');
    $role = wfUser::getRole();
    foreach ($role as $key => $value) {
      if(isset($secure[$value])){
        if(isset($secure[$value][wfArray::get($GLOBALS, 'sys/method')])){
          if(!$secure[$value][wfArray::get($GLOBALS, 'sys/method')]){
            $stop = false;
            break;
          }
        }elseif(isset($secure[$value]['*']) && !$secure[$value]['*']){
          $stop = false;
          break;
        }
      }
    }
  }else{
    $stop = false;
  }
  if($stop){
    wfEvent::run('page_not_found');
  }
  /**
   * Check enabled.
   */
  if(wfGlobals::get('enabled')=='all' && !wfDocument::isPluginEnabled(wfGlobals::get('plugin'))){
    throw new Exception('Plugin '.wfGlobals::get('plugin').' is not enabled.');
  }
  /**
   * Run the page plugin.
   */
  wfPlugin::includeonce(wfArray::get($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/plugin'));
  $obj = wfSettings::getPluginObj();
  $method = 'page_'.wfSettings::getPluginMethod();
  if(!method_exists($obj, $method) && !method_exists($obj, '__call')){
    wfEvent::run('page_not_found', array('description' => 'Method '.$method.' does not exist in '.wfArray::get($GLOBALS, 'sys/plugin').'!'));
  }
  wfEvent::run('module_method_before');
  /**
   * session sys
   */
  if(!wfUser::getSession()->get('sys')){
    wfUser::setSession('sys', wfGlobals::get('sys'));
  }else{
    if(wfUser::getSession()->get('sys/version') != wfGlobals::get('sys/version')){
      // System version has changed.
    }
  }
  /**
   * session pages
   */
  if(wfUser::getSession()->get('pages/item')){
    wfUser::setSession('pages/item', array_slice(wfUser::getSession()->get('pages/item'), sizeof(wfUser::getSession()->get('pages/item'))-9 ));
  }
  wfUser::setSession('pages/id', wfCrypt::getUid());
  $temp = new PluginWfArray(wfRequest::getAll());
  if($temp->get('password')){
    $temp->set('password', '****');
  }
  wfUser::setSession('pages/item/'.wfUser::getSession()->get('pages/id'), array(
    'REQUEST_URI' => wfServer::getRequestUri(), 
    'plugin' => wfArray::get($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/plugin'), 
    'method' => $method,
    'time' => date('Y-m-d H:i:s'),
    'get' => $temp->get()
  ));
  /**
   * 
   */
  $obj->$method();
  wfEvent::run('module_method_after');
  unset($obj);
  unset($method);
  /**
   * Render element.
   */
  wfEvent::run('document_render_before');
  /**
   * sys/page/content
   */
  if(wfArray::get($GLOBALS, 'sys/page') && !wfArray::get($GLOBALS, 'sys/page/content')){
    if(false){
      /**
       * Not sure of this (230322, CÖ);
       */
      throw new Exception(__FILE__.' says: param sys/page/content is not set in globals (filename '.wfArray::get($GLOBALS, 'sys/filename').')!');
    }
  }
  /**
   * 
   */
  wfDocument::renderElement((wfArray::get($GLOBALS, 'sys/page/content')));
  wfEvent::run('document_render_after');
}else{
  wfEvent::run('page_not_found', array('description' => 'Module is not set in theme settings.yml'));
}
/**
 * Set time params.
 */
wfGlobals::setMicrotimeEnd();
/**
 * 
 */
wfEvent::run('sys_close');
