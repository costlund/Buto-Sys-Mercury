<?php
class wfUser {
    
  public static function isSecure(){
      if(isset($_SESSION['secure']) && $_SESSION['secure']){
          return true;
      }else{
          return false;
      }
  }

  /**
   * Get user security merged with unsecure.
   * @return type
   */
  public static function getSecurity(){
    $temp = $GLOBALS['settings']['security']['unsecure'];
    if(isset($_SESSION['security'])){
      $user_security = $_SESSION['security'];
      foreach ($user_security as $key => $value) {
        if(!array_key_exists($key, $temp)){
          $temp[$key] = $user_security[$key];
        }elseif(!is_array($user_security[$key])){
          $temp[$key] = $user_security[$key];
        }elseif(is_array($temp[$key])){
          $temp[$key] = array_merge($user_security[$key], $temp[$key]);
        }
      }
    }
    return $temp;
  }
  
  /**
   * Check if user has role.
   * @param string $role
   * @return boolean
   */
  public static function hasRole($role){
    $roles = self::getRole();
    if($roles){
      return in_array($role, $roles);
    }else{
      return false;
    }
  }
  
  //always:
  //  - visitor
  //not_authenticated:
  //  - unknown
  //is_authenticated:
  //  - client  
  public static function getRole(){
    
    $roles = wfArray::get($GLOBALS, 'sys/settings/roles');
    // If roles not set in theme settings.yml we set mandatory roles.
    if(!$roles){
      //exit('Roles is not set in theme settings.yml.');
      $roles = array('always' => array('visitor'), 'not_authenticated' => array('unknown'), 'is_authenticated' => array('client'));
    }
    
    //wfHelp::yml_dump($roles);
    
    $always = wfArray::get($roles, 'always');
    if(!$always){
      exit('Role with key always is missing in theme settings.yml');
    }
    
    $not_authenticated = wfArray::get($roles, 'not_authenticated');
    $is_authenticated = wfArray::get($roles, 'is_authenticated');
    
    
    
    //wfHelp::yml_dump($roles);
    
    $role = $always;
    
    if(wfArray::issetAndTrue($_SESSION, 'secure')){
      if($is_authenticated){
        $role = array_merge($role, $is_authenticated);
      }
      if(wfArray::isKey($_SESSION, 'role')){
        $role = array_merge($role, wfArray::get($_SESSION, 'role'));
      }
    }else{
      if($not_authenticated){
        $role = array_merge($role, $not_authenticated);
      }
    }
    
    return $role;
  }
    
    
}

?>
