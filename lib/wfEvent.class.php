<?php
class wfEvent {
  public static function run($event, $data = array(), $element = array()) {
    if(wfArray::isKey($GLOBALS, 'sys/settings/events/'.$event)){
      foreach (wfArray::get($GLOBALS, 'sys/settings/events/'.$event) as $key => $value) {
        $run = true;
        if(wfArray::isKey($value, 'settings/plugin_modules')){
          $allow = true;
          if(wfArray::isKey($value, 'settings/plugin_modules/allow')){
            $allow = wfArray::get($value, 'settings/plugin_modules/allow');
          }
          $item = wfArray::get($value, 'settings/plugin_modules/item');
          if($allow){
            if(in_array(wfArray::get($GLOBALS, 'sys/class'), $item)){
              $run = true;
            }else{
              $run = false;
            }
          }else{
            if(in_array(wfArray::get($GLOBALS, 'sys/class'), $item)){
              $run = false;
            }else{
              $run = true;
            }
          }
        }
        if($run){
          /**
           * Set event in Globals.
           */
          $GLOBALS['sys']['event'] = $value;
          /**
           * 
           */
          $plugin = wfArray::get($value, 'plugin');
          wfPlugin::includeonce($plugin);
          $obj = wfSettings::getPluginObj($plugin);
          $method = 'event_'.wfArray::get($value, 'method', "Method is not set in sys/settings/events/$event/$key!");
          $data = $obj->$method($value, $data, $element);
        }
      }
    }
    /**
     * Some event should return data.
     */
    if($event == 'document_render_element' || $event == 'document_render_element_innerhtml' || $event == 'document_render_string' || $event == 'document_render_title'){
      return $data;
    }
    /**
     * Stop executing if critical events not handled.
     */
    if($event == 'security_issue'){
      throw new Exception(__CLASS__.'::'.__FUNCTION__.' says: Security issue!');
      exit('Security issue!');
    }elseif($event == 'page_not_found'){
      header("HTTP/1.0 404 Not Found");
      exit('Could not find this page ('.wfServer::getRequestUri().'). Please go to <a href="/">Start page</a>.');
    }
  }
}