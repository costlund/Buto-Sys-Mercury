<?php
class wfFilesystem {
  public static function getTemplate($module, $file){ // Should be removed 190201.
    if(file_exists("../a/module/$module/templates/$file")){
      $file = "../a/module/$module/templates/$file";
    }elseif(file_exists("../b/module/$module/templates/$file")){
      $file = "../b/module/$module/templates/$file";
    }else{
      $file = null;
    }
    return ($file);
  }
  public static function getLayout($file){ // Should be removed 190201.
    if(file_exists("../a/layout/templates/$file")){
      $file = "../a/layout/templates/$file";
    }elseif(file_exists("../b/layout/templates/$file")){
      $file = "../b/layout/templates/$file";
    }else{
      $file = null;
    }
    return ($file);
  }
  /**
   * Delete file.
   * @param string $file From system root.
   * @return bool
   */
  public static function delete($file){
    return unlink($file);
  }
  /**
   * Delete folder.
   * @param string $src
   */
  public static function delete_dir($src) { 
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ){
      if (( $file != '.' ) && ( $file != '..' )) { 
        if(is_dir($src . '/' . $file)){ 
          wfFilesystem::delete_dir($src . '/' . $file); 
        }else{ 
          unlink($src . '/' . $file); 
        }
      }
    }
    closedir($dir); 
    rmdir($src);
  }
  public static function getCompClass($module){ // Should be removed 190201.
    if(file_exists("../a/module/$module/component.class.php")){
      $file = "../a/module/$module/component.class.php";
    }elseif(file_exists("../b/module/$module/component.class.php")){
      $file = "../b/module/$module/component.class.php";
    }else{
      $file = null;
    }
    return ($file);
  }
  public static function getLayoutCompClass(){ // Should be removed 190201.
    if(file_exists("../a/layout/component.class.php")){
      $file = "../a/layout/component.class.php";
    }elseif(file_exists("../b/layout/component.class.php")){
      $file = "../b/layout/component.class.php";
    }else{
      $file = null;
    }
    return ($file);
  }
  public static function getCompTemplate($module, $file){ // Should be removed 190201.
    if(file_exists("../a/module/$module/comp/$file")){
      $file = "../a/module/$module/comp/$file";
    }elseif(file_exists("../b/module/$module/comp/$file")){
      $file = "../b/module/$module/comp/$file";
    }else{
      $file = null;
    }
    return ($file);
  }
  public static function getLayoutCompTemplate($file){ // Should be removed 190201.
    if(file_exists("../a/layout/comp/$file")){
      $file = "../a/layout/comp/$file";
    }elseif(file_exists("../b/layout/comp/$file")){
      $file = "../b/layout/comp/$file";
    }else{
      $file = null;
    }
    return ($file);
  }
  /**
   * Get textfile to array per line break.
   * @param string $file From system root.
   * @return array
   */
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
  /**
   * Get file contents.
   * @param string $filename
   * @param bool $root
   * @return string
   */
  public static function getContents($filename, $root = false){
    if($root){
      return file_get_contents($filename);
    }else{
      return file_get_contents(wfSettings::getAppDir().$filename);
    }
  }
  /**
   * 
   */
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
        if(substr($value, 0, 1)=='.'){ // If start with "." we should continue.
          continue;
        }
        if(sizeof($file_extansion)){
          $i = substr($value, strrpos($value , '.'));
          if(in_array(strtolower($i), $file_extansion)){
            $array[] = $value;
          }
        }else{
          $array[] = $value;
        }
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
  /**
   * Create file along with folders if not exist.
   * @param string $filename From system root.
   * @param string $content
   * @return null
   */
  public static function createFile($filename, $content){
    $dirname = dirname($filename);
    if(!wfFilesystem::fileExist($dirname)){
      mkdir($dirname, 0777, true);
    }
    file_put_contents($filename, $content);
    return null;
  }
  /**
   * Copy file along with folders if not exist.
   * @param string $source
   * @param string $dest
   * @return bool
   */
  public static function copyFile($source, $dest){
    $dirname = dirname($dest);
    if(!wfFilesystem::fileExist($dirname)){
      mkdir($dirname, 0777, true);
    }
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
    $dir = wfSettings::replaceTheme($dir);
    if(file_exists($dir)){
      return filemtime($dir);
    }else{
      return false;
    }
  }
  /**
   * Returns time from now to now how old a file is.
   * @param int $dir
   * @return int
   */
  public static function getFiletimeFromNow($dir){
    if(file_exists($dir)){
      return time() - filemtime($dir);
    }else{
      return 0;
    }
  }
  public static function saveFile($path_to_file, $text, $append = false){
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
  public static function clearCache(){
    if(wfFilesystem::fileExist(wfArray::get($GLOBALS, 'sys/theme_data_dir').'/cache')){
      $scan = wfFilesystem::getScandir(wfArray::get($GLOBALS, 'sys/theme_data_dir').'/cache');
      foreach ($scan as $key => $value) {
        wfFilesystem::delete(wfArray::get($GLOBALS, 'sys/theme_data_dir').'/cache/'.$value);
      }
    }
    return null;
  }
  public static function getCacheFile($file){
    $str = file_get_contents(wfFilesystem::getCacheFolder().'/'.$file);
    $str = unserialize($str);
    return $str;
  }
  /**
   * Checks if cache=true and folder exist.
   */
  public static function isCache(){
    if(wfArray::get($GLOBALS, 'sys/settings/cache') && wfFilesystem::fileExist(wfArray::get($GLOBALS, 'sys/theme_data_dir').'/cache')){
      return true;
    }else{
      return false;
    }
  }
  public static function getCacheFolder(){
    return wfArray::get($GLOBALS, 'sys/theme_data_dir').'/cache';
  }
  public static function formatCacheFileName($path){
    return str_replace('/', '_', $path).'.cache';
  }
  /**
   * Get cache if exist.
   * @param type $path
   * @return type
   */
  public static function getCacheIfExist($path){
    $settings = null;
    $filename = wfArray::get($GLOBALS, 'sys/app_dir').wfSettings::replaceTheme($path);
    /**
     * Check if cached file exist first.
     */
    if(wfFilesystem::isCache()){
      $cache_file = wfFilesystem::formatCacheFileName($path);
      if(wfFilesystem::fileExist(wfFilesystem::getCacheFolder().'/'.$cache_file)){
        /**
         * Cache exist.
         * Get it.
         */
        $settings = wfFilesystem::getCacheFile($cache_file);
      }else{
        /**
         * Cache not exist.
         * Load and create it.
         */
        $settings = sfYaml::load($filename);
        wfFilesystem::saveFile(wfFilesystem::getCacheFolder().'/'.$cache_file, serialize($settings));
      }
    }else{
      /**
       * Cache folder not exist 
       */
      $settings = sfYaml::load($filename);
    }
    return $settings;
  }
}
