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
        /**
         * /?phpinfo=error_fatal
         */
        if($_REQUEST['phpinfo']=='error_fatal'){
          echo 'Buto says: Trying to set variable $func=fake_func() to throw an fatal error.';
          $func = eval("fake_func();");
          exit;
        }
        /**
         * /?phpinfo=error_deprecated
         */
        if($_REQUEST['phpinfo']=='error_deprecated'){
          echo 'Buto says: Method get_magic_quotes_runtime() is deprecated from PHP 7.4. Should return false below.';
          $magic_quotes = eval("get_magic_quotes_runtime();");
          exit;
        }
        /**
         * /?phpinfo=error_notice
         */
        if($_REQUEST['phpinfo']=='error_notice'){
          echo 'Buto says: Variable $test is not defined and it should return a notice.';
          echo eval("$test;");
          exit;
        }
      }
      
    }
    return null;
  }
}
