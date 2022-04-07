<?php
class wfI18n{
  public static function getLanguage(){
    if(wfArray::get($_SESSION, 'i18n/language')){
      $language = wfArray::get($_SESSION, 'i18n/language');
    }else{
      $language = wfArray::get($GLOBALS, 'sys/settings/i18n/language');
    }
    return $language;
  }
  public static function setLanguage($language){
    $_SESSION = wfArray::set($_SESSION, 'i18n/language', $language);
    return true;
  }
  public static function getLanguages(){
    return wfArray::get($GLOBALS, 'sys/settings/i18n/languages');
  }
  public static function getLanguagesMore(){
    $languages = wfI18n::getLanguages();
    $data = array();
    foreach($languages as $k => $v){
      $label = $v;
      if(wfGlobals::get("settings/i18n/lable/$v")){
        $label = wfGlobals::get("settings/i18n/lable/$v");

      }
      $data[] = array('name' => $v, 'label' => $label);
    }
    return $data;
  }
  public static function hasLanguage($language){
    return in_array($language, wfI18n::getLanguages());
  }
  public static function autoSelectLanguage(){
    /**
     * Run only once per session.
     */
    if(!wfUser::getSession()->get('i18n/auto_select') && !wfUser::getSession()->get('i18n/language')){
      /**
       * Get language from HTTP_ACCEPT_LANGUAGE.
       */
      $language = locale_accept_from_http(wfServer::getHttpAccept_Language());
      if(strpos($language, '_')){
        $language = substr($language, 0, strpos($language, '_'));
      }
      /**
       * Set only once param.
       */
      wfUser::setSession('i18n/auto_select', true);
      /**
       * Set language if exist.
       */
      if(wfI18n::hasLanguage($language)){
        wfUser::setSession('i18n/language', $language);
      }else{
        wfUser::setSession('i18n/language', wfI18n::getLanguage());
      }
    }
  }
}