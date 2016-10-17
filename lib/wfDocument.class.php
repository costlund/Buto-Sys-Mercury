<?php
class wfDocument {
  
  private static $find_and_get_by_id = null;
  private static $find_and_get_id = null;
  private $element_one_tag = array('meta', 'link', 'img', 'text', 'input');
  private $element_one_line = array('script', 'h1');
  
  
  private static function validateRole($element){
    
//    if(wfArray::get($element, 'type')=='widget' && wfArray::get($element, 'data/plugin') == 'amcharts/amcharts'){
//      wfHelp::yml_dump($element);
//    }
    
    if(wfArray::isKey($element, 'settings/role')){
      $user_role = wfUser::getRole();
      if(wfArray::get($element, 'settings/role/allow')===false){
        //If allow is set and false.
        //Not render if user has a role.
        foreach (wfArray::get($element, 'settings/role/item') as $key => $value) {
          if(in_array($value, $user_role)){
            return false;
          }
        }
      }else{
        //If allow not set or is set and true.
        //Render if user has a role.
        $render = false;
        foreach (wfArray::get($element, 'settings/role/item') as $key => $value) {
          if(in_array($value, $user_role)){
            $render = true; break;
          }
        }
        if(!$render){
          return false;
        }
      }
    }
    return true;
  }
  
  public static function validateSettings($element){
    
    
    
    if(!self::validateRole($element)){
      return false;
    }
    
    
    if(isset($element['settings']['superadmin'])){
      if($element['settings']['superadmin'] && !wfArray::get($_SESSION, 'superadmin')){
        //settings/superadmin is true and user is not superadmin.
        return false;
      }elseif(!$element['settings']['superadmin'] && wfArray::get($_SESSION, 'superadmin')){
        //settings/superadmin is false and user is superadmin.
        return false;
      }
    }
    
    //#Element/IP
    if(wfArray::get($element, 'settings/ip')){
      if(wfArray::get($element, 'settings/ip/item')){
        if(in_array($_SERVER['REMOTE_ADDR'], wfArray::get($element, 'settings/ip/item'))){
          if(!wfArray::get($element, 'settings/ip/allow')){
            // We found ip in the list AND allow is false.
            return false;
          }
        }else{
          if(wfArray::get($element, 'settings/ip/allow')){
            // We did not found ip in the list AND allow is true.
            return false;
          }
        }
      }
    }
    //#Element/Cookie
    /**
      cookie:
        allow / deny:
          name: hide_vimeo
          value: 1
     */
    if(wfArray::get($element, 'settings/cookie')){
//      if(wfArray::isKey($element, 'settings/cookie/item')){
//        foreach (wfArray::get($element, 'settings/cookie/item') as $key => $value) {
//          if(array_key_exists($value['name'], $_COOKIE) && $_COOKIE[$value['name']]==$value['value']){
//            if(wfArray::get($element, 'settings/cookie/allow')){
//              //We render element.  
//            }else{
//              return false;
//            }
//          }else{
//            //Client does not have the cookie or cookie has wrong value.
//            return false;
//          }
//        }
      
      if(wfArray::isKey($element, 'settings/cookie/deny')){
        if(array_key_exists(wfArray::get($element, 'settings/cookie/deny/name'), $_COOKIE) && $_COOKIE[wfArray::get($element, 'settings/cookie/deny/name')]==wfArray::get($element, 'settings/cookie/deny/value')){
          //Client has cookie and correct value, deny.
          return false;
        }
      }
      if(wfArray::isKey($element, 'settings/cookie/allow')){
        if(array_key_exists(wfArray::get($element, 'settings/cookie/allow/name'), $_COOKIE) && $_COOKIE[wfArray::get($element, 'settings/cookie/allow/name')]==wfArray::get($element, 'settings/cookie/allow/value')){
          //Client has cookie and correct value, allow.
        }else{
          return false;
        }
      }
      
      //wfHelp::yml_dump($_COOKIE, true);
      //wfHelp::yml_dump($element, true);
    }
    
    
    
    
    if(isset($element['settings']['disabled']) && $element['settings']['disabled']){return false;}
    if(isset($element['settings']['target']) && $element['settings']['target']!=wfHelp::detectScreen()){return false;}
    if(isset($element['settings']['security'])){
      $ok = false;
      $user_security = wfUser::getSecurity();
      foreach ($element['settings']['security'] as $key => $value) {
        if(array_key_exists($key, $user_security)){ //User has this security.
          if($user_security[$key]=='%'){ //User has % for this module it will be rendered.
            $ok = true;
            break;
          }else{
            if(is_array($value)){ //User has array and element also.
              foreach ($user_security[$key] as $key2 => $value2) {
                if(array_key_exists($key2, $value)){ //User has same key as element and it will be rendered.
                  $ok = true;
                  break;
                }
              }
            }elseif($value != '%'){
              throw new Exception('Security value is not an array and should contain % (key:'.$key.', value:'.$value.').');
            }elseif($value=='%'){ //User has not % for this module and it will not be rendered.
              
            }
          }
        }
      }
      if(!$ok){
        return false;
      }
    }
    
    /**
     * If settings/secure is set we check if user has role.
     */
//    if(wfArray::isKey($element, 'settings/secure')){
//      $secure = wfArray::get($element, 'settings/secure');
//      //wfHelp::yml_dump($secure);
//      $role = wfUser::getRole();
//      //wfHelp::yml_dump($role);
//      $hide = true;
//      foreach ($secure as $key => $value) {
//        if(!$value){
//          if(in_array($key, $role)){
//            $hide = false; break;
//          }
//        }
//      }
//      if($hide){
//        return false;
//      }
//    }

//    if(wfElement::isSecure($element)){
//      return false;
//    }
    return true;
  }
  
  private function renderStartTag($element, $i){
    $element = wfEvent::run('document_render_element', $element);
    if(!$element){
      return false;
    }
//    if(!isset($element['type'])){
//      return false;
//    }
    
    
    
    $allowed_keys = array('text', 'data', 'type', 'innerHTML', 'attribute', 'settings', 'code');
    foreach ($element as $key => $value) {
      if(!in_array($key, $allowed_keys) && !strstr($key, 'zzz')){ // zzz is WF developers method to set things in sleep :-)
        wfHelp::yml_dump($element);
        throw new Exception("None supported key $key. Are you passing an object and missing the get() method?");
      }
    }
    
//    //#Element/IP
//    if(wfArray::get($element, 'settings/ip')){
//      if(wfArray::get($element, 'settings/ip/item')){
//        if(in_array($_SERVER['REMOTE_ADDR'], wfArray::get($element, 'settings/ip/item'))){
//          if(!wfArray::get($element, 'settings/ip/allow')){
//            // We found ip in the list AND allow is false.
//            return false;
//          }
//        }else{
//          if(wfArray::get($element, 'settings/ip/allow')){
//            // We did not found ip in the list AND allow is true.
//            return false;
//          }
//        }
//      }
//    }
//    if(isset($element['settings']['disabled']) && $element['settings']['disabled']){return false;}
//    if(isset($element['settings']['target']) && $element['settings']['target']!=wfHelp::detectScreen()){return false;}
//    if(isset($element['settings']['security'])){
//      $ok = false;
//      $user_security = wfUser::getSecurity();
//      foreach ($element['settings']['security'] as $key => $value) {
//        if(array_key_exists($key, $user_security)){ //User has this security.
//          if($user_security[$key]=='%'){ //User has % for this module it will be rendered.
//            $ok = true;
//            break;
//          }else{
//            if(is_array($value)){ //User has array and element also.
//              foreach ($user_security[$key] as $key2 => $value2) {
//                if(array_key_exists($key2, $value)){ //User has same key as element and it will be rendered.
//                  $ok = true;
//                  break;
//                }
//              }
//            }elseif($value != '%'){
//              throw new Exception('Security value is not an array and should contain % (key:'.$key.', value:'.$value.').');
//            }elseif($value=='%'){ //User has not % for this module and it will not be rendered.
//              
//            }
//          }
//        }
//      }
//      if(!$ok){
//        return false;
//      }
//    }
    
    if(!wfDocument::validateSettings($element)){
      return false;
    }
    
    
//    // If innerHTML has value load:/doc/something we ensure that we has an id.
//    if(wfDocument::checkLoad($element) && !isset($element['attribute']['id'])){
//      $element['attribute']['id'] = wfCrypt::getUid();
//    }
    
    
    $element = self::checkGlobals($element);
    
    
    // Special for tag A.
    if($element['type']=='a' && !isset($element['attribute']['href'])){$element['attribute']['href']='#';}
    if($element['type']=='a'){
      $element['attribute']['href'] = wfSettings::getSettingsFromYmlString($element['attribute']['href']);
      $element['attribute']['href'] = str_replace('[class]', wfArray::get($GLOBALS, 'sys/class'), $element['attribute']['href']);
    }
    
    // Replace attribute/src [theme].
    if(isset($element['attribute']['src'])){$element['attribute']['src'] = str_replace('[theme]', wfSettings::getTheme(), $element['attribute']['src']);}
    if(isset($element['attribute']['style'])){$element['attribute']['style'] = str_replace('[theme]', wfSettings::getTheme(), $element['attribute']['style']);}
    
    // Replace innerHTML [[class]]
    if(wfArray::isKey($element, 'innerHTML')){
      $element['innerHTML'] = str_replace("[[class]]", wfArray::get($GLOBALS, 'sys/class'), $element['innerHTML']);
    }
    
    if(isset($element['attribute']['href'])){
      $element['attribute']['href'] = wfSettings::replaceTheme($element['attribute']['href']);
    }
    
    
    //wfHelp::echoecho(substr($element['type'], 0, 3));
    
    if($element['type']=='widget'){
      $data = $element['data'];
      
      // Trying to set data of it is a link to yml file.
      if(wfArray::get($data, 'data') && !is_array(wfArray::get($data, 'data'))){
        $data['data'] = wfSettings::getSettingsFromYmlString(wfArray::get($data, 'data'));
        
        if(!is_array($data['data'])){
          //echo $data['data'];
          $data['data'] = wfSettings::getGlobalsFromString($data['data']);
        }
        
      }
      
      

      //wfHelp::yml_dump($data);
      //wfHelp::yml_dump($GLOBALS['sys'], true);
      
      if(!wfArray::get($GLOBALS, 'sys/settings/plugin/'.$data['plugin'].'/enabled')){
        throw new Exception('Plugin '.$data['plugin'].' is not enabled.');
      }
      $data['file'] = wfPlugin::includeonce($data['plugin']);
//      $data['file'] = wfSettings::getAppDir().'/plugin/'.$data['plugin'].'/action.class.php';
//      if(!wfFilesystem::fileExist($data['file'], true)){
//        throw new Exception('File '.$data['file'].' does not exist.');
//      }else{
//        include_once $data['file'];
//      }
      $obj = wfSettings::getPluginObj($data['plugin']) ;
      $method = 'widget_'.$data['method'];
      
      if(!method_exists($obj, $method)){
        throw new Exception('Widget '.$data['method'].' in plugin '.$data['plugin'].' does not exist.');
      }
      
      $obj->$method($data);
    }elseif(substr($element['type'], 0, 3)=='wf_'){
      
//        echo $element['type'];
//        return null;
        
//      if($element['type'] == 'wf_developer'){
//        $element['innerHTML'] = $this->renderWfDeveloper(); 
//        $element['type'] = 'textarea';
//      }else if($element['type'] == 'wf_form'){
//        $form = $element['data'];
//        include '../b/layout/comp/form.php';
//        
//      }else if($element['type'] == 'wf_form_send'){
//        $form = $element['data'];
//        include '../b/layout/comp/form_send.php';
//      }else if($element['type'] == 'wf_actions'){
//        $data = $element['data'];
//        include '../b/layout/comp/actions.php';
//      }else if($element['type'] == 'wf_plot'){
//        $data = $element['data'];
//        include '../b/layout/comp/plot.php';
//      }else if($element['type'] == 'wf_template'){
//        $data = $element['data'];
//        include '..'.$element['data']['filename'];
//      }else if($element['type'] == 'wf_table'){
//        $data = $element['data'];
//        include '../b/layout/comp/table.php';
//      }else if($element['type'] == 'wf_icon'){
//          $filename = dirname(__FILE__).'/../../b/config/svg_icon.yml';
//          if(file_exists($filename)){
//              $svg_icon = sfYaml::load($filename);
//              if(isset($svg_icon['svg_icon'][$element['data']])){
//                $this->renderElement(array($svg_icon['svg_icon'][$element['data']]));
//              }else{echo '[icon:'.$element['data'].']';}
//          }
//      }else if($element['type'] == 'wf_infobar'){
//        
//        $arr = wfDocument::createWfElement('wf_icon', wfHelp::detectScreen());
//        $a = wfDocument::createHtmlElement('a', array($arr), array('href' => '#', 'onclick' => "wfOpenWindow('set_screen', '/user/setScreenInfo', 300, 300, '".__('Screen info')."')"));
//        $this->renderElement(array($a));
//        
//        if(isset($_SESSION['secure']) && $_SESSION['secure']){$icon = 'user_check';}else{$icon = 'user_uncheck';}
//        $arr = wfDocument::createWfElement('wf_icon', $icon);
//        $a = wfDocument::createHtmlElement('a', array($arr), array('href' => '/secure/login'));
//        $this->renderElement(array($a));
//        
//        
//        //$settings = $GLOBALS['settings'];
//        //if(isset($settings['secure']) && $settings['secure']){$icon = 'lock';}else{$icon = 'unlock';}
//        //if(isset($GLOBALS['unsecure']) && $GLOBALS['unsecure']){$icon = 'unlock';}else{$icon = 'lock';}
//        
//        $settings = wfSettings::loadThemeConfigSettings(); //Denna borde vi inte hämta en gång till utan via $GLOBALS?
//        //wfHelp::print_r($settings['security']['unsecure'], true);
//        $icon = 'lock';
//        if(isset($settings['security']['unsecure'][$GLOBALS['class']])){
//          if(!is_array($settings['security']['unsecure'][$GLOBALS['class']])){
//            $icon = 'unlock';
//          }elseif(isset($settings['security']['unsecure'][$GLOBALS['class']][$GLOBALS['method']])){
//            $icon = 'unlock';
//          }
//        }
//        
//        $arr = wfDocument::createWfElement('wf_icon', $icon);
//        $this->renderElement(array($arr));
//        
//        $arr = wfDocument::createWfElement('wf_icon', 'language');
//        $a = wfDocument::createHtmlElement('a', array($arr), array('href' => '#', 'onclick' => "wfOpenWindow('language', '/user/language', 300, 300, '".__('Language')."')"));
//        $this->renderElement(array($a));
//        
//        $arr = wfDocument::createWfElement('wf_icon', 'share');
//        $a = wfDocument::createHtmlElement('a', array($arr), array('href' => '#', 'onclick' => "wfOpenWindow('share', '/user/share', 400, 500, '".__('Share')."')"));
//        $this->renderElement(array($a));
//        
//      }else if($element['type'] == 'wf_form_upload'){
//        $data = $element['data'];
//        include '../b/layout/comp/form_upload.php';
//      }else if($element['type'] == 'wf_lable_value_row'){
//        $data = $element['data'];
//        include '../b/layout/comp/lable_value_row.php';
//      }else if($element['type'] == 'wf_tab'){
//        $data = $element['data'];
//        include '../b/layout/comp/tab.php';
//      }else 
        
      if($element['type'] == 'wf_method'){
        $data = $element['data'];
        throw new Exception('type=wf_method is not longer in use ('.$data['plugin'].', '.$data['method'].').');
//        if(isset($data['plugin'])){
//          //wfHelp::yml_dump($data);
//          if(!wfArray::get($GLOBALS, 'sys/settings/plugin/'.$data['plugin'].'/enabled')){
//            throw new Exception('Plugin '.$data['plugin'].' is not enabled.');
//          }
//          $data['file'] = wfSettings::getAppDir().'/plugin/'.$data['plugin'].'/action.class.php';
//          if(!wfFilesystem::fileExist($data['file'], true)){
//            throw new Exception('File '.$data['file'].' does not exist.');
//          }else{
//            include_once $data['file'];
//          }
//          $obj = wfSettings::getPluginObj($data['plugin']) ;
//          $method = 'widget_'.$data['method'];
//          $obj->$method($data);
//        }else{
//          if(isset($data['file'])){
//            $data['file'] = str_replace('[theme]', wfSettings::getTheme(), $data['file']);
////            if(!wfFilesystem::fileExist($data['file'])){
////              throw new Exception('File '.$data['file'].' does not exist.');
////            }else{
////              include_once "..".$data['file'];
////            }
//            
//            //wfHelp::yml_dump($GLOBALS['sys'], true);
//            //exit(wfArray::get($GLOBALS, 'sys/app_dir').$data['file']);
//            if(!wfFilesystem::fileExist(wfArray::get($GLOBALS, 'sys/app_dir').$data['file'])){
//              throw new Exception('File '.wfArray::get($GLOBALS, 'sys/app_dir').$data['file'].' does not exist.');
//            }else{
//              include_once wfArray::get($GLOBALS, 'sys/app_dir').$data['file'];
//            }
//          }
//          $obj = new $data['class'];
//          $method = 'widget_'.$data['method'];
//          $obj->$method($data);
//        }

      }else{
//        if($element['type'] == 'wf_upload'){
//          wfHelp::print_r($element, true);
//        }
        
        if(isset($element['data'])){
          wfComp::get('layout', str_replace('wf_', '', $element['type']), $element['data']);
        }else{
          wfComp::get('layout', str_replace('wf_', '', $element['type']));
        }
        return true;
      }
      
    }  else {
      if($element['type']=='text'){
        if(isset($element['text'])){
          echo $element['text']."\n"; 
        }elseif(isset($element['innerHTML'])){
          if(substr($element['innerHTML'], 0, 4)=='yml:'){
            echo self::ymlFromInnerHtml($element['innerHTML']);
          }  else {
            echo $element['innerHTML'];
          }

        }
        return true;
      }
      if(isset($element['target']) && $element['target']!=wfHelp::detectScreen()){return false;}
      echo str_repeat(" ", $i*2)."<".$element['type'];
      if(isset($element['attribute'])){
        foreach ($element['attribute'] as $attribute => $value) {
          $value = wfSettings::getGlobalsFromString($value);
          $value = wfSettings::getSettingsFromYmlString($value);
          echo ' '.$attribute.'="'.self::handleOutput($value).'"';
        }
      }
      echo ">";
      //if(array_search($element['type'], $this->element_one_line)===false){ echo "\n"; }
      //if(array_search($element['type'], $this->element_one_line)===false){ echo "\n"; }
      if(isset($element['innerHTML']) && !is_array($element['innerHTML'])){
        
        $innerHTML = $element['innerHTML'];
        $innerHTML = wfSettings::replaceTheme($innerHTML);
        $innerHTML = wfSettings::getGlobalsFromString($innerHTML);
        $innerHTML = wfSettings::getSettingsFromYmlString($innerHTML);
        
        $innerHTML = wfEvent::run('document_render_element_innerhtml', $innerHTML);

        /**
         * Here we will handle translation...ddd_02
         */

        
//        if(substr($element['innerHTML'], 0, 4)=='yml:'){
//          echo self::ymlFromInnerHtml($element['innerHTML']);
////        }elseif(substr($element['innerHTML'], 0, 2)=='__'){
////          echo __(substr($element['innerHTML'], 2))."\n";
//        }else{
//          echo wfSettings::replaceTheme($element['innerHTML'])."\n";
//        }
        
        echo $innerHTML;
        
      }
      if(isset($element['code']))     {echo $element['code']."\n";}
    }
    return true;
    
  }
  
  private static function handleOutput($value){
    if($value === true){
      return 'True';
    }elseif($value === false){
      return 'False';
    }elseif($value === 0 || $value === '0'){
      return '0';
    }else{
      return $value;
    }
  }
  
  private static function ymlFromInnerHtml($innerHTML){
    $temp = preg_split('/:/', $innerHTML);
    if(sizeof($temp)==3){
      return wfSettings::getSettings(trim($temp[1]), trim($temp[2]));
    }else{
      throw new Exception('Params is missing when using yml: in innerHTML.');
    }
  }
  
  private static function handleInnerHTMLzzz($str){
    if(substr($str, 0, 2)=='__'){
      if(isset($GLOBALS['language'][substr($str, 2)])){
        return $GLOBALS['language'][substr($str, 2)];
      }else{
        return $str;
      }
    }else{
      return $str;
    }
  }
  private function renderEndTag($element, $i){
    if(isset($element['disabled']) && $element['disabled']){return null;}
    if(substr($element['type'], 0, 3)=='wf_'){return null;}
    if($element['type']=='widget'){return null;}
    if(isset($element['target']) && $element['target']!=wfHelp::detectScreen()){return null;}
    if(array_search($element['type'], $this->element_one_tag)===false){
      if($element['type'] != 'textarea'){
        //echo str_repeat(" ", $i*2);
      }
      echo "</".$element['type'].">\n";
      $checkLoad = wfDocument::checkLoad($element);
      if($checkLoad){
        //wfHelp::print_r($element);
        if(isset($element['attribute']['id'])){
          echo "<script> if(PluginWfAjax){ PluginWfAjax.load('".$element['attribute']['id']."', '".$checkLoad."'); }</script>";
        }  else {
          throw new Exception('Element attribute ID is not set when using load: in innerHTML.');
        }
      }
    }
  }
  
  
  
  
  private static function checkGlobals($array){
    if(isset($array['innerHTML']) && !is_array($array['innerHTML'])){
//      if(substr($array['innerHTML'], 0, 8)=='globals:'){
//        $temp = preg_split('/:/', $array['innerHTML']);
//        $array['innerHTML'] = wfArray::get($GLOBALS, $temp[1]);
//      }
      $array['innerHTML'] = wfSettings::getGlobalsFromString($array['innerHTML']);
    }
    return $array;
  }


  /**
   * Check if innerHTML begin with load: .
   * @param type $array
   * @return type
   */  
  private static function checkLoad($array){
    if(isset($array['innerHTML']) && !is_array($array['innerHTML'])){
      if(substr($array['innerHTML'], 0, 5)=='load:'){
        //$temp = split(':', $array['innerHTML']);
        
        $temp = preg_split('/:/', $array['innerHTML']);
        
        //echo "<script>alert('".$temp[1]."');</script>";
        $temp[1] = str_replace('[class]', wfArray::get($GLOBALS, 'sys/class'), $temp[1]);
        return $temp[1];
      }
    }
    return null;
  }
  
  public static function renderElementzzz($element){
    $document = new wfDocument();
    //var_dump($element); exit;
    if($element){
      foreach ($element as $key0 => $value0) {

        if(!$document->renderStartTag($value0, 0)){continue;}
        if(isset($value0['innerHTML']) && is_array($value0['innerHTML'])){
        foreach ($value0['innerHTML'] as $key1 => $value1) {

        if(!$document->renderStartTag($value1, 1)){continue;}
        if(isset($value1['innerHTML']) && is_array($value1['innerHTML'])){
        foreach ($value1['innerHTML'] as $key2 => $value2) {

        if(!$document->renderStartTag($value2, 2)){continue;}
        if(isset($value2['innerHTML']) && is_array($value2['innerHTML'])){
        foreach ($value2['innerHTML'] as $key3 => $value3) {

        if(!$document->renderStartTag($value3, 3)){continue;}
        if(isset($value3['innerHTML']) && is_array($value3['innerHTML'])){
        foreach ($value3['innerHTML'] as $key4 => $value4) {

        if(!$document->renderStartTag($value4, 4)){continue;}
        if(isset($value4['innerHTML']) && is_array($value4['innerHTML'])){
        foreach ($value4['innerHTML'] as $key5 => $value5) {

        if(!$document->renderStartTag($value5, 5)){continue;}
        if(isset($value5['innerHTML']) && is_array($value5['innerHTML'])){
        foreach ($value5['innerHTML'] as $key6 => $value6) {

        if(!$document->renderStartTag($value6, 6)){continue;}
        if(isset($value6['innerHTML']) && is_array($value6['innerHTML'])){
        foreach ($value6['innerHTML'] as $key7 => $value7) {

        if(!$document->renderStartTag($value7, 7)){continue;}
        if(isset($value7['innerHTML']) && is_array($value7['innerHTML'])){
        foreach ($value7['innerHTML'] as $key8 => $value8) {

        if(!$document->renderStartTag($value8, 8)){continue;}
        if(isset($value8['innerHTML']) && is_array($value8['innerHTML'])){
        foreach ($value8['innerHTML'] as $key9 => $value9) {

        if(!$document->renderStartTag($value9, 9)){continue;}
        if(isset($value9['innerHTML']) && is_array($value9['innerHTML'])){
        foreach ($value9['innerHTML'] as $key10 => $value10) {

        if(!$document->renderStartTag($value10, 10)){continue;}
        if(isset($value10['innerHTML']) && is_array($value10['innerHTML'])){
        foreach ($value10['innerHTML'] as $key11 => $value11) {

        if(!$document->renderStartTag($value11, 11)){continue;}
        if(isset($value11['innerHTML']) && is_array($value11['innerHTML'])){
        foreach ($value11['innerHTML'] as $key12 => $value12) {

        if(!$document->renderStartTag($value12, 12)){continue;}
        if(isset($value12['innerHTML']) && is_array($value12['innerHTML'])){
        foreach ($value12['innerHTML'] as $key13 => $value13) {
          
          if(!$document->renderStartTag($value13, 13)){continue;}



          $document->renderEndTag($value13, 13);
        }}$document->renderEndTag($value12, 12);
        }}$document->renderEndTag($value11, 11);
        }}$document->renderEndTag($value10, 10);
        }}$document->renderEndTag($value9, 9);
        }}$document->renderEndTag($value8, 8);
        }}$document->renderEndTag($value7, 7);
        }}$document->renderEndTag($value6, 6);
        }}$document->renderEndTag($value5, 5);
        }}$document->renderEndTag($value4, 4);
        }}$document->renderEndTag($value3, 3);
        }}$document->renderEndTag($value2, 2);
        }}$document->renderEndTag($value1, 1);
        }}$document->renderEndTag($value0, 0);
      }
    }
    
    
    
    
  }
  
  public static function renderElement($element){
    $document = new wfDocument();
    //var_dump($element); exit;
    if($element){
      foreach ($element as $key0 => $value0) {

        // Generated via wfDocument.class.ods
        if(!$document->renderStartTag($value0, 0)){continue;}if(isset($value0['innerHTML']) && is_array($value0['innerHTML'])){foreach ($value0['innerHTML'] as $key1 => $value1) {
        if(!$document->renderStartTag($value1, 1)){continue;}if(isset($value1['innerHTML']) && is_array($value1['innerHTML'])){foreach ($value1['innerHTML'] as $key2 => $value2) {
        if(!$document->renderStartTag($value2, 2)){continue;}if(isset($value2['innerHTML']) && is_array($value2['innerHTML'])){foreach ($value2['innerHTML'] as $key3 => $value3) {
        if(!$document->renderStartTag($value3, 3)){continue;}if(isset($value3['innerHTML']) && is_array($value3['innerHTML'])){foreach ($value3['innerHTML'] as $key4 => $value4) {
        if(!$document->renderStartTag($value4, 4)){continue;}if(isset($value4['innerHTML']) && is_array($value4['innerHTML'])){foreach ($value4['innerHTML'] as $key5 => $value5) {
        if(!$document->renderStartTag($value5, 5)){continue;}if(isset($value5['innerHTML']) && is_array($value5['innerHTML'])){foreach ($value5['innerHTML'] as $key6 => $value6) {
        if(!$document->renderStartTag($value6, 6)){continue;}if(isset($value6['innerHTML']) && is_array($value6['innerHTML'])){foreach ($value6['innerHTML'] as $key7 => $value7) {
        if(!$document->renderStartTag($value7, 7)){continue;}if(isset($value7['innerHTML']) && is_array($value7['innerHTML'])){foreach ($value7['innerHTML'] as $key8 => $value8) {
        if(!$document->renderStartTag($value8, 8)){continue;}if(isset($value8['innerHTML']) && is_array($value8['innerHTML'])){foreach ($value8['innerHTML'] as $key9 => $value9) {
        if(!$document->renderStartTag($value9, 9)){continue;}if(isset($value9['innerHTML']) && is_array($value9['innerHTML'])){foreach ($value9['innerHTML'] as $key10 => $value10) {
        if(!$document->renderStartTag($value10, 10)){continue;}if(isset($value10['innerHTML']) && is_array($value10['innerHTML'])){foreach ($value10['innerHTML'] as $key11 => $value11) {
        if(!$document->renderStartTag($value11, 11)){continue;}if(isset($value11['innerHTML']) && is_array($value11['innerHTML'])){foreach ($value11['innerHTML'] as $key12 => $value12) {
        if(!$document->renderStartTag($value12, 12)){continue;}if(isset($value12['innerHTML']) && is_array($value12['innerHTML'])){foreach ($value12['innerHTML'] as $key13 => $value13) {
        if(!$document->renderStartTag($value13, 13)){continue;}if(isset($value13['innerHTML']) && is_array($value13['innerHTML'])){foreach ($value13['innerHTML'] as $key14 => $value14) {
        if(!$document->renderStartTag($value14, 14)){continue;}if(isset($value14['innerHTML']) && is_array($value14['innerHTML'])){foreach ($value14['innerHTML'] as $key15 => $value15) {
        if(!$document->renderStartTag($value15, 15)){continue;}if(isset($value15['innerHTML']) && is_array($value15['innerHTML'])){foreach ($value15['innerHTML'] as $key16 => $value16) {
        if(!$document->renderStartTag($value16, 16)){continue;}if(isset($value16['innerHTML']) && is_array($value16['innerHTML'])){foreach ($value16['innerHTML'] as $key17 => $value17) {
        if(!$document->renderStartTag($value17, 17)){continue;}if(isset($value17['innerHTML']) && is_array($value17['innerHTML'])){foreach ($value17['innerHTML'] as $key18 => $value18) {
        if(!$document->renderStartTag($value18, 18)){continue;}if(isset($value18['innerHTML']) && is_array($value18['innerHTML'])){foreach ($value18['innerHTML'] as $key19 => $value19) {
        if(!$document->renderStartTag($value19, 19)){continue;}if(isset($value19['innerHTML']) && is_array($value19['innerHTML'])){foreach ($value19['innerHTML'] as $key20 => $value20) {
        if(!$document->renderStartTag($value20, 20)){continue;}if(isset($value20['innerHTML']) && is_array($value20['innerHTML'])){foreach ($value20['innerHTML'] as $key21 => $value21) {
          
          if(!$document->renderStartTag($value21, 21)){continue;}
          $document->renderEndTag($value21, 21);
          
          
        // Generated via wfDocument.class.ods
        }}$document->renderEndTag($value20, 20);
        }}$document->renderEndTag($value19, 19);
        }}$document->renderEndTag($value18, 18);
        }}$document->renderEndTag($value17, 17);
        }}$document->renderEndTag($value16, 16);
        }}$document->renderEndTag($value15, 15);
        }}$document->renderEndTag($value14, 14);
        }}$document->renderEndTag($value13, 13);
        }}$document->renderEndTag($value12, 12);
        }}$document->renderEndTag($value11, 11);
        }}$document->renderEndTag($value10, 10);
        }}$document->renderEndTag($value9, 9);
        }}$document->renderEndTag($value8, 8);
        }}$document->renderEndTag($value7, 7);
        }}$document->renderEndTag($value6, 6);
        }}$document->renderEndTag($value5, 5);
        }}$document->renderEndTag($value4, 4);
        }}$document->renderEndTag($value3, 3);
        }}$document->renderEndTag($value2, 2);
        }}$document->renderEndTag($value1, 1);
        }}$document->renderEndTag($value0, 0);
        
        
      }
    }
  }
  
  private static function renderWfDeveloper(){
    return null;
//      echo '<pre>';
//      echo 'serverTime:';
//      wfHelp::getServerTime();
//      echo '<br>';
//      echo '$class:'.$class.'<br>';
//      echo '$method:'.$method.'<br>';

//          echo '$_GET:';
//          print_r($_GET);
//          echo '$content:';
//          print_r($GLOBALS);
      //print_r($GLOBALS['security']);
//      print_r($GLOBALS['settings']);
    
    //return wfHelp::getYmlDump($GLOBALS['settings']['document']);
    
  }
  
  private static function renderWfForm($array){
    
  }
  
  public static function setHead($value, $key = null){
    if($key){
      $GLOBALS['settings']['document']['html']['innerHTML']['head']['innerHTML'][$key] = $value;
    }else{
      $GLOBALS['settings']['document']['html']['innerHTML']['head']['innerHTML'][] = $value;
    }
  }
  public static function setDisabled($id, $bool){
    $obj = wfDocument::getId($id);
    if($obj){
      $obj['settings']['disabled'] = $bool;
      wfDocument::setId($id, $obj);
    }
  }
  
  /**
   * Set innerHTML by id.
   * @param type $array
   * @param type $id
   * @param type $new_value
   * @param type $keys
   */
  public static function findAndSetById($array, $id, $new_value, $keys = null){
    foreach ($array as $key => $value) {
      if(isset($value['attribute']['id']) && $value['attribute']['id'] == $id){
        $array_key = '$GLOBALS[\'settings\'][\'document\']'.wfDocument::formatArrayKeyId($keys.'['.$key.']')."['innerHTML']";
        eval("$array_key = \$new_value;");
        break;
      }
      if(isset($value['innerHTML']) && is_array($value['innerHTML'])){
        wfDocument::findAndSetById($value['innerHTML'], $id, $new_value, $keys.'['.$key.'][innerHTML]');
      }
    }
  }
  /**
   * Get innerHTML of given attribute id.
   * @param type $array
   * @param type $id
   * @param type $keys
   * @return null
   */
  public static function findAndGetById($array, $id, $keys = null){
    $array_key = null;
    foreach ($array as $key => $value) {
      if(isset($value['attribute']['id']) && $value['attribute']['id'] == $id){
        wfDocument::$find_and_get_by_id = '$GLOBALS[\'settings\'][\'document\']'.wfDocument::formatArrayKeyId($keys.'['.$key.']')."['innerHTML']";
      }
      if(isset($value['innerHTML']) && is_array($value['innerHTML'])){
        wfDocument::findAndGetById($value['innerHTML'], $id, $keys.'['.$key.'][innerHTML]');
      }
    }
    return $array_key;
  }
  /**
   * Get array of given attribute id.
   * @param type $array
   * @param type $id
   * @param type $keys
   * @return null
   */
  public static function findAndGetId($array, $id, $keys = null){
    $array_key = null;
    foreach ($array as $key => $value) {
      if(isset($value['attribute']['id']) && $value['attribute']['id'] == $id){
        wfDocument::$find_and_get_id = '$GLOBALS[\'settings\'][\'document\']'.wfDocument::formatArrayKeyId($keys.'['.$key.']');
      }
      if(isset($value['innerHTML']) && is_array($value['innerHTML'])){
        wfDocument::findAndGetId($value['innerHTML'], $id, $keys.'['.$key.'][innerHTML]');
      }
    }
    return $array_key;
  }
  
  /**
   * Set innerHTML by its id.
   * @param type $id
   * @param type $value
   */
  public static function setById($id, $value){
    wfDocument::findAndSetById($GLOBALS['settings']['document'], $id, $value);
  }
  public static function formatArrayKeyId($id){
    $id = str_replace("[" ,"['", $id);
    $id = str_replace("]" ,"']", $id);
    return $id;
  }
  public static function getById($id){
    wfDocument::findAndGetById($GLOBALS['settings']['document'], $id);
    $find_and_get_by_id = wfDocument::$find_and_get_by_id;
    if($find_and_get_by_id){
      //echo '$find_and_get_by_id:'.$find_and_get_by_id;
      eval("\$temp = $find_and_get_by_id;");
      return $temp;
    }else{return null;}
  }
  /**
   * Ny....
   * @param type $id
   * @return type
   */
  public static function getId($id){
    wfDocument::$find_and_get_id = null;
    wfDocument::findAndGetId($GLOBALS['settings']['document'], $id);
    $find_and_get_id = wfDocument::$find_and_get_id;
    //return wfDocument::$find_and_get_id;
    //wfHelp::echoecho($find_and_get_id, false);
    if($find_and_get_id){
      eval("\$temp = $find_and_get_id;");
      return $temp;
    }else{return null;}
  }
  
  public static function setId($id, $array){
    wfDocument::findAndGetId($GLOBALS['settings']['document'], $id);
    //wfHelp::echoecho(wfDocument::$find_and_get_id, true);
    $find_and_get_id = wfDocument::$find_and_get_id;
    if($find_and_get_id){
      eval("$find_and_get_id = \$array;");
    }
    
  }
  
  public static function setDocument($value){ $GLOBALS['settings']['document'] = $value; }
  public static function getDocument(){ return $GLOBALS['settings']['document']; }
  
  /**
   * Create an element.
   * @param string $type
   * @param stringorarray $innerHTML
   * @param array $attribute
   * @param array $settings
   * @return array
   */
  public static function createHtmlElement($type, $innerHTML = null, $attribute = array(), $settings = null){
    $array = array();
    $array['type'] = $type;
    //if(strlen($innerHTML.'')){ $array['innerHTML'] = $innerHTML; }
    //if($innerHTML){ $array['innerHTML'] = $innerHTML; }
    if($innerHTML || $innerHTML=='0'){ $array['innerHTML'] = $innerHTML; }
    if($attribute){ $array['attribute'] = $attribute; }
    if($settings){$array['settings'] = $settings;}
    return $array;
  }
  /**
   * Create element.
   * @param type $type
   * @param type $data
   * @param type $settings
   * @return type
   */
  public static function createWfElement($type, $data = array(), $settings = null){
    $array = array();
    $array['type'] = $type;
    if($data){ $array['data'] = $data; }
    if($settings){$array['settings'] = $settings;}
    return $array;
  }
  
  /**
   * 
   * @param type $plugin
   * @param type $method
   * @param type $data
   * @param type $settings
   * @return type
   */
  public static function createWidget($plugin, $method, $data = null, $settings = null){
    $widget = wfDocument::createWfElement('widget', array('plugin' => $plugin, 'method' => $method, 'data' => $data), $settings);
    return $widget;
  }
  
  public static function setLanguagezzz(){
    $language = $GLOBALS['settings']['language'];
    if(isset($_COOKIE['language']) && array_key_exists($_COOKIE['language'], $language['availible'])){
      $language['user_language'] = $language['availible'][$_COOKIE['language']]; //Set from cookie.
    }else{
      $temp = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
      $user_languages = array();
      foreach ($temp as $key => $value) {
        $temp2 = explode(';', $value);
        $user_languages[$temp2[0]] = null;
      }
      foreach ($language['availible'] as $key => $value) {
        if(array_key_exists($key, $user_languages)){
          $language['user_language'] = $value; //Match in HTTP_ACCEPT_LANGUAGE;
          break;
        }
      }
      if(!isset($language['user_language'])){
        $language['user_language'] = reset($language['availible']); //No match, set default language.
      }
    }
    $GLOBALS['settings']['language'] = $language;
    
    $text = array();
    $filename = wfSettings::getAppDir().'/b/config/language.'.$language['user_language'].'.yml';
    if(file_exists($filename)){
        $text = sfYaml::load($filename);
    }
    $filename = wfSettings::getAppDir().'/a/config/language.'.$language['user_language'].'.yml';
    if(file_exists($filename)){
        $text = array_merge($text, sfYaml::load($filename));
    }
    $GLOBALS['language'] = $text;
    
    
    //wfHelp::print_r($GLOBALS, true);
    return null;
  }
  public static function getLanguagezzz($translate = false){
    //wfHelp::print_r($GLOBALS['language']);
    if(!$translate){
      return $GLOBALS['settings']['language']['user_language'];
    }  else {
      return __($GLOBALS['settings']['language']['translate'][$GLOBALS['settings']['language']['user_language']]);
    }
  }
  
  public static function setLanguageForModulezzz($module){
    $text = $GLOBALS['language'];
    $filename = wfSettings::getAppDir().'/b/module/'.$module.'/config/language.'.wfDocument::getLanguage().'.yml';
    if(file_exists($filename)){
        $text = array_merge($text, sfYaml::load($filename));
    }
    $filename = wfSettings::getAppDir().'/a/module/'.$module.'/config/language.'.wfDocument::getLanguage().'.yml';
    if(file_exists($filename)){
        $text = array_merge($text, sfYaml::load($filename));
    }
    $GLOBALS['language'] = $text;
  }
  
  
  public static function handleExecute($method){
    
    
    $module = $GLOBALS['class'];
    
    $method = strtolower(substr($method, 7)); // executeStart()...
    
    if(!wfArray::get($GLOBALS, 'settings/plugin/class_is_plugin')){
      $module_settings = wfSettings::getModuleSettings($module);
    }else{
      $filename = wfSettings::getAppDir().'/plugin/'.$module.'/config/settings.yml';
      if(file_exists($filename)){
        $module_settings = sfYaml::load($filename);
      }else{
        throw new Exception("Could not find $filename!");
      }
    }
    
    //wfHelp::yml_dump(wfArray::get($module_settings, 'doc/'.$method.'/settings/_rewrite_globals'), true);
    
    
    //If __rewrite (fixa denna också for doc settings)

    //If _rewrite_globals in doc/(page_key)/settings
    if(wfArray::isKey($module_settings, 'doc/'.$method.'/settings/_rewrite_globals')){
      wfArray::set($GLOBALS, '_rewrite', wfArray::get($module_settings, 'doc/'.$method.'/settings/_rewrite_globals'));
      $module_settings = wfArray::setUnset($module_settings, 'doc/'.$method.'/settings/_rewrite_globals');
      $GLOBALS = wfArray::rewrite($GLOBALS);
    }
    
    
    //wfHelp::print_r(wfArray::get($GLOBALS, 'settings/plugin/class_is_plugin'), true);
    //wfHelp::print_r($module_settings, true);
    
    
    
    if(!array_key_exists($method, $module_settings['doc'])){
      wfDocument::setById('body', array(wfSettings::loadThemeConfigSettings('could_not_find_the_page')));
    }else{
      
      
      
      
      //Unset values.
      if(isset($module_settings['doc'][$method]['settings']['unset'])){
        foreach ($module_settings['doc'][$method]['settings']['unset'] as $key => $value) {
          wfArray::setUnset($GLOBALS, $value);
        }
      }
      
      
      
      
      
      if(wfRequest::get('_time')){
        wfDocument::setDocument(array($module_settings['doc'][$method]));
      }else{
        $content_id = 'body';
        if(isset($module_settings['doc'][$method]['settings']['layout'])){
          //wfHelp::print_r($module_settings['doc'][$method]['settings']['layout']);
          if(!is_array($module_settings['doc'][$method]['settings']['layout'])){
            throw new Exception('Param layout is not an array.');
          }
          //wfHelp::print_r($module_settings['doc'][$method]['settings']['layout'], true);
          foreach ($module_settings['doc'][$method]['settings']['layout'] as $key => $value) {
            $layout = wfArray::get($module_settings, 'settings/layout/'.$value);
            //wfHelp::print_r($layout, true);
            if($layout){
              //echo $content_id.'<br>';
              if(isset($layout['innerHTML'])){
                wfDocument::setById($content_id, $layout['innerHTML']);
              }  else {
                //wfHelp::yml_dump($layout);
                throw new Exception('innerHTML is not set in layout.');
              }
              $content_id = $layout['content_id'];
            }else{
              throw new Exception('Could not find layout '.$value.'.');
            }
          }
        }
        wfDocument::setById($content_id, array($module_settings['doc'][$method]));
        
        
        //Document rewrite.
        if(wfArray::get($module_settings, 'document_rewrite')){
          foreach (wfArray::get($module_settings, 'document_rewrite') as $key => $value) {
            if(isset($value['disabled']) && $value['disabled']){continue;}
            //wfHelp::echoecho($value['id']);
            $arr = wfDocument::getId($value['id']);
            //wfHelp::print_r($arr);
            $item_key = null;
            if(isset($value['key'])){
              $item_key = $value['key'];
              if(strstr($item_key, '[')){
                //From start vi formate key as ['attribute']['style'].
              }else{
                //Later we do it as attribute/style.
                $item_key = wfArray::formatPathToKey($item_key);
              }
            }
            $item_value = $value['value'];
            if(is_array($item_value)){
              eval("\$arr$item_key = \$item_value;");
            }else{
              eval("\$arr$item_key = '$item_value';");
            }
            if($arr){
              wfDocument::setId($value['id'], $arr);
            }
          }
        }
        
//        $doc_root = 'doc/'.$method;
//        if(wfArray::isKey($module_settings, $doc_root.'/settings/document/title')){
//          wfDocument::setById('title', wfArray::get($module_settings, $doc_root.'/settings/document/title'));
//        }
//        
//        
//        //var_dump(wfArray::isKey($module_settings, $doc_root.'/settings/document/title_pre'));
//        
//        if(wfArray::isKey($module_settings, $doc_root.'/settings/document/title_pre')){
//          wfDocument::setById('title', wfArray::get($module_settings, $doc_root.'/settings/document/title_pre').' '.wfDocument::getById('title'));
//        }
//        if(wfArray::isKey($module_settings, $doc_root.'/settings/document/title_post')){
//          wfDocument::setById('title', wfDocument::getById('title').' '.wfArray::get($module_settings, $doc_root.'/settings/document/title_post'));
//        }
//        if(wfArray::isKey($module_settings, $doc_root.'/settings/document/description')){
//          //wfDocument::setById('description', wfArray::get($module_settings, $doc_root.'/settings/document/description', 'attribute/content'));
//          wfArray::set($GLOBALS, 'settings/document/html/innerHTML/head/innerHTML/description/attribute/content', wfArray::get($module_settings, $doc_root.'/settings/document/description', 'attribute/content')) ;
//          //wfHelp::print_r($GLOBALS, true);
//        }
        
      }
    }
  }
  
  
  /**
   * This section is about merge layouts into a page.
   */
  public static function mergeLayout($page){
    if(!wfArray::get($GLOBALS, 'sys/layout_path')){
      throw new Exception("Param sys/layout_path is not set!");
    }
    if(!wfArray::isKey($page, 'content')){
      return null;
//      wfHelp::yml_dump($page, true);
//      throw new Exception("Key content is not set in page layout!");
    }
    $path = null;
    
    $layout_path = wfArray::get($GLOBALS, 'sys/app_dir').'/'.wfArray::get($GLOBALS, 'sys/layout_path');
    
    
    
    if(!wfRequest::get('_time')){
      if(wfArray::isKey($page, 'settings/layout') && $layout_path){
        $layouts = wfArray::get($page, 'settings/layout');
        $temp = null;
        foreach ($layouts as $key => $value) {
          $filename = $layout_path.'/'.$value.'.yml';
          if(file_exists($filename)){
            $layout = sfYaml::load($filename);
            if(!isset($layout['content'])){
              throw new Exception("Key content is not set in $filename!");
            }
            if(!isset($layout['settings']['path'])){
              throw new Exception("Key settings/path is not set in $filename!");
            }
            if(!$temp){
              //First layout.
              $temp = $layout['content'];
              $path = $layout['settings']['path'];
            }else{
              //Other layouts.
              $temp = wfArray::set($temp, $path, $layout['content']);
              $path = $path.'/'.$layout['settings']['path'];
            }

            if(wfArray::get($layout, 'settings/rewrite_globals')){
              foreach (wfArray::get($layout, 'settings/rewrite_globals') as $key2 => $value2) {
                $GLOBALS = wfArray::set($GLOBALS, $value2['key'], $value2['value']);
              }
            }

          }else{
            throw new Exception("Could not find file $filename!");
          }
          $temp = wfArray::set($temp, $path, wfArray::get($page, 'content'));
        }
        $page['content'] = $temp;
        if(wfArray::get($page, 'settings/rewrite_globals')){
          foreach (wfArray::get($page, 'settings/rewrite_globals') as $key2 => $value2) {
            $GLOBALS = wfArray::set($GLOBALS, $value2['key'], $value2['value']);
          }
        }
      }else{
        if(wfArray::get($page, 'settings/rewrite_globals')){
          foreach (wfArray::get($page, 'settings/rewrite_globals') as $key2 => $value2) {
            $GLOBALS = wfArray::set($GLOBALS, $value2['key'], $value2['value']);
          }
        }
      }
    }else{
      //wfHelp::yml_dump($page, true);
      if(wfArray::get($page, 'settings/rewrite_globals')){
        foreach (wfArray::get($page, 'settings/rewrite_globals') as $key2 => $value2) {
          $GLOBALS = wfArray::set($GLOBALS, $value2['key'], $value2['value']);
        }
      }
    }
    wfArray::set($GLOBALS, 'sys/page', $page);
    wfArray::set($GLOBALS, 'sys/path_to_content', $path);
    return null;
  }
  
  
}