<?php
class wfEvent {
  public static function run($event, $data = array()) {
    /**
      * sys_start
      * load_config_settings_before
      * load_config_settings_after
      * load_theme_config_settings_before
      * load_theme_config_settings_after
      * shutdown
      * request_rewrite_before
      * request_rewrite_after
      * module_method_before
      * security_issue
      * page_not_found
      * module_method_after
      * document_render_before
        * document_render_element
          * document_render_innerhtml
      * document_render_after
      * sys_close
     * 
      */
    if(wfArray::isKey($GLOBALS, 'sys/settings/events/'.$event)){
      // Get event registrations in theme settings.yml for this event.
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
          $plugin = wfArray::get($value, 'plugin');
          wfPlugin::includeonce($plugin);
          $obj = wfSettings::getPluginObj($plugin);
          $method = 'event_'.wfArray::get($value, 'method', "Method is not set in sys/settings/events/$event/$key!");
          $data = $obj->$method($value, $data);
        }
        

        
        
      }
    }
    if($event == 'document_render_element' || $event == 'document_render_element_innerhtml'){
      return $data;
    }
    // Stop executing if critical events not handled.
    if($event == 'security_issue'){
      exit('Security issue!');
    }elseif($event == 'page_not_found'){
      header("HTTP/1.0 404 Not Found");
     exit('404 Not Found');
    }
  }
}