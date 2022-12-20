<?php
class wfElement {
  public static function get($dir, $function){
    return new PluginWfYml($dir.'/element/'.$function.'.yml');
  }
}
