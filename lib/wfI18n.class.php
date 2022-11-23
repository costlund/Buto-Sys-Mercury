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
    if(wfI18n::getLanguages()){
      return in_array($language, wfI18n::getLanguages());
    }else{
      return false;
    }
  }
  public static function autoSelectLanguage(){
    /**
     * 
     */
    $auto_select = true;
    if(wfGlobals::get('settings/i18n/auto_select')===false){
      $auto_select = false;
    }
    /**
     * Run only once per session.
     */
    if(!wfUser::getSession()->get('i18n/auto_select') && !wfUser::getSession()->get('i18n/language')){
      if($auto_select){
        /**
         * Get language from HTTP_ACCEPT_LANGUAGE.
         */
        $language = wfI18n::getHttpAccept_Language_Languages();
        if(strpos($language, '_')){
          $language = substr($language, 0, strpos($language, '_'));
        }
        /**
         * Set language if exist.
         */
        if(wfI18n::hasLanguage($language)){
          wfUser::setSession('i18n/language', $language);
        }else{
          wfUser::setSession('i18n/language', wfI18n::getLanguage());
        }
      }
      /**
       * Set only once param.
       */
      wfUser::setSession('i18n/auto_select', true);
    }
  }
  public static function getHttpAccept_Language_Languages(){
    $return = '';
    $rs = wfServer::get('HTTP_ACCEPT_LANGUAGE');
    if(!$rs){
      return $return;
    }
    $rs = str_replace(';', ',', $rs);
    $rs = preg_split("/,/", $rs);
    if(!$rs){
      return $return;
    }
    $languages = wfI18n::getLanguages();
    if(!$languages){
      return $return;
    }
    foreach($rs as $v){
      if(in_array($v, $languages)){
        $return = $v;
        break;
      }
    }
    return $return;
  }
}