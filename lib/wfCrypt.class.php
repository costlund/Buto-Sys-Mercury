<?php
class wfCrypt {
    
    public static function getHashAndSaltAsString($password){
      $uid = uniqid(mt_rand(), true);
      $salt = crypt($uid, $uid);
      $hash = crypt($password, $salt);
      return $hash.' '.$salt;
    }
    public static function isValid($password, $string){
      $arr = explode(' ', $string);
      if(sizeof($arr) != 2){
        return false;
      }elseif($arr[0]==  crypt($password, $arr[1])){
        return true;
      }else{
        return false;
      }
    }
    /**
     * Get unic id.
     * @return string
     */
    public static function getUid(){
      return str_replace('.', '', uniqid(mt_rand(), true));
    }
    
}

?>
