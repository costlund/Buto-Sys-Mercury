<?php
class wfFilesystem {
  
  
  public static function getTemplate($module, $file){
    if(file_exists("../a/module/$module/templates/$file")){
      $file = "../a/module/$module/templates/$file";
    }elseif(file_exists("../b/module/$module/templates/$file")){
      $file = "../b/module/$module/templates/$file";
    }else{
      $file = null;
    }
    return ($file);
  }
  public static function getLayout($file){
    if(file_exists("../a/layout/templates/$file")){
      $file = "../a/layout/templates/$file";
    }elseif(file_exists("../b/layout/templates/$file")){
      $file = "../b/layout/templates/$file";
    }else{
      $file = null;
    }
    return ($file);
  }
  
  public static function delete($file){
    return unlink($file);
  }
  
  //include_once "../app/".strtolower($class)."/component.class.php";
  
  public static function getCompClass($module){
    if(file_exists("../a/module/$module/component.class.php")){
      $file = "../a/module/$module/component.class.php";
    }elseif(file_exists("../b/module/$module/component.class.php")){
      $file = "../b/module/$module/component.class.php";
    }else{
      $file = null;
    }
    return ($file);
  }
  public static function getLayoutCompClass(){
    if(file_exists("../a/layout/component.class.php")){
      $file = "../a/layout/component.class.php";
    }elseif(file_exists("../b/layout/component.class.php")){
      $file = "../b/layout/component.class.php";
    }else{
      $file = null;
    }
    return ($file);
  }
  public static function getCompTemplate($module, $file){
    if(file_exists("../a/module/$module/comp/$file")){
      $file = "../a/module/$module/comp/$file";
    }elseif(file_exists("../b/module/$module/comp/$file")){
      $file = "../b/module/$module/comp/$file";
    }else{
      $file = null;
    }
    return ($file);
  }
  public static function getLayoutCompTemplate($file){
    if(file_exists("../a/layout/comp/$file")){
      $file = "../a/layout/comp/$file";
    }elseif(file_exists("../b/layout/comp/$file")){
      $file = "../b/layout/comp/$file";
    }else{
      $file = null;
    }
    return ($file);
  }
  
  public static function getTextfileToArray($file){
    $table = array();
    if(file_exists($file)){
        $str = file_get_contents($file);
        $str = explode("\r", $str);
        foreach ($str as $key => $value) {
            $table[] = explode("\t", $value);
        }
    }
    return $table;
  }
  
  public static function getContents($filename, $root = false){
    if($root){
      return file_get_contents($filename);
    }else{
      return file_get_contents(wfSettings::getAppDir().$filename);
    }
  }
  
  public static function getContent($url, $file, $filetime_reload){
    $reload = false;
    if(file_exists($file)){
      if((time() - filemtime($file)) > $filetime_reload){
        $reload = true;
      }
    }else{
      $reload = true;
    }

    if($reload){
        $content = file_get_contents($url);
        file_put_contents($file, $content);
    }else{
      $content = file_get_contents($file);    
    }
    return $content;
  }
  
  /**
   * Get files from a folder. Optional to include extensions.
   * Include root dir.
   * @param string $dir From root.
   * @param array $file_extansion Extension in array (.jpg).
   * @return array
   */
  public static function getScandir($dir, $file_extansion = array()){
    $dir = wfSettings::replaceTheme($dir);
    
    foreach ($file_extansion as $key => $value) {
      $file_extansion['key'] = strtolower($value);
    }
    
    
      $array = array();
      if(file_exists($dir)){
        $dir =  scandir($dir);
        foreach ($dir as $key => $value) {
            if($value=='.' || $value=='..'){continue;}
            
            if(sizeof($file_extansion)){
              $i = substr($value, strrpos($value , '.'));
              if(in_array(strtolower($i), $file_extansion)){
                $array[] = $value;
              }
              
              
              
            }else{
              $array[] = $value;
            }
            
            
            //if(!is_dir($value)){
            //}
        }
      }
      return $array;
  }
  
  public static function createDir($dir){
    if(!file_exists($dir)){
      return mkdir($dir);
    }
    return null;
  }
  public static function copyFile($source, $dest){
    return copy($source, $dest);
  }
  public static function getCreatedAt($dir){
    if(file_exists($dir)){
      return date('Y-m-d H:i:s', filemtime($dir));
    }else{
      return $dir;
    }
  }
  
  /**
   * Gets file modification time.
   * @param string $dir
   * @return boolean or int
   */
  public static function getFiletime($dir){
    if(file_exists($dir)){
      return filemtime($dir);
    }else{
      return false;
    }
  }
  
  
  public static function saveFile($path_to_file, $text, $append = false){
//    if(file_exists($path_to_file)){//
//      file_put_contents($path_to_file, $text, FILE_APPEND);
//    }else{
//    }
    if(!$append){
      file_put_contents($path_to_file, $text);
    }else{
      file_put_contents($path_to_file, $text, FILE_APPEND);
    }
  }
  
  /**
   * Check if a file or dir exist.
   * @param type $filename
   * @return type
   */
  public static function fileExist($filename){
    return file_exists(wfSettings::replaceTheme($filename));
//    if($root){
//    }  else {
//      return file_exists(wfSettings::getAppDir(). $filename);
//    }
  }
  
  /**
   * Load array from .yml file.
   * @param string $filename Path to file.
   * @param bool $error False to return null if file not exist.
   * @param array $replace Array with to replace content.
   * @return array/null
   * @throws Exception If no file exist and param $error is true.
   */
  public static function loadYml($filename, $error = true, $replace = array()){
    $filename = wfSettings::replaceTheme($filename);
    if(file_exists($filename)){
      if($replace){
        $yml = file_get_contents($filename);
        foreach ($replace as $key => $value) {
          $yml = str_replace($key, $value, $yml);
        }
        return sfYaml::load($yml);
      }else{
        return sfYaml::load($filename);
      }
    }else{
      if($error){
        throw new Exception("Could not find file $filename!");
      }else{
        return null;       
      }
    }
  }
  
}

?>
