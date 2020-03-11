<?php
class wfPhpinfo{
  public static function show_info(){
    if(isset($_REQUEST['phpinfo'])){
      if(wfUser::hasRole('webmaster') || wfUser::hasRole('webadmin')){
        /**
         * /?phpinfo=phpinfo.
         */
        if($_REQUEST['phpinfo']=='phpinfo'){
          phpinfo();
          exit;
        }
        /**
         * /?phpinfo=session.
         */
        if($_REQUEST['phpinfo']=='session'){
          echo '<pre>';
          echo wfHelp::getYmlDump($_SESSION);
          exit;
        }
        /**
         * /?phpinfo=server.
         */
        if($_REQUEST['phpinfo']=='server'){
          echo '<pre>';
          echo wfHelp::getYmlDump($_SERVER);
          exit;
        }
        /**
         * /?phpinfo=cookie.
         */
        if($_REQUEST['phpinfo']=='cookie'){
          echo '<pre>';
          echo wfHelp::getYmlDump($_COOKIE);
          exit;
        }
      }
      if(wfUser::hasRole('webmaster')){
        /**
         * /?phpinfo=globals.
         */
        if($_REQUEST['phpinfo']=='globals'){
          echo '<pre>';
          echo wfHelp::getYmlDump(wfGlobals::get());
          exit;
        }
      }
      
    }
    return null;
  }
}
