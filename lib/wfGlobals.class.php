<?php
/**
 * Buto class to get handle data in GLOBALS/sys.
 */
class wfGlobals{
  /**
   * Get data from globals.
   * @return mixed
   */
  public static function get($key = null){
    wfPlugin::includeonce('wf/array');
    $g = new PluginWfArray($GLOBALS['sys']);
    return $g->get($key);
  }
  public static function set($key, $value){
    wfPlugin::includeonce('wf/array');
    $g = new PluginWfArray($GLOBALS['sys']);
    $g->set($key, $value);
    $GLOBALS['sys'] = $g->get();
    return null;
  }
  public static function getVersion()           {return wfGlobals::get('version');}
  public static function getMicrotime()         {return wfGlobals::get('microtime');}
  public static function getMicrotimeStart()    {return wfGlobals::get('microtime/start');}
  public static function getMicrotimeEnd()      {return wfGlobals::get('microtime/end');}
  public static function getMicrotimeTime()     {return wfGlobals::get('microtime/time');}
  /**
   * Calc time from start.
   * @return float
   */
  public static function getMicrotimeTimeCalc() {
    return microtime(true) - wfGlobals::get('microtime/start');
  }
  /**
   * Web dir.
   * @return string
   */
  public static function getWebDir()            {return wfGlobals::get('web_dir');}
  /**
   * Get web folder from web_dir.
   * @return string
   */
  public static function getWebFolder()         {
    return substr(wfGlobals::get('web_dir'), strlen(dirname(wfGlobals::get('web_dir')))+1);
  }
  public static function getAppDir()            {return wfGlobals::get('app_dir');}
  public static function getSysDir()            {return wfGlobals::get('sys_dir');}
  public static function getThemeDataWebDir()   {return wfGlobals::get('theme_data_web_dir');}
  public static function getThemeDataDir()      {return wfGlobals::get('theme_data_dir');}
  public static function getTheme()             {return wfGlobals::get('theme');}
  public static function getTimezone()          {return wfGlobals::get('timezone');}
  public static function getErrorReporting()    {return wfGlobals::get('error_reporting');}
  public static function getDisplayErrors()     {return wfGlobals::get('display_errors');}
  public static function setMicrotimeEnd(){
    $GLOBALS['sys']['microtime']['end'] = microtime(true);
    $GLOBALS['sys']['microtime']['time'] = $GLOBALS['sys']['microtime']['end'] - $GLOBALS['sys']['microtime']['start'];
    return null;
  }
}
