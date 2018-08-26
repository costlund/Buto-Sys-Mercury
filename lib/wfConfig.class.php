<?php
/**
 * Buto class to get data from theme settings.yml.
 */
class wfConfig{
  public static function get($key = null){
    wfPlugin::includeonce('wf/array');
    $g = new PluginWfArray(wfSettings::getSettings('/theme/'.wfGlobals::getTheme().'/config/settings.yml'));
    return $g->get($key);
  }
  public static function getCache()          {return wfConfig::get('cache');}
  public static function getDump()           {return wfConfig::get('dump');}
  public static function getI18n()           {return wfConfig::get('i18n');}
  public static function getI18nLanguage()   {return wfConfig::get('i18n/language');}
  public static function getI18nLanguages()  {return wfConfig::get('i18n/languages');}
  public static function getPluginModules()  {return wfConfig::get('plugin_modules');}
  public static function getDefaultClass()   {return wfConfig::get('default_class');}
  public static function getDefaultMethod()  {return wfConfig::get('default_method');}
  public static function getPlugin()         {return wfConfig::get('plugin');}
  public static function getEvents()         {return wfConfig::get('events');}
  public static function getDomain()         {return wfConfig::get('domain');}
}
