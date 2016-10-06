<?php
class wfElement {
    
  public static function isSecurezzz($element){

    /**
     * If settings/secure is set we check if user has role.
     */
    if(wfArray::isKey($element, 'settings/secure')){
      $secure = wfArray::get($element, 'settings/secure');
      $role = wfUser::getRole();
//      wfHelp::yml_dump($secure);
//      wfHelp::yml_dump($role);
      $hide = true;
      foreach ($secure as $key => $value) {
        if(!$value){
          if(in_array($key, $role)){
            $hide = false; break;
          }
        }
      }
      if($hide){
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
    
    
  }

    
    
}

?>
