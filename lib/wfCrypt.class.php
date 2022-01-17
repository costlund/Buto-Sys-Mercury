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
    if(sizeof($arr)==2 && $arr[0]==crypt($password, $arr[1])){
      return true;
    }elseif(sizeof($arr)==1 && $arr[0]==$password){
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
  /**
    * Encrypt.
    * @param string $string
    * @param string $key Optional (trying to get from /config/crypt.yml).
    * @return string
    * @throws Exception
    */
  public static function encrypt($string, $key = null){
    /**
      * Warning (mcrypt_create_iv)
      * https://www.php.net/manual/en/function.mcrypt-create-iv.php
      * This function was DEPRECATED in PHP 7.1.0, and REMOVED in PHP 7.2.0.
      */
    /**
      *
      */
    if(is_null($key)){
      $key = wfCrypt::getKey();
    }
    if(!$key){
      throw new Exception('Could not find any key in wfCrypt::encrypt().');
    }
    try {
      $iv = mcrypt_create_iv(
        mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
        MCRYPT_DEV_URANDOM
      );
      $encrypted = base64_encode(
          $iv .
          mcrypt_encrypt(
              MCRYPT_RIJNDAEL_128,
              hash('sha256', $key, true),
              $string,
              MCRYPT_MODE_CBC,
              $iv
          )
      );
      return $encrypted;
    }
    catch (Throwable $e) {
      return $e->getMessage();
    }
    return '(error)';
  }
  /**
    * Decrypt.
    * @param string $encrypted
    * @param string $key Optional (trying to get from /config/crypt.yml).
    * @return string
    * @throws Exception
    */
  public static function decrypt($encrypted, $key = null){
    /**
      * Warning (mcrypt_create_iv)
      * https://www.php.net/manual/en/function.mcrypt-create-iv.php
      * This function was DEPRECATED in PHP 7.1.0, and REMOVED in PHP 7.2.0.
      */
    /**
      *
      */
    if(is_null($key)){
      $key = wfCrypt::getKey();
    }
    if(!$key){
      throw new Exception('Could not find any key in wfCrypt::decrypt().');
    }
    try{
      $data = base64_decode($encrypted);
      $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
      $decrypted = rtrim(
          mcrypt_decrypt(
              MCRYPT_RIJNDAEL_128,
              hash('sha256', $key, true),
              substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
              MCRYPT_MODE_CBC,
              $iv
          ),
          "\0"
      );
      return $decrypted;
    }
    catch (Throwable $e) {
      return $e->getMessage();
    }
    return '(error)';
  }
  /**
    * Trying to retrieve key from param key in /config/crypt.yml.
    * @return string
    */
  public static function getKey(){
    $key = null;
    if(wfFilesystem::fileExist(wfArray::get($GLOBALS, 'sys/app_dir').'/theme/[theme]/config/crypt.yml')){
      $key = wfSettings::getSettings('/theme/[theme]/config/crypt.yml', 'key', false);
    }
    return $key;
  }
  /**
    * wfCrypt::decryptFromString('crypt:DBNfQCwZWmTG6qVbKd0QS8NdbYMNUUE17b5o+xXUZbc=').
    * @param string $string
    * @return string
    */
  public static function decryptFromString($string){
    if(substr($string, 0, 6)=='crypt:'){
      return wfCrypt::decrypt(substr($string, 6));
    }else{
      return $string;
    }
  }
}
