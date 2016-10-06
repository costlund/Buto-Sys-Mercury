<?php

class wfForm {
  
  
  public static function bind($form){
    //Bind form to post params...
    foreach ($form['items'] as $key => $value) {
        
        $str = wfRequest::get($key);
        if($form['items'][$key]['type']=='checkbox'){
            if($str=='on'){$str=true;}
        }
        $form['items'][$key]['post_value'] = $str;
    }
    return $form;
  }
  
  public static function validate($form){
    //Validate mandatory.
    foreach ($form['items'] as $key => $value) {
        if(isset($value['mandatory']) && $value['mandatory']){
            if(strlen($value['post_value'])){
                $form['items'][$key]['is_valid'] = true;
            }else{
                $form['items'][$key]['is_valid'] = false;
                $form['items'][$key]['errors'][] = __('Empty');
            }
        }else{
            $form['items'][$key]['is_valid'] = true;
        }
    }
    
    //Validate email.
    foreach ($form['items'] as $key => $value) {
        if($value['is_valid']){
            if(isset($value['validate_as_email']) && $value['validate_as_email']){
                if (!filter_var($value['post_value'], FILTER_VALIDATE_EMAIL)) {
                    // invalid emailaddress
                    //$form['items'][$key]['is_valid_text'] = 'Epost är felaktig!';
                    $form['items'][$key]['errors'][] = __('Email is not correct.');
                    $form['items'][$key]['is_valid'] = false;
                }                
            }
        }
    }
    
    //Set form is_valid.
    $form['is_valid'] = true;
    foreach ($form['items'] as $key => $value) {
        if(!$value['is_valid']){
            $form['is_valid'] = false;
            $form['errors'][] = __('Error:');
            break;
        }
    }
    
    return $form;
  }
  
  public static function bindAndValidate($form){
      $form = wfForm::bind($form);
      $form = wfForm::validate($form);
    return $form;
  }
  
  
  public static function getComponent($array){ //Borde vi kunna ta bort....(141024)
      
      if(array_key_exists('type', $array)){
          $s = null;
          $lable = null;   if(array_key_exists('lable', $array)){$lable = $array['lable'];}
          
          switch ($array['type']) {
              case 'button':
                  //wfHelp::print_r($array);
                  $s = '<button_attribute_>'.$lable.'</button>';
                  break;

              default:
                  break;
          }
          
          if(array_key_exists('onclick', $array)){$s = str_replace('_attribute_', ' onclick="'.$array['onclick'].'"_attribute_', $s);}
          if(array_key_exists('disabled', $array) && $array['disabled']){$s = str_replace('_attribute_', ' disabled="'.$array['disabled'].'"_attribute_', $s);}
          
          
          $s = str_replace('_attribute_', '', $s);
          return $s;
      }else{
          return null;
      }
      
  }
  
  public static function setErrorField($form, $field, $message){
    $form['is_valid'] = false;
    $form['items'][$field]['is_valid'] = false;
    $form['items'][$field]['errors'][] = $message;
    return $form;
  }
  
  
}

?>
