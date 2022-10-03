<?php
class wfWidget{
  public static function handle_data($data, $default = array()){
    $wData = new PluginWfArray($default);
    if(isset($data['data'])){
      $wData->merge($data['data']);
    }
    return $wData;
  }
}
