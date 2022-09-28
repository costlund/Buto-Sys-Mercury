<?php
class wfDocument {
  private static $find_and_get_by_id = null;
  private static $find_and_get_id = null;
  private $element_one_tag = array('meta', 'link', 'img', 'text', 'input');
  private $element_one_line = array('script', 'h1');
  private $element_globals = array();
  public static $mode = 'html';
  /**
   * Set to 1 if capture html in content param and also render.
   * Set to 2 if capture html in content param only and NOT render.
   */
  public static $capture = null;
  private static $content = null;
  /**
   * Mode.
   */
  public static function setModeSvg() {wfDocument::$mode='svg'; }
  public static function setModeHtml(){wfDocument::$mode='html';}
  /**
   * Get content and reset capture and content.
   */
  public static function getContent(){
    $str = wfDocument::$content;
    wfDocument::$capture = null;
    wfDocument::$content = null;
    return $str;
  }
  /**
   * Role validate.
   * @param type $element
   * @return boolean
   */
  private static function validateRole($element){
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
  private static function validateSession($element){
    if(wfArray::isKey($element, 'settings/session')){
      $user = wfUser::getSession();
      if(wfArray::get($element, 'settings/session/allow')===false){
        //If allow is set and false.
        foreach (wfArray::get($element, 'settings/session/item') as $key => $value) {
          $item = new PluginWfArray($value);
          if($user->get($item->get('param'))==$item->get('value')){
            return false;
          }
        }
      }else{
        //If allow not set or is set and true.
        $render = false;
        foreach (wfArray::get($element, 'settings/session/item') as $key => $value) {
          $item = new PluginWfArray($value);
          if($user->get($item->get('param'))==$item->get('value')){
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
  /**
   * Validate settings.
   * @param array $element
   * @return boolean
   * @throws Exception
   */
  public static function validateSettings($element){
    if(!self::validateRole($element)){
      return false;
    }
    if(!self::validateSession($element)){
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
    /**
     * SERVER_NAME
     */
    if(wfArray::get($element, 'settings/server_name')){
      if(wfArray::get($element, 'settings/server_name/item')){
        if(in_array($_SERVER['SERVER_NAME'], wfArray::get($element, 'settings/server_name/item'))){
          if(!wfArray::get($element, 'settings/server_name/allow')){
            // We found server_name in the list AND allow is false.
            return false;
          }
        }else{
          if(wfArray::get($element, 'settings/server_name/allow')){
            // We did not found server_name in the list AND allow is true.
            return false;
          }
        }
      }
    }
    /**
     * PAGE
     */
    if(wfArray::get($element, 'settings/page')){
      if(wfArray::get($element, 'settings/page/item')){
        if(wfArray::get($element, 'settings/page/allow')){
          $element = wfArray::set($element, 'settings/page/render', false);
          foreach(wfArray::get($element, 'settings/page/item') as $v){
            if(wfSettings::match_wildcard($v, wfServer::getRequestUri())){
              $element = wfArray::set($element, 'settings/page/render', true);
            }
          }
        }else{
          $element = wfArray::set($element, 'settings/page/render', true);
          foreach(wfArray::get($element, 'settings/page/item') as $v){
            if(wfSettings::match_wildcard($v, wfServer::getRequestUri())){
              $element = wfArray::set($element, 'settings/page/render', false);
            }
          }
        }
        if(!wfArray::get($element, 'settings/page/render')){
          return false;
        }
      }
    }
    /**
     * tag (string or array)
     */
    if(wfArray::get($element, 'settings/tag')){
      if(!is_array(wfArray::get($element, 'settings/tag'))){
        if(wfArray::get($element, 'settings/tag') != wfGlobals::get('tag')){
          return false;
        }
      }else{
        if(!in_array(wfGlobals::get('tag'), wfArray::get($element, 'settings/tag'))){
          return false;
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
    }
    /**
     * Date.
     * Show or hide element depending on date params.
     * settings/date
     * settings/date/allow
     * settings/date/from
     * settings/date/to
     */
    if(wfArray::get($element, 'settings/date')){
      if(!wfArray::isKey($element, 'settings/date/allow')){
        $element = wfArray::set($element, 'settings/date/allow', true);
      }
      if(!wfArray::isKey($element, 'settings/date/from')){
        $element = wfArray::set($element, 'settings/date/from', time());
      }else{
        $element = wfArray::set($element, 'settings/date/from', strtotime(wfArray::get($element, 'settings/date/from')));
      }
      if(!wfArray::isKey($element, 'settings/date/to')){
        $element = wfArray::set($element, 'settings/date/to', time());
      }else{
        $element = wfArray::set($element, 'settings/date/to', strtotime(wfArray::get($element, 'settings/date/to')));
      }
      $element = wfArray::set($element, 'settings/date/now', time());
      if(wfArray::get($element, 'settings/date/allow') && (wfArray::get($element, 'settings/date/from') > time() || wfArray::get($element, 'settings/date/to') < time())){
        /**
         * Hide element if "from" after date or "to" before date.
         */
        return false;
      }else if(!wfArray::get($element, 'settings/date/allow') && wfArray::get($element, 'settings/date/from') <= time() && wfArray::get($element, 'settings/date/to') >= time()){
        /**
         * Hide element if "from" before date and "to" after date.
         */
        return false;
      }
    }
    /**
     * Param.
     * Check for param settings to render or not render widget depending on allow value.
        settings:
          param:
            name: param_name
            value: some_param_value
            allow: true (optional, default=true)
     */
    if(wfArray::get($element, 'settings/param')){
      if(!wfArray::isKey($element, 'settings/param/allow')){
        $element = wfArray::set($element, 'settings/param/allow', true);
      }
      /**
       * If allow=true and param NOT match.
       */
      if(wfArray::get($element, 'settings/param/allow') && wfRequest::get(wfArray::get($element, 'settings/param/name'))!=wfArray::get($element, 'settings/param/value')){
        return false;
      }
      /**
       * If allow=false and param match.
       */
      if(!wfArray::get($element, 'settings/param/allow') && wfRequest::get(wfArray::get($element, 'settings/param/name'))==wfArray::get($element, 'settings/param/value')){
        return false;
      }
    }
    /**
     * Disabled.
     * settings/disabled
     * Element should not be rendered if settings/disabled is set and true.
     */
    if(wfDocument::isElementDisabled($element)){return false;}
    /**
     * Enabled (also check for enable parameter and do the opposite).
     * settings/enabled
     */
    if(!wfDocument::isElementEnabled($element)){return false;}
    /**
     * Disable element if a file not exist.
     */
    if(!wfDocument::isElementEnabledIfFileExist($element)){return false;}
    /**
     * settings/i18n/language
     */
    if(isset($element['settings']['i18n']['language'])){
      if(!is_array($element['settings']['i18n']['language'])){
        if($element['settings']['i18n']['language'] != wfI18n::getLanguage()){
          return false;
        }
      }else{
        if(!in_array(wfI18n::getLanguage(), $element['settings']['i18n']['language'])){
          return false;
        }
      }
    }
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
    return true;
  }
  private function renderInnerHTML($element){
    if(isset($element['settings']['innerHTML'])){
      if(!is_array($element['settings']['innerHTML'])){
        throw new Exception(__CLASS__.' says: Param settings/innerHTML must be array!');
      }else{
        foreach($element['settings']['innerHTML'] as $v){
          $i = new PluginWfArray($v);
          if(wfFilesystem::fileExist(wfGlobals::getAppDir().$i->get('file'))){
            wfPlugin::includeonce('wf/yml');
            $temp = new PluginWfYml(wfGlobals::getAppDir().$i->get('file'));
            $element['innerHTML'] = $temp->get($i->get('path_to_key'));
            //$element['innerHTML'] = 'test';
            //wfHelp::yml_dump($element);
            break;
          }
        }
      }
      //wfHelp::yml_dump($element, true);
    }
    return $element;
  }
  /**
   * Render start tag.
   * @param mixed $element Array or string.
   * @param int $i Level.
   * @return boolean
   * @throws Exception
   */
  private function renderStartTag($element, $i){
    /**
     * If element is a string it should be like for example 'yml:/theme/[theme]/layout/navbar.yml'
     */
    if(!is_array($element)){
      $element = wfSettings::getSettingsFromYmlString($element);
    }
    /**
     * Event.
     */
    $element = wfEvent::run('document_render_element', $element);
    if(!$element){
      return false;
    }
    /**
     * Validate.
     */
    $allowed_keys = array('text', 'data', 'type', 'innerHTML', 'attribute', 'settings', 'code', '_');
    foreach ($element as $key => $value) {
      if(!in_array($key, $allowed_keys) && !strstr($key, 'zzz')){ // zzz is WF developers method to set things in sleep :-)
        wfHelp::yml_dump($element);
        throw new Exception("None supported key $key. Are you passing an object and missing the get() method?");
      }
    }
    if(!wfDocument::validateSettings($element)){
      return false;
    }
    /**
     * Set values.
     */
    $element = self::checkServer($element);
    /**
     * element_globals
     */
    if(isset($element['settings']['globals'])){
      $from = array();
      foreach ($element['settings']['globals'] as $key => $value) {
        /**
         * Get current value to restore in method renderEndTag.
         */
        $from[] = array('path_to_key' => $value['path_to_key'], 'value' => wfGlobals::get($value['path_to_key']));
        wfGlobals::set($value['path_to_key'], $value['value']);
      }
      $this->element_globals[$i] = $from;
      unset($from);
    }
    /**
     * settings/innerHTML
     */
    $element = $this->renderInnerHTML($element);
    /**
     * Special for tag A.
     */
    if($element['type']=='a' && !isset($element['attribute']['href'])){
      $element['attribute']['href']='#!';
    }
    if($element['type']=='a'){
      $element['attribute']['href'] = str_replace('[class]', wfArray::get($GLOBALS, 'sys/class'), $element['attribute']['href']);
    }
    /**
     * title
     */
    if($element['type']=='title'){
      $element['innerHTML'] = wfGlobals::getGlobalsFromString($element['innerHTML']);
    }
    /**
     * Replace attribute/src [theme] in attribute/(src/style).
     */
    if(isset($element['attribute']['src'])){$element['attribute']['src'] = str_replace('[theme]', wfSettings::getTheme(), $element['attribute']['src']);}
    if(isset($element['attribute']['src'])){$element['attribute']['src'] = str_replace('[tag]', wfGlobals::get('tag'), $element['attribute']['src']);}
    if(isset($element['attribute']['style'])){$element['attribute']['style'] = str_replace('[theme]', wfSettings::getTheme(), $element['attribute']['style']);}
    /**
     * Replace innerHTML [[class]] for special usage to pick up from javascript.
     */
    if(wfArray::isKey($element, 'innerHTML') && !is_array(wfArray::get($element, 'innerHTML'))){
      $element['innerHTML'] = str_replace("[[class]]", wfArray::get($GLOBALS, 'sys/class'), $element['innerHTML']);
    }
    if(wfArray::isKey($element, 'attribute/onclick')){
      $element['attribute']['onclick'] = str_replace("[[class]]", wfArray::get($GLOBALS, 'sys/class'), wfArray::get($element, 'attribute/onclick'));
    }
    /**
     * Replace [theme] in attribute/href.
     */
    if(isset($element['attribute']['href'])){
      $element['attribute']['href'] = wfSettings::replaceTheme($element['attribute']['href']);
    }
    /**
     * Widget or Element.
     */
    if($element['type']=='widget'){
      /**
       * Widget.
       */
      $data = $element['data'];
      /**
       * Set widget in Globals.
       */
      $GLOBALS['sys']['widget'] = $element;
      // Trying to set data of it is a link to yml file.
      if(wfArray::get($data, 'data') && !is_array(wfArray::get($data, 'data'))){
        $data['data'] = wfSettings::getSettingsFromYmlString(wfArray::get($data, 'data'));
      }
      if(!wfArray::get($GLOBALS, 'sys/settings/plugin/'.$data['plugin'].'/enabled')){
        throw new Exception('Plugin '.$data['plugin'].' is not enabled.');
      }
      $data['file'] = wfPlugin::includeonce($data['plugin']);
      $obj = wfSettings::getPluginObj($data['plugin']) ;
      $method = 'widget_'.$data['method'];
      if(!method_exists($obj, $method)){
        throw new Exception('Widget '.$data['method'].' in plugin '.$data['plugin'].' does not exist.');
      }
      $obj->$method($data);
    }  else {
      /**
       * Element.
       */
      if($element['type']=='text' && wfDocument::$mode=='html'){
        if(isset($element['text'])){
          $this->_echo_($element['text']."\n"); 
        }elseif(isset($element['innerHTML']) && !is_array($element['innerHTML'])){
          if(substr($element['innerHTML'], 0, 4)=='yml:'){
            $temp = self::ymlFromInnerHtml($element['innerHTML']);
            if(!is_array($temp)){
              $this->_echo_($temp);
            }else{
              wfDocument::renderElement($temp);
            }
          }elseif(substr($element['innerHTML'], 0, 5)=='file:'){
            echo wfSettings::getFileContent($element['innerHTML']);
          }  else {
            $this->_echo_($element['innerHTML']);
          }
        }
        return true;
      }
      $this->_echo_(str_repeat(" ", $i*2)."<".$element['type']);
      $document_render_string = true;
      if(isset($element['settings']['i18n']) && $element['settings']['i18n']===false){
        $document_render_string = false;
      }elseif(isset($element['settings']['event']['document_render_string']['disabled']) && $element['settings']['event']['document_render_string']['disabled']==true){
        $document_render_string = false;
      }elseif(in_array($element['type'], array('script'))){
        $document_render_string = false;
      }
      if(isset($element['attribute'])){
        foreach ($element['attribute'] as $attribute => $value) {
          if(is_array($value)){
            if($attribute=='class'){
              $value = $this->array_to_values($value);
            }elseif($attribute!='style'){
              $value = $this->array_to_json($value);
            }else{
              $value = $this->array_to_string($value);
            }
          }else{
            $value = wfSettings::getServerFromString($value);
            if(isset($element['settings']['method']) && $element['settings']['method']){
              $value = wfSettings::getSettingsFromMethod($value);
            }
            $value = self::handleOutput($value);
            /**
             * Attribute content, lang.
             * We should consider to translate attributes via element settings (also)...
             */
            if(($attribute == 'content'  || $attribute == 'lang'  || $attribute == 'data-original-title'  || strstr($attribute, 'data-content') ) && $document_render_string){
              $value = wfEvent::run('document_render_string', $value);
            }
            /**
             * Attribute value if input type button.
             */
            if($element['type'] == 'input' && isset($element['attribute']['type']) && $element['attribute']['type']=='button' && $attribute=='value' && $document_render_string){
              $value = wfEvent::run('document_render_string', $value);
            }
          }
          $this->_echo_(' '.$attribute.'="'.$value.'"');
        }
      }
      $this->_echo_(">");
      if(isset($element['innerHTML']) && !is_array($element['innerHTML'])){
        $innerHTML = $element['innerHTML'];
        $innerHTML = wfSettings::replaceTheme($innerHTML);
        $innerHTML = wfSettings::getSettingsFromYmlString($innerHTML);
        $innerHTML = wfSettings::getFileContent($innerHTML);
        if(isset($element['settings']['method']) && $element['settings']['method']){
          $innerHTML = wfSettings::getSettingsFromMethod($innerHTML);
        }
        if(!is_array($innerHTML)){
          if(!in_array($element['type'], array('script', 'style')) && $document_render_string){
            $innerHTML = wfEvent::run('document_render_string', $innerHTML);
          }
          /**
           * title 
           */
          if($element['type']=='title' && wfHelp::isLocalhost()){
            $innerHTML .=' (localhost)';
          }
          $this->_echo_($innerHTML);
        }else{
          wfDocument::renderElement($innerHTML);
        }
      }
      if(isset($element['code']))     {$this->_echo_($element['code']."\n");}
    }
    return true;
  }
  /**
   * Echo.
   */
  private function _echo_($str){
    if(wfDocument::$capture != null){
      wfDocument::$content .= $str;
    }
    if(wfDocument::$capture != 2){
      echo $str;
    }
  }
  /**
   * Check if element should be disabled.
   */
  private static function isElementDisabled($element){
    if(isset($element['settings']) && array_key_exists('disabled', $element['settings'])){
      if($element['settings']['disabled']){
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
  /**
   * Check if element should be enabled.
   */
  private static function isElementEnabled($element){
    if(isset($element['settings']) && array_key_exists('enabled', $element['settings'])){
      if(!$element['settings']['enabled']){
        return false;
      }else{
        return true;
      }
    }else{
      return true;
    }
  }
  /**
   * Check if element should be enabled regardin of if a file exist.
   */
  private static function isElementEnabledIfFileExist($element){
    if(isset($element['settings']) && array_key_exists('file_exist', $element['settings'])){
      return wfFilesystem::fileExist(wfGlobals::getAppDir().$element['settings']['file_exist']);
    }else{
      return true;
    }
  }
  /**
   * Handle output.
   * @param type $value
   * @return string
   */  
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
  /**
   * Get yml from innerHTML string.
   * @param string $innerHTML
   * @return array
   * @throws Exception
   */
  private static function ymlFromInnerHtml($innerHTML){
    $temp = preg_split('/:/', $innerHTML);
    if(sizeof($temp)==3){
      return wfSettings::getSettings(trim($temp[1]), trim($temp[2]));
    }elseif(sizeof($temp)==2){
      return wfSettings::getSettings(trim($temp[1]));
    }else{
      throw new Exception('Params is missing when using yml: in innerHTML.');
    }
  }
  /**
   * Render end tag.
   * @param type $element
   * @param type $i
   * @return type
   * @throws Exception
   */
  private function renderEndTag($element, $i){
    /**
     * If element is a string it should be like for example 'yml:/theme/[theme]/layout/navbar.yml'
     */
    if(!is_array($element)){
      $element = wfSettings::getSettingsFromYmlString($element);
    }
    /**
     * 
     */
    if(wfDocument::isElementDisabled($element)){return null;}
    if(!wfDocument::isElementEnabled($element)){return null;}
    if(!wfDocument::isElementEnabledIfFileExist($element)){return null;}
    if($element['type']=='widget'){return null;}
    if(array_search($element['type'], $this->element_one_tag)===false){
      $this->_echo_("</".$element['type'].">\n"); 
      $checkLoad = wfDocument::checkLoad($element);
      if($checkLoad){
        if(isset($element['attribute']['id'])){
          $this->_echo_("<script> if(PluginWfAjax){ PluginWfAjax.load('".$element['attribute']['id']."', '".$checkLoad."'); }</script>");
        }  else {
          throw new Exception('Element attribute ID is not set when using load: in innerHTML.');
        }
      }
    }
    /**
     * element_globals
     */
    if(isset($element['settings']['globals'])){
      /**
       * Restore values set in method renderStartTag.
       */
      foreach ($this->element_globals[$i] as $key => $value) {
        wfGlobals::set($value['path_to_key'], $value['value']);
      }
      unset($this->element_globals[$i]);
    }
  }
  private static function checkServer($array){
    if(isset($array['innerHTML']) && !is_array($array['innerHTML'])){
      $array['innerHTML'] = wfSettings::getServerFromString($array['innerHTML']);
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
        $temp = preg_split('/:/', $array['innerHTML']);
        $temp[1] = str_replace('[class]', wfArray::get($GLOBALS, 'sys/class'), $temp[1]);
        /**
         * 
         */
        foreach(wfRequest::getAll() as $k => $v){
          $temp[1] = str_replace("[$k]", $v, $temp[1]);
        }
        /**
         * 
         */
        return $temp[1];
      }
    }
    return null;
  }
  public static function setBySessionTag($element){
    $element = new PluginWfArray($element);
    /**
     * Search keys.
     */
    wfPlugin::includeonce('wf/arraysearch');
    $search = new PluginWfArraysearch(true);
    $search->data = array('key_name' => '', 'key_value' => '', 'data' => $element->get());
    $keys = $search->get();
    /**
     * Loop keys.
     */
    foreach ($keys as $key => $value) {
      $element->set(substr($value, 1), wfSettings::getGlobalsFromString($element->get(substr($value, 1))));
    }
    /**
     * 
     */
    return $element->get();
  }
  /**
   * Set all yml: for an element.
   */
  public static function setElementYml($element){
    $element = new PluginWfArray($element);
    /**
     * Search keys.
     */
    wfPlugin::includeonce('wf/arraysearch');
    $search = new PluginWfArraysearch(true);
    $search->data = array('key_name' => '', 'key_value' => '', 'data' => $element->get());
    $keys = $search->get();
    /**
     * Loop keys.
     */
    foreach ($keys as $key => $value) {
      $element->set(substr($value, 1), wfSettings::getSettingsFromYmlString($element->get(substr($value, 1))));
    }
    /**
     * 
     */
    return $element->get();
  }
  /**
   * Render elements from folder.
   * @param string __DIR__
   * @param string Yml filename without extension.
   * @param string Optional foldername, default is element.
   */
  public static function renderElementFromFolder($dir, $filename, $folder = 'element'){
    $element = new PluginWfYml("$dir/$folder/$filename.yml");
    wfDocument::renderElement($element);
  }
  /**
   * Render elements.
   * If param capture is true one could pick up html in param content once.
   * @param type $element
   */
  public static function renderElement($element){
    /**
     * 
     */
    if(gettype($element)=='object'){
      $element = $element->get();
    }
    /**
     * Check if $element is array.
     */
    if(gettype($element)!='array' && gettype($element)!='NULL'){
      throw new Exception('Error in wfDocument::renderElement() because param is not an array!');
    }
    /**
     * Replace session params.
     */
    $element = wfDocument::setBySessionTag($element);
    /**
     * Replace yml params.
     */
    $element = wfDocument::setElementYml($element);
    /**
     * 
     */
    $document = new wfDocument();
    if($element){
      foreach ($element as $key0 => $value0) {
        /**
         * Generated via wfDocument.class.ods
         */
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
        if(!$document->renderStartTag($value21, 21)){continue;}if(isset($value21['innerHTML']) && is_array($value21['innerHTML'])){foreach ($value21['innerHTML'] as $key22 => $value22) {
        if(!$document->renderStartTag($value22, 22)){continue;}if(isset($value22['innerHTML']) && is_array($value22['innerHTML'])){foreach ($value22['innerHTML'] as $key23 => $value23) {
        if(!$document->renderStartTag($value23, 23)){continue;}if(isset($value23['innerHTML']) && is_array($value23['innerHTML'])){foreach ($value23['innerHTML'] as $key24 => $value24) {
        if(!$document->renderStartTag($value24, 24)){continue;}if(isset($value24['innerHTML']) && is_array($value24['innerHTML'])){foreach ($value24['innerHTML'] as $key25 => $value25) {
        if(!$document->renderStartTag($value25, 25)){continue;}if(isset($value25['innerHTML']) && is_array($value25['innerHTML'])){foreach ($value25['innerHTML'] as $key26 => $value26) {
        if(!$document->renderStartTag($value26, 26)){continue;}if(isset($value26['innerHTML']) && is_array($value26['innerHTML'])){foreach ($value26['innerHTML'] as $key27 => $value27) {
        if(!$document->renderStartTag($value27, 27)){continue;}if(isset($value27['innerHTML']) && is_array($value27['innerHTML'])){foreach ($value27['innerHTML'] as $key28 => $value28) {
        if(!$document->renderStartTag($value28, 28)){continue;}if(isset($value28['innerHTML']) && is_array($value28['innerHTML'])){foreach ($value28['innerHTML'] as $key29 => $value29) {
        if(!$document->renderStartTag($value29, 29)){continue;}if(isset($value29['innerHTML']) && is_array($value29['innerHTML'])){foreach ($value29['innerHTML'] as $key30 => $value30) {
        if(!$document->renderStartTag($value30, 30)){continue;}if(isset($value30['innerHTML']) && is_array($value30['innerHTML'])){foreach ($value30['innerHTML'] as $key31 => $value31) {
        if(!$document->renderStartTag($value31, 31)){continue;}if(isset($value31['innerHTML']) && is_array($value31['innerHTML'])){foreach ($value31['innerHTML'] as $key32 => $value32) {
        if(!$document->renderStartTag($value32, 32)){continue;}if(isset($value32['innerHTML']) && is_array($value32['innerHTML'])){foreach ($value32['innerHTML'] as $key33 => $value33) {
        if(!$document->renderStartTag($value33, 33)){continue;}if(isset($value33['innerHTML']) && is_array($value33['innerHTML'])){foreach ($value33['innerHTML'] as $key34 => $value34) {
        if(!$document->renderStartTag($value34, 34)){continue;}if(isset($value34['innerHTML']) && is_array($value34['innerHTML'])){foreach ($value34['innerHTML'] as $key35 => $value35) {
        if(!$document->renderStartTag($value35, 35)){continue;}if(isset($value35['innerHTML']) && is_array($value35['innerHTML'])){foreach ($value35['innerHTML'] as $key36 => $value36) {
        if(!$document->renderStartTag($value36, 36)){continue;}if(isset($value36['innerHTML']) && is_array($value36['innerHTML'])){foreach ($value36['innerHTML'] as $key37 => $value37) {
        if(!$document->renderStartTag($value37, 37)){continue;}if(isset($value37['innerHTML']) && is_array($value37['innerHTML'])){foreach ($value37['innerHTML'] as $key38 => $value38) {
        if(!$document->renderStartTag($value38, 38)){continue;}if(isset($value38['innerHTML']) && is_array($value38['innerHTML'])){foreach ($value38['innerHTML'] as $key39 => $value39) {
        if(!$document->renderStartTag($value39, 39)){continue;}if(isset($value39['innerHTML']) && is_array($value39['innerHTML'])){foreach ($value39['innerHTML'] as $key40 => $value40) {
        if(!$document->renderStartTag($value40, 40)){continue;}if(isset($value40['innerHTML']) && is_array($value40['innerHTML'])){foreach ($value40['innerHTML'] as $key41 => $value41) {
        if(!$document->renderStartTag($value41, 41)){continue;}if(isset($value41['innerHTML']) && is_array($value41['innerHTML'])){foreach ($value41['innerHTML'] as $key42 => $value42) {
        if(!$document->renderStartTag($value42, 42)){continue;}if(isset($value42['innerHTML']) && is_array($value42['innerHTML'])){foreach ($value42['innerHTML'] as $key43 => $value43) {
        if(!$document->renderStartTag($value43, 43)){continue;}if(isset($value43['innerHTML']) && is_array($value43['innerHTML'])){foreach ($value43['innerHTML'] as $key44 => $value44) {
        if(!$document->renderStartTag($value44, 44)){continue;}if(isset($value44['innerHTML']) && is_array($value44['innerHTML'])){foreach ($value44['innerHTML'] as $key45 => $value45) {
        if(!$document->renderStartTag($value45, 45)){continue;}if(isset($value45['innerHTML']) && is_array($value45['innerHTML'])){foreach ($value45['innerHTML'] as $key46 => $value46) {
        if(!$document->renderStartTag($value46, 46)){continue;}if(isset($value46['innerHTML']) && is_array($value46['innerHTML'])){foreach ($value46['innerHTML'] as $key47 => $value47) {
        if(!$document->renderStartTag($value47, 47)){continue;}if(isset($value47['innerHTML']) && is_array($value47['innerHTML'])){foreach ($value47['innerHTML'] as $key48 => $value48) {
        if(!$document->renderStartTag($value48, 48)){continue;}if(isset($value48['innerHTML']) && is_array($value48['innerHTML'])){foreach ($value48['innerHTML'] as $key49 => $value49) {
        if(!$document->renderStartTag($value49, 49)){continue;}if(isset($value49['innerHTML']) && is_array($value49['innerHTML'])){foreach ($value49['innerHTML'] as $key50 => $value50) {
        if(!$document->renderStartTag($value50, 50)){continue;}if(isset($value50['innerHTML']) && is_array($value50['innerHTML'])){foreach ($value50['innerHTML'] as $key51 => $value51) {
          if(!$document->renderStartTag($value51, 51)){continue;}
          $document->renderEndTag($value51, 51);
        /**
         * Generated via wfDocument.class.ods
         */
        }}$document->renderEndTag($value50, 50);
        }}$document->renderEndTag($value49, 49);
        }}$document->renderEndTag($value48, 48);
        }}$document->renderEndTag($value47, 47);
        }}$document->renderEndTag($value46, 46);
        }}$document->renderEndTag($value45, 45);
        }}$document->renderEndTag($value44, 44);
        }}$document->renderEndTag($value43, 43);
        }}$document->renderEndTag($value42, 42);
        }}$document->renderEndTag($value41, 41);
        }}$document->renderEndTag($value40, 40);
        }}$document->renderEndTag($value39, 39);
        }}$document->renderEndTag($value38, 38);
        }}$document->renderEndTag($value37, 37);
        }}$document->renderEndTag($value36, 36);
        }}$document->renderEndTag($value35, 35);
        }}$document->renderEndTag($value34, 34);
        }}$document->renderEndTag($value33, 33);
        }}$document->renderEndTag($value32, 32);
        }}$document->renderEndTag($value31, 31);
        }}$document->renderEndTag($value30, 30);
        }}$document->renderEndTag($value29, 29);
        }}$document->renderEndTag($value28, 28);
        }}$document->renderEndTag($value27, 27);
        }}$document->renderEndTag($value26, 26);
        }}$document->renderEndTag($value25, 25);
        }}$document->renderEndTag($value24, 24);
        }}$document->renderEndTag($value23, 23);
        }}$document->renderEndTag($value22, 22);
        }}$document->renderEndTag($value21, 21);
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
  /**
   * Set head.
   * @param type $value
   * @param type $key
   */
  public static function setHead($value, $key = null){
    if($key){
      $GLOBALS['settings']['document']['html']['innerHTML']['head']['innerHTML'][$key] = $value;
    }else{
      $GLOBALS['settings']['document']['html']['innerHTML']['head']['innerHTML'][] = $value;
    }
  }
  /**
   * Set disabled.
   * @param type $id
   * @param type $bool
   */
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
  /**
   * Format array key id.
   * @param string $id
   * @return string
   */
  public static function formatArrayKeyId($id){
    $id = str_replace("[" ,"['", $id);
    $id = str_replace("]" ,"']", $id);
    return $id;
  }
  /**
   * Get by id.
   * @param type $id
   * @return type
   */
  public static function getById($id){
    wfDocument::findAndGetById($GLOBALS['settings']['document'], $id);
    $find_and_get_by_id = wfDocument::$find_and_get_by_id;
    if($find_and_get_by_id){
      $temp = null;
      eval("\$temp = $find_and_get_by_id;");
      return $temp;
    }else{return null;}
  }
  /**
   * Get id.
   * @param type $id
   * @return type
   */
  public static function getId($id){
    wfDocument::$find_and_get_id = null;
    wfDocument::findAndGetId($GLOBALS['settings']['document'], $id);
    $find_and_get_id = wfDocument::$find_and_get_id;
    if($find_and_get_id){
      $temp = null;
      eval("\$temp = $find_and_get_id;");
      return $temp;
    }else{return null;}
  }
  /**
   * Set id.
   * @param type $id
   * @param type $array
   */
  public static function setId($id, $array){
    wfDocument::findAndGetId($GLOBALS['settings']['document'], $id);
    $find_and_get_id = wfDocument::$find_and_get_id;
    if($find_and_get_id){
      eval("$find_and_get_id = \$array;");
    }
  }
  /**
   * Set document.
   * @param type $value
   */  
  public static function setDocument($value){ $GLOBALS['settings']['document'] = $value; }
  /**
   * Get document.
   * @return type
   */
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
    if($innerHTML || $innerHTML=='0'){ $array['innerHTML'] = $innerHTML; }
    if($attribute){ $array['attribute'] = $attribute; }
    if($settings){$array['settings'] = $settings;}
    return $array;
  }
  /**
   * AS an object.
   * @param type $type
   * @param type $innerHTML
   * @param type $attribute
   * @param type $settings
   * @return \PluginWfArray
   */
  public static function createHtmlElementAsObject($type, $innerHTML = null, $attribute = array(), $settings = null){
    wfPlugin::includeonce('wf/array');
    return new PluginWfArray(wfDocument::createHtmlElement($type, $innerHTML, $attribute, $settings));
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
   * Create widget.
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
  /**
   * Hanlde execute.
   * @param string $method
   * @throws Exception
   */
  public static function handleExecute($method){
    $module = $GLOBALS['class'];
    $method = strtolower(substr($method, 7));
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
    if(wfArray::isKey($module_settings, 'doc/'.$method.'/settings/_rewrite_globals')){
      wfArray::set($GLOBALS, '_rewrite', wfArray::get($module_settings, 'doc/'.$method.'/settings/_rewrite_globals'));
      $module_settings = wfArray::setUnset($module_settings, 'doc/'.$method.'/settings/_rewrite_globals');
      $GLOBALS = wfArray::rewrite($GLOBALS);
    }
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
          if(!is_array($module_settings['doc'][$method]['settings']['layout'])){
            throw new Exception('Param layout is not an array.');
          }
          foreach ($module_settings['doc'][$method]['settings']['layout'] as $key => $value) {
            $layout = wfArray::get($module_settings, 'settings/layout/'.$value);
            if($layout){
              if(isset($layout['innerHTML'])){
                wfDocument::setById($content_id, $layout['innerHTML']);
              }  else {
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
            $arr = wfDocument::getId($value['id']);
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
      }
    }
  }
  /**
   * Merge layout.
   * @param type $page
   * @return type
   * @throws Exception
   */
  public static function mergeLayout($page){
    /**
     * error handling
     */
    if(!wfArray::get($GLOBALS, 'sys/layout_path')){
      throw new Exception("Param sys/layout_path is not set!");
    }
    if(!wfArray::isKey($page, 'content')){
      return null;
    }
    /**
     * 
     */
    $path = null;
    $layout_path = wfArray::get($GLOBALS, 'sys/app_dir').'/'.wfArray::get($GLOBALS, 'sys/layout_path');
    if(!wfRequest::get('_time')){
      /**
       * page
       */
      if(wfArray::isKey($page, 'settings/layout') && $layout_path){
        /**
         * layouts
         */
        $layouts = wfArray::get($page, 'settings/layout');
        $temp = null;
        foreach ($layouts as $key => $value) {
          /**
           * If value is a path it should start with /.
           */
          if(substr($value, 0, 1)=='/'){
            $filename = $value;
          }else{
            $filename = $layout_path.'/'.$value.'.yml';
          }
          /**
           * 
           */
          if(file_exists($filename)){
            $layout = sfYaml::load($filename);
            if(!isset($layout['content'])){
              throw new Exception("Key content is not set in $filename!");
            }
            if(!isset($layout['settings']['path'])){
              throw new Exception("Key settings/path is not set in $filename!");
            }
            if(!$temp){
              /**
               * First layout.
               */
              $temp = $layout['content'];
              $path = $layout['settings']['path'];
            }else{
              /**
               * Other layouts.
               */
              $temp = wfArray::set($temp, $path, $layout['content']);
              $path = $path.'/'.$layout['settings']['path'];
            }
            wfDocument::rewrite_globals($layout);
          }else{
            throw new Exception("Could not find file $filename!");
          }
          $temp = wfArray::set($temp, $path, wfArray::get($page, 'content'));
        }
        $page['content'] = $temp;
        wfDocument::rewrite_globals($page);
      }else{
        wfDocument::rewrite_globals($page);
      }
    }else{
      /**
       * An ajax request due to _time param.
       */
      wfDocument::rewrite_globals($page);
    }
    wfArray::set($GLOBALS, 'sys/page', $page);
    wfArray::set($GLOBALS, 'sys/path_to_content', $path);
    return null;
  }
  private static function rewrite_globals($data){
    if(wfArray::get($data, 'settings/rewrite_globals')){
      foreach (wfArray::get($data, 'settings/rewrite_globals') as $v) {
        $GLOBALS = wfArray::set($GLOBALS, $v['key'], $v['value']);
      }
    }
    return null;
  }
  /**
   * 
   * @param PluginWfArray $settings
   * @param int $position Where in page file layout should be.
   * @param PluginWfArray $page
   * @return PluginWfArray
   * @throws Exception
   */
  public static function insertAdminLayout($settings, $position, $page){
    if(!$settings->get('admin_layout')){
      return $page;
    }
    $file = wfGlobals::getAppDir(). wfSettings::replaceDir($settings->get('admin_layout'));
    if(wfFilesystem::fileExist($file)){
      $page->set('settings/layout', wfArray::insertToPosition($position, $page->get('settings/layout'), $file));
      return $page;
    }else{
      throw new Exception('wfDocument::insertAdminLayout says it could not find file '.$file.'.');
    }
  }
  /**
   * Convert array to key1:value1;key2:value2; to render element style attributes.
   */
  private function array_to_string($data){
    $str = null;
    foreach ($data as $key => $value) {
      $str .= "$key:$value;";
    }
    return $str;
  }
  /**
   * Convert array values to values in string.
   * Using for element attribute class when array is provided.
   * @param type $data Array
   * @return type String
   */
  private function array_to_values($data){
    $str = null;
    foreach ($data as $key => $value) {
      $str .= "$value ";
    }
    return $str;
  }
  /**
   * Convert array to json.
   */
  private function array_to_json($value){
    return htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8');
  }
}
