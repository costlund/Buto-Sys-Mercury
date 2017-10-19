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
}