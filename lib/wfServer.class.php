<?php
/**
 * Buto class to get data from $_SERVER variable.
 */
class wfServer{
  public static function get($key = null){
    wfPlugin::includeonce('wf/array');
    $g = new PluginWfArray($_SERVER);
    return $g->get($key);
  }
  public static function getRedirectStatus()              {return wfServer::get('REDIRECT_STATUS');}
  public static function getHttpHost()                    {return wfServer::get('HTTP_HOST');}
  public static function getHttpUserAgent()               {return wfServer::get('HTTP_USER_AGENT');}
  public static function getHttpAccept()                  {return wfServer::get('HTTP_ACCEPT');}
  public static function getHttpAccept_Language()         {return wfServer::get('HTTP_ACCEPT_LANGUAGE');}
  public static function getHttpAccept_Encoding()         {return wfServer::get('HTTP_ACCEPT_ENCODING');}
  public static function getHttpCookie()                  {return wfServer::get('HTTP_COOKIE');}
  public static function getHttpConnection()              {return wfServer::get('HTTP_CONNECTION');}
  public static function getHttpUpgradeInsecureRequests() {return wfServer::get('HTTP_UPGRADE_INSECURE_REQUESTS');}
  public static function getHttpCacheControl()            {return wfServer::get('HTTP_CACHE_CONTROL');}
  public static function getPath()                        {return wfServer::get('PATH');}
  public static function getServerSignature()             {return wfServer::get('SERVER_SIGNATURE');}
  public static function getServerSoftware()              {return wfServer::get('SERVER_SOFTWARE');}
  public static function getServerName()                  {return wfServer::get('SERVER_NAME');}
  public static function getServerAddr()                  {return wfServer::get('SERVER_ADDR');}
  public static function getServerPort()                  {return wfServer::get('SERVER_PORT');}
  public static function getRemoteAddr()                  {return wfServer::get('REMOTE_ADDR');}
  public static function getDocumentRoot()                {return wfServer::get('DOCUMENT_ROOT');}
  public static function getServerAdmin()                 {return wfServer::get('SERVER_ADMIN');}
  public static function getScriptFilename()              {return wfServer::get('SCRIPT_FILENAME');}
  public static function getRemotePort()                  {return wfServer::get('REMOTE_PORT');}
  public static function getRedirectUrl()                 {return wfServer::get('REDIRECT_URL');}
  public static function getGatewayInterface()            {return wfServer::get('GATEWAY_INTERFACE');}
  public static function getServerPotocol()               {return wfServer::get('SERVER_PROTOCOL');}
  public static function getRequestMethod()               {return wfServer::get('REQUEST_METHOD');}
  public static function getQueryString()                 {return wfServer::get('QUERY_STRING');}
  public static function getRequestUri()                  {return wfServer::get('REQUEST_URI');}
  public static function getScriptName()                  {return wfServer::get('SCRIPT_NAME');}
  public static function getPhpSelf()                     {return wfServer::get('PHP_SELF');}
  public static function getRequestTimeFloat()            {return wfServer::get('REQUEST_TIME_FLOAT');}
  public static function getRequestTime()                 {return wfServer::get('REQUEST_TIME');}
  public static function calcProtocol(){
    if(wfServer::get('HTTPS')=='on'){
      return 'https';
    }elseif(strstr(strtolower(wfServer::getServerPotocol()), 'https')){
      return 'https';
    }else{
      return 'http';
    }
  }
  public static function calcUrl($full = false){
    if($full){
      return wfServer::calcProtocol().'://'.wfServer::getHttpHost().wfServer::getRequestUri();
    }else{
      return wfServer::calcProtocol().'://'.wfServer::getHttpHost();
    }
  }
}
