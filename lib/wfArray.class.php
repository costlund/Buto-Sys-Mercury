<?php

class wfArray {

  /**
   * Sorting multiple array by a key.
   * @param array $array
   * @param string_or_float $key
   * @param bool $desc Optional
   * @return array
   */
  public static function sortMultiple($array, $key, $desc = false) {
    $sorter = array();
    reset($array);
    $largest_sort_value = null; //Of key not exist and $desc=false.
    foreach ($array as $ii => $va) {
      if (isset($va[$key])) {
        if ($va[$key] > $largest_sort_value) {
          $largest_sort_value = $va[$key] + 1;
        }
      }
    }
    foreach ($array as $ii => $va) {
      if (isset($va[$key])) {
        $sorter[$ii] = $va[$key];
      } else {
        if ($desc) {
          $sorter[$ii] = null;
        } else {
          $sorter[$ii] = $largest_sort_value;
        }
      }
    }
    if ($desc) {
      arsort($sorter);
    } else {
      asort($sorter);
    }
    $ret = array();
    foreach ($sorter as $ii => $va) {
      $ret[$ii] = $array[$ii];
    }
    return $ret;
  }

  /**
   * Check if array key exist and value is true.
   * @param array $array
   * @param float $key
   * @return bool
   */
  public static function issetAndTrue($array, $key) {
    $path_to_key = "['".str_replace('/', "']['", $key)."']";
    eval("if(isset(\$array$path_to_key) && \$array$path_to_key===true){\$return = true;}else{\$return = false;};");
    return $return;
  }

  
  public static function isKey($array, $path_to_key){
    $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
    //eval("var_dump(isset(\$array$path_to_key));");
    eval("\$return = isset(\$array$path_to_key);");
    return $return;
  }
  
  
  /**
   * Insert array value to a specific position.
   * Example:           $page['content'] = wfArray::insertToPosition('first', $page['content'], $error, 'error');
   * @param float $position String first/last or int position. Position zero is first.
   * @param array $array Array to handle.
   * @param anything $value
   * @param string_or_float $key Optional.
   * @return array
   */
  public static function insertToPosition($position, $array, $value, $key = null) {
    if ($key == null) {
      $array[] = $value;
      end($array);
      $key = key($array);
    } else {
      $array[$key] = $value;
    }
    if ($position == 'first') {
      $array = wfArray::moveByKey($array, $key, 0);
    } elseif ($position == 'last') {
      //Do nothing.
    } elseif (is_int($position)) {
      $array = wfArray::moveByKey($array, $key, $position);
    }
    return $array;
  }

  /**
   * Insert array value after another value by it's key.
   *
   * @param type $after_key
   * @param type $array
   * @param type $value
   * @param type $key optional
   * @return array
   */
  public static function insertAfter($after_key, $array, $value, $key = null) {
    if ($key == null) {
      $array[] = $value;
      end($array);
      $key = key($array);
    } else {
      $array[$key] = $value;
    }
    $position = array_search($after_key, array_keys($array));
    $array = wfArray::moveByKey($array, $key, $position + 1);

    return $array;
  }

  /**
   * Move array element by key to a specific position.
   * @param type $array
   * @param type $move_key
   * @param type $to_position
   * @return array
   */
  public static function moveByKey($array, $move_key, $to_position) {
    $size = sizeof($array);
    $array_part_to_move = array();
    $from_position = 0;
    foreach ($array as $key => $value) {
      if ($key == $move_key) {
        $array_part_to_move[$move_key] = $value;
        break;
      }
      $from_position++;
    }
    unset($array[$move_key]);
    $new_array = array();
    $i = 0;
    foreach ($array as $key => $value) {
      if ($i == $to_position) {
        $new_array[$move_key] = $array_part_to_move[$move_key];
      }
      $new_array[$key] = $value;
      $i++;
    }
    if ($to_position + 1 >= $size) {
      $new_array[$move_key] = $array_part_to_move[$move_key];
    }
    return $new_array;
  }
  
  /**
   * Get value from array.
   * @param type $array
   * @param string $path_to_key
   * @return value or null
   */
  public static function get($array, $path_to_key, $error_message = null){
    $return = null;
    $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
    eval("if(isset(\$array$path_to_key)){\$return = \$array$path_to_key;}elseif(\$error_message){throw new Exception(\$error_message);}");
    return $return;
  }
  /**
   * 
   * @param type $array
   * @param string $path_to_key
   * @param type $value
   * @return type
   */
  public static function set($array, $path_to_key, $value){
    $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
    $path_to_key = str_replace("['']", "[]", $path_to_key);

    
    eval("\$array$path_to_key = \$value;");
    return $array;
  }
  public static function setUnset($array, $path_to_key){
    $path_to_key = "['".str_replace('/', "']['", $path_to_key)."']";
    eval("unset(\$array$path_to_key);");
    return $array;
  }
  
  public static function formatPathToKey($path_to_key){
    return "['".str_replace('/', "']['", $path_to_key)."']";
  }
  
  
  /**
   *
   * @param array $arr1 Array to merge with.
   * @param array $arr2 Array to merge.
   * @param int $merge_level Whitch level to only merge values.
   * @param int $level Are not to be set, param for the function to pass around .
   * @return array
   */
  public static function mergeMultiple($arr1, $arr2, $merge_level = null, $level = 0){
    $level ++;
    foreach ($arr2 as $key1 => $value1) {
      if(array_key_exists($key1, $arr1)){
        if(is_array($arr1[$key1]) && is_array($value1)){
          if($merge_level==$level){
            $arr1[$key1] = array_merge($arr1[$key1], $value1);
          }else{
            $arr1[$key1] = wfArray::mergeMultiple($arr1[$key1], $value1, $merge_level, $level);
          }
        }else{
          $arr1[$key1] = $value1;
        }
      }else{
        $arr1[$key1] = $value1;
      }
    }
    return $arr1;
  }
  public static function rewrite($array){
    
    if(isset($array['_rewrite']['set'])){
      foreach ($array['_rewrite']['set'] as $key => $value) {
        
        if(!isset($value['path_to_key']) || !isset($value['value'])){
          continue;
        }
        
        $path_to_key = "['".str_replace('/', "']['", $value['path_to_key'])."']";
        $item_value = $value['value'];
        if(is_array($item_value)){
          eval("\$array$path_to_key = \$item_value;");
        }else{
          eval("\$array$path_to_key = '$item_value';");
        }
      }
      //wfHelp::yml_dump($array, true);
    }
    if(isset($array['_rewrite']['unset'])){
      foreach ($array['_rewrite']['unset'] as $key => $value) {
        $path_to_key = "['".str_replace('/', "']['", $value)."']";
        eval("unset(\$array$path_to_key);");
      }
    }
    unset($array['_rewrite']);
    return $array;
  }
  

}
