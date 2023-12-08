# Buto

<ul>
<li>Specification of a Buto system.</li>
<li>Mercury is the name of this Buto system. </li>
<li>It runs on any server supporting PHP. </li>
<li>It has no dependencies like any type of database. </li>
<li>The main configuration of a Buto system is done with yml files.</li>
<li>Only 22 files with .php extension for the hole framework.</li>
<li>HTML code is done with yml in the exact same way but with the benefit to add extra Buto system params.</li>
<li>One could work with Buto without any knowledge of PHP when building a theme. Only in plugin development PHP skills are needed.</li>
</ul>

<a name="key_0"></a>

## System

<p>Buto system is located in folder sys/mercury</p>

<a name="key_0_0"></a>

### Errors

<p>As default errors will be shown along with deprecated notice. 
In production mode we can change this in /config/settings.yml file.</p>

<a name="key_0_0_0"></a>

#### Display errors

<p>If to display errors. 
Default value is 0.</p>
<pre><code>display_errors: 1</code></pre>
<p>Set value to 0 to hide errors. </p>
<pre><code>display_errors: 0</code></pre>

<a name="key_0_0_1"></a>

#### Error reporting

<p>Default value is E_ALL to catch all errors.</p>
<pre><code>error_reporting: 'E_ALL'</code></pre>
<p>To hide deprecated errors one could set it like this.</p>
<pre><code>error_reporting: 'E_ALL ^ E_DEPRECATED'</code></pre>
<p>To hide deprecated errors and notice one could set it like this.</p>
<pre><code>error_reporting: 'E_ALL ^ E_DEPRECATED ^ E_NOTICE'</code></pre>

<a name="key_0_1"></a>

### Roles

<p>Roles visitor, unknown and client are handled by the system.</p>
<ul>
<li>visitor (always)</li>
<li>unknown (if user NOT signed in)</li>
<li>client (if user signed in)</li>
</ul>
<p>Roles normaly used by theme builders.</p>
<ul>
<li>webmaster (user has full access)</li>
<li>webadmin (application administrator)</li>
<li>developer (user is in developer team)</li>
</ul>
<p>Along with this roles plugin can handle custom roles depending on purpose. For example.</p>
<ul>
<li>invoice (user should handle invoices)</li>
<li>moderator (user is a moderator)</li>
</ul>

<a name="key_0_2"></a>

### GIT

<p>In folder sys/mercury/git there are sh files to run git status/pull/fetch for all repos. Navigate in terminal to this folder and run commands.</p>
<pre><code>sh git_status.sh
sh git_fetch.sh
sh git_pull.sh</code></pre>
<p>Sometimes when clone a git repo files get status changes. One could solve this issue by running this command.</p>
<pre><code>sh git_fileMode.sh</code></pre>

<a name="key_0_3"></a>

### Repo

<p>Download from Github.com.</p>
<pre><code>https://github.com/costlund/Buto-Sys-Mercury.git</code></pre>

<a name="key_0_4"></a>

### Time zone

<p>In /config/settings.yml one could set time zone for application (optional).
Default value is Europe/Paris.</p>
<pre><code>timezone: 'America/New_York'</code></pre>
<p>If using CURRENT_TIMESTAMP for TIMESTAMP types the system time is used. 
Change type from TIMESTAMP to DATETIME to apply time zone setting.</p>
<p>Read more about different time zones.</p>
<pre><code>https://en.wikipedia.org/wiki/List_of_tz_database_time_zones</code></pre>

<a name="key_1"></a>

## Theme

<p>Buto can have multiple themes. But in most cases there is only one involved when an application is in production.</p>

<a name="key_1_0"></a>

### Hello World

<p>An Hello World example where theme is in location /theme/hello/world. This theme only make use of one plugin wf/doc to render a page.</p>

<a name="key_1_0_0"></a>

#### Config

<p>In /config/settings.yml</p>
<pre><code>plugin_modules:
  doc:
    plugin: 'wf/doc'
default_class: doc
default_method: home</code></pre>

<a name="key_1_0_1"></a>

#### Page

<p>In /page/home.yml</p>
<pre><code>content:
  div: 
    type: div
    innerHTML:
      span:
        type: span
        attribute:
          id: min_span_tag
          style: "font-weight:bold"
        innerHTML: Hello World</code></pre>
<p>One could load content via ajax by using the load prefix.</p>
<pre><code>        innerHTML: load:/any/page</code></pre>
<p>Use request params if needed.</p>
<pre><code>        innerHTML: load:/any/page/id/[id]</code></pre>

<a name="key_1_0_2"></a>

#### Result

<p>Access your theme like this.</p>
<pre><code>http://localhost</code></pre>
<p>Or.</p>
<pre><code>http://localhost/doc/home</code></pre>

<a name="key_1_1"></a>

### Hello Buto

<p>One could have layout pages. 
I this example we show how to use two layout files. 
Also how to use plugin theme/include wich include other plugins one need to build a complet site.</p>

<a name="key_1_1_0"></a>

#### Config

<p>In /config/settings.yml</p>
<pre><code>default_class: d
default_method: home
plugin_modules:
  d:
    plugin: 'wf/doc'
plugin:
  theme:
    include:
      enabled: true</code></pre>

<a name="key_1_1_1"></a>

#### Layout files

<p>In /layout/html.yml</p>
<pre><code>settings:
  path: '1/innerHTML/1/innerHTML'
content:
  -
    type: text
    text: '&lt;!DOCTYPE html&gt;'
  -
    type: html
    attribute:
      la: globals:_SESSION/i18n/language
    innerHTML:
      -
        type: head
        innerHTML:
          -
            type: widget
            data:
              plugin: 'theme/include'
              method: include
      -
        type: body
        innerHTML: 'body...'</code></pre>
<p>In /layout/main.yml</p>
<pre><code>settings:
  path: '1/innerHTML'
content:
  -
    type: div
    innerHTML: navbar...
  -
    type: div
    innerHTML: content...
  -
    type: div
    innerHTML: footer...</code></pre>

<a name="key_1_1_2"></a>

#### Page

<p>In /page/home.yml</p>
<pre><code>settings:
  title: 'Hello Buto'
  layout:
    - html
    - main
content:
  - 
    type: div
    innerHTML:
      span:
        type: span
        attribute:
          style: "font-weight:bold"
        innerHTML: Hello Buto</code></pre>

<a name="key_1_1_3"></a>

#### Result

<p>Adress.</p>
<pre><code>http://localhost</code></pre>
<p>Browser output.</p>
<pre><code>navbar...
Hello Buto
footer...</code></pre>

<a name="key_1_2"></a>

### Theme configuration

<p>Like in Hello World example there is not much data a Theme need.</p>

<a name="key_1_2_0"></a>

#### Settings file

<p>In /config/settings.yml</p>
<p>This is basic data.</p>
<pre><code>plugin_modules:
  doc:
    plugin: 'wf/doc'
default_class: doc
default_method: home</code></pre>
<p>This data can refer to a file.</p>
<pre><code>plugin_modules:
  account:
    plugin: 'wf/account2'
    settings: yml:/theme/[theme]/config/plugin_wf_account2.yml</code></pre>

<a name="key_1_2_1"></a>

#### Folder buto_data

<p>In some case you do not want to include sensitive data in a buto project. One could use a buto_data folder in same folder as a Buto project. Access shoud be like this.                </p>
<pre><code>plugin_modules:
  account:
    plugin: 'wf/account2'
    settings: 'yml:/../buto_data/theme/[theme]/config/plugin_wf_account2.yml'</code></pre>

<a name="key_2"></a>

## Plugin

<p>Plugins can have one or many of this purposes. A theme must make use of at least one plugin to work properly.</p>
<ul>
<li>Widgets</li>
<li>Pages</li>
<li>Events</li>
<li>Methods</li>
<li>Element methods</li>
</ul>
<p>Some plugins are like a complete application. And some are designed just to include a javascript file with a widget.</p>

<a name="key_2_0"></a>

### Widgets

<p>A widget should be some HTML element on a page.</p>

<a name="key_2_1"></a>

### Pages

<p>A page where there could be layout pages involved.</p>

<a name="key_2_2"></a>

### Events

<p>Buto system or plugins can fire events.</p>

<a name="key_2_3"></a>

### Methods

<p>Methods used from other plugins.</p>

<a name="key_2_4"></a>

### Element methods

<p>Methods used from an element.
Example.</p>
<pre><code>type: body
settings:
  methods:
    -
      plugin: element/style
      method: background_cover
      data:
        src: /theme/[theme]/background.jpg</code></pre>
<p>Method example.</p>
<pre><code>public function element_background_cover($element, $data){
  //Handle element as needed and return it.
  return $element;
}</code></pre>

<a name="key_3"></a>

## Application dir

<p>Application dir contains this folders.</p>
<ul>
<li>config</li>
<li>plugin</li>
<li>sys</li>
<li>theme</li>
<li>public_html</li>
</ul>

<a name="key_4"></a>

## Folders

<p>Folder descriptions.</p>

<a name="key_4_0"></a>

### config

<p>Must have system settings file settings.yml.</p>

<a name="key_4_1"></a>

### plugin

<p>Plugins is stored in two folder levels. In first level there should only be folders. In second level there is plugin data files.</p>

<a name="key_4_2"></a>

### sys

<p>Buto system folder.</p>

<a name="key_4_3"></a>

### theme

<p>Themes is stored in two folder levels. In first level there should only be folders. In second level there is theme data files.</p>

<a name="key_4_4"></a>

### public_html

<p>Web root folder. One should point your web server Apache/IIS to this folder. This folder can have any name.</p>

<a name="key_5"></a>

## Settings

<p>File /config/settings.yml tells Buto which theme to render along with a few params.</p>

<a name="key_5_0"></a>

### System

<p>This file is read before theme settings file.</p>
<pre><code>/config/settings.yml.</code></pre>

<a name="key_5_0_0"></a>

#### Basic settings

<p>The file must contain this settings. This theme has location /theme/my/theme.</p>
<pre><code>theme: my/theme
timezone: Europe/Paris</code></pre>
<p>One could set tag to separate themes.</p>
<pre><code>tag: _a_tag_to_separate_themes_</code></pre>

<a name="key_5_0_1"></a>

#### HTTP_USER_AGENT

<p>One could change theme depending on user agent. Example to rewrite theme param from my/theme to my/next_theme if HTTP_USER_AGENT contains chrome.</p>
<pre><code>theme: my/theme
http_user_agent:
  '*chrome*':
    rewrite:
      set:
        -
          path_to_key: theme
          value: my/next_theme</code></pre>
<p>Now it should be like this.</p>
<pre><code>theme: my/next_theme</code></pre>

<a name="key_5_0_2"></a>

#### Domain

<p>One could change theme depending on domain name. Example to rewrite theme param from my/theme to my/next_theme if domain is localhost.</p>
<pre><code>theme: my/theme
domain:
  'localhost':
    rewrite:
      set:
        -
          path_to_key: theme
          value: my/next_theme</code></pre>
<p>Now it should be like this.</p>
<pre><code>theme: my/next_theme</code></pre>

<a name="key_5_1"></a>

### Theme

<p>This file contains plugin settings for widget, pages, events.</p>
<pre><code>/theme/xxx/yyy/config/settings.yml</code></pre>

<a name="key_5_1_0"></a>

#### I18N

<p>I18N settings.</p>
<pre><code>i18n:
  language: sv
  languages:
    - sv
    - de
    - en
  url:
    la-sv: sv
    la-de: de
    la-en: en
  lable:
    sv: Swedish
    de: Danish
    en: English</code></pre>
<p>Param url is optional. If url like /la-sv language sv will be set in Globals and Session.
One should use plugin i18n/url_rewrite when using this param to add language prefix for all href values.</p>
<pre><code>http://localhost/la-de</code></pre>
<p>When system find a match in url language the param is removed from query params.</p>

<a name="key_5_1_1"></a>

#### buto_data folder

<p>Rewrite settings.yml if this file also exist in (app dir) /../buto_data/theme/(theme)/settings.yml</p>
<pre><code>rewrite:
  set:
    -
      path_to_key: plugin_modules/account/settings
      value:
        allow:
          signin: true
          signin_method: username
          registration: true
          change_email: true
          change_username: true
          change_password: true
          two_factor_authentication: false
          remember: false
        two_factor_authentication:
          key_timeout: 600
        mysql:
          server: '127.0.0.1'
          database: '_my_db_'
          user_name: '_username_'
          password: '_password_'</code></pre>

<a name="key_6"></a>

## Element

<p>Elements are just as any HTML element but with extra settings attribute for Buto to handle.</p>
<pre><code>type: p
settings:
  disabled: true
attribute:
  style: 'color:red'
innerHTML: Hello World</code></pre>

<a name="key_6_0"></a>

### Attribute

<p>Attributes are set in same way as HTML.</p>
<pre><code>type: span
attribute:
  title: Hello Title
innerHTML: Hello World</code></pre>
<p>Attribute parameter as array render string if style or otherwise json data.</p>

<a name="key_6_0_0"></a>

#### Style

<p>This element has attribute style from array to string.</p>
<pre><code>type: div
attribute:
  style:
    color: red
    border: 'solid 1px blue'
    height: 100px
innerHTML: This element has attribute style from array to string.</code></pre>
<pre><code>&lt;div style="color:red;border:solid 1px blue;height:100px;"&gt;This element has attribute style from array to string.&lt;/div&gt;                </code></pre>

<a name="key_6_0_1"></a>

#### JSON

<p>This element has attribute data from array to json.</p>
<pre><code>type: span
attribute:
  data:
    name: Title
    id: 123
innerHTML: Hello World</code></pre>
<p>HTML</p>
<pre><code>&lt;span data="{&amp;quot;name&amp;quot;:&amp;quot;Title&amp;quot;,&amp;quot;id&amp;quot;:123}" onclick="console.log(JSON.parse(this.getAttribute('data')))"&gt;Hello World&lt;/span&gt;</code></pre>

<a name="key_6_1"></a>

### Settings

<p>Settings params are used by Buto and not to be rendered in browser.</p>

<a name="key_6_1_0"></a>

#### Role

<p>Restrict rendering element regarding to user role.</p>
<pre><code>type: span
settings:
  role:
    item:
      - client
innerHTML: This element is only rendered if user has role client. </code></pre>

<a name="key_6_1_1"></a>

#### Date

<p>Restrict rendering element regarding to date.</p>
<pre><code>type: span
settings:
  date:
    from: '2020-01-01 00:00:00'
    to: '2020-12-31 23:59:59'
innerHTML: Render this element year 2020.  </code></pre>
<pre><code>type: span
settings:
  date:
    allow: false
    from: '2020-01-01'
    to: '2020-12-31 23:59:59'
innerHTML: Do not render this element year 2020. </code></pre>

<a name="key_6_1_2"></a>

#### Enabled

<pre><code>type: span
settings:
  enabled: true
innerHTML: Hello World</code></pre>
<p>Enabled could also have yml-string.</p>
<pre><code>enabled: 'yml:/theme/[theme]/config/settings.yml:plugin_modules/account/settings/allow/change_email'</code></pre>

<a name="key_6_1_3"></a>

#### Disabled

<pre><code>type: span
settings:
  disabled: true
innerHTML: Hello World</code></pre>
<p>Disabled could also have yml-string.</p>
<pre><code>disabled: 'yml:/theme/[theme]/config/settings.yml:plugin_modules/account/settings/allow/change_email'</code></pre>

<a name="key_6_1_4"></a>

#### I18N

<p>Disable I18N.</p>
<pre><code>type: span
settings:
  i18n: false
innerHTML: Hello World</code></pre>
<p>Show element depending on language (one language).</p>
<pre><code>type: img
settings:
  i18n:
    language: sv
attribute:
  src: /theme/[theme]/flag/flag_se.png</code></pre>
<p>Show element depending on language (multiple languages).</p>
<pre><code>type: img
settings:
  i18n:
    language:
      - sv
      - en
attribute:
  src: /theme/[theme]/flag/flag_se.png</code></pre>

<a name="key_6_1_5"></a>

#### Server name

<p>Show element depending on server name.</p>
<pre><code>type: span
settings:
  server_name:
    allow: true
    item:
      - localhost
innerHTML: Hello World</code></pre>

<a name="key_6_1_6"></a>

#### Page

<p>Show element depending on page (wfServer::getRequestUri()).</p>
<pre><code>type: span
settings:
  page:
    allow: true
    item:
      - '/'
innerHTML: Show if start page.</code></pre>
<pre><code>type: span
settings:
  page:
    allow: false
    item:
      - '/'
innerHTML: Hide if start page.</code></pre>
<pre><code>type: span
settings:
  page:
    allow: true
    item:
      - '*contact*'
innerHTML: Show if value contact is in page adress.</code></pre>

<a name="key_6_1_7"></a>

#### Param

<p>Show element depending on param value.</p>
<pre><code>-
  type: div
  settings:
    param:
      allow: false
      name: sw
      value: John
  innerHTML: Paras sw must have value John
-
  type: div
  settings:
    param:
      allow: true
      name: sw
      value: John
  innerHTML: Paras sw can not have value John
-
  type: div
  settings:
    param:
      allow: true
      name: sw
  innerHTML: Paras sw must have a value
-
  type: div
  settings:
    param:
      allow: false
      name: sw
  innerHTML: Paras sw can not have a value</code></pre>

<a name="key_6_1_8"></a>

#### innerHTML

<p>Add innerHTML content (string only) from a file. Stop on first existing file.</p>
<pre><code>type: h1
settings:
  innerHTML:
    -
      file: /../buto_data/theme/[theme]/data.yml
      path_to_key: title
    -
      file: /theme/[theme]/buto_data/data.yml
      path_to_key: title</code></pre>
<p>Add innerHTML content from Globals.</p>
<pre><code>type: h1
settings:
  innerHTML: globals:sys/theme</code></pre>
<pre><code>type: h1
settings:
  innerHTML: 'globals:_SESSION/user_id'</code></pre>
<p>Add innerHTML content from file.</p>
<pre><code>type: div
settings:
  innerHTML: file:/theme/[theme]/content/file.html</code></pre>

<a name="key_6_1_9"></a>

#### file_exist

<p>Render element if a file exist only.</p>
<pre><code>type: widget
settings:
  file_exist: /theme/[theme]/buto_data/data.yml
data:
  plugin: 'theme/include'
  method: include
  data: yml:/theme/[theme]/buto_data/data.yml:plugin/theme/include/data</code></pre>

<a name="key_6_1_10"></a>

#### tag

<p>Render element if tag match.</p>
<pre><code>type: div
settings:
  tag: _my_tag_
innerHTML: 'Show this if tag match.'</code></pre>
<p>Render element if tags match.</p>
<pre><code>type: div
settings:
  tag:
    - _my_tag_
    - _my_tag2_
innerHTML: 'Show this if tag match.'</code></pre>

<a name="key_6_2"></a>

### innerHTML

<p>One can embed text from any file.</p>
<pre><code>innerHTML: file:/_path_/any_text.txt</code></pre>

<a name="key_6_3"></a>

### Globals

<p>Globals data can be picked up by a string.</p>
<pre><code>type: span
innerHTML: 'globals:_SESSION/username'</code></pre>
<pre><code>type: span
innerHTML: 'globals:sys/theme'</code></pre>

<a name="key_6_4"></a>

### Comment

<p>Add a comment to element.</p>
<pre><code>-
  _: 'This is a comment one could use in yml. This comment will not be removed if yml file is updated by system.'
  type: div
  innerHTML: 'Some text'</code></pre>

<a name="key_7"></a>

## Events

<p>Events are fired by system or plugins.</p>

<a name="key_7_0"></a>

### Settings

<p>Events are methods registrared in theme settings file.</p>
<pre><code>events:
  page_not_found:
    -
      plugin: 'wf/pagenotfound'
      method: handler
      data:
        location_url: '/d/pagenotfound'</code></pre>

<a name="key_7_1"></a>

### Method

<p>Method example.</p>
<pre><code>public function event_handler(){
  // Do stuff...
}</code></pre>

<a name="key_7_2"></a>

### System events

<p>Lisf of events in system.</p>
<pre><code>sys_start
load_config_settings_before
load_config_settings_after
load_theme_config_settings_before
load_theme_config_settings_after
shutdown
request_rewrite_before
request_rewrite_after
module_method_before
security_issue
page_not_found
module_method_after
document_render_before
  document_render_element
    document_render_innerhtml
document_render_after
document_render_title
sys_close</code></pre>

<a name="key_7_3"></a>

### Fire event

<p>A plugin can fire event like this.</p>
<pre><code>wfEvent::run('_any_name_', array('some_data' =&gt; null));</code></pre>

<a name="key_7_4"></a>

### Globals

<p>Last event is stored in globals to be detected later. Can be detected in __construct method for security reasons.</p>
<pre><code>$GLOBALS['sys']['event'] = array('plugin' =&gt; 'wf/pagenotfound', 'method' =&gt; 'handler');</code></pre>
<p>Can be checked like this.</p>
<pre><code>if(wfGlobals::get('event/plugin')=='wf/pagenotfound'){
  // 
}</code></pre>

<a name="key_8"></a>

## Lib

<p>Class files loaded from /sys/(name)/lib.</p>

<a name="key_8_0"></a>

### wfArray



<a name="key_8_1"></a>

### wfArraySearch



<a name="key_8_2"></a>

### wfConfig



<a name="key_8_3"></a>

### wfCrypt

<p>Methods.</p>

<a name="key_8_3_0"></a>

#### getOpensslRandomPseudoBytes

<p>Returns uniq id using openssl_random_pseudo_bytes.</p>
<pre><code>6e04-96da-5f50-572e-bc6d-b610</code></pre>

<a name="key_8_4"></a>

### wfDate



<a name="key_8_5"></a>

### wfDocument

<p>Handle elements.</p>

<a name="key_8_5_0"></a>

#### renderElement

<p>Render element from plugin.</p>
<pre><code>wfDocument::renderElement($element);</code></pre>

<a name="key_8_5_1"></a>

#### getContent

<p>Set capture to 1 or 2 to be able to get content via getContent method. Good for send email usage.
Set to 1 if capture html in content param and also render.
Set to 2 if capture html in content param only and NOT render.</p>
<pre><code>$element = new PluginWfYml(__DIR__.'/element/mail_registrate.yml');
wfDocument::$capture=2;
wfDocument::renderElement($element-&gt;get());
$content = wfDocument::getContent();</code></pre>

<a name="key_8_5_2"></a>

#### renderElementFromFolder

<p>Render a file direct from a folder where filename is the same as method name.</p>
<pre><code>wfDocument::renderElementFromFolder(__DIR__, __FUNCTION__);</code></pre>

<a name="key_8_5_3"></a>

#### setModeSvg

<p>Default mode is HTML but could also be SVG. SVG mode render text elements.</p>
<pre><code>wfDocument::setModeSvg();
wfDocument::renderElement($svg-&gt;get());
wfDocument::setModeHtml();</code></pre>

<a name="key_8_5_4"></a>

#### renderStartTag



<a name="key_8_5_4_0"></a>

##### Globals

<p>Set globals for an element and it´s child elements. This example change path settings for PluginI18nTranslate_v1.</p>
<pre><code>type: div
innerHTML:
  -
    type: div
    innerHTML: Hello World
  -
    type: div
    settings:
      globals:
        -
          path_to_key: 'settings/plugin/i18n/translate_v1/settings/path'
          value: '/plugin/invoice/invoice_v1/i18n'
    innerHTML: Hello World</code></pre>
<p>This example disable i18n if using plugin PluginI18nTranslate_v1.</p>
<pre><code>type: div
settings:
  globals:
    -
      path_to_key: 'settings/plugin/i18n/translate_v1/settings/disabled'
      value: true
innerHTML:
  -
    type: div
    innerHTML: Hello World</code></pre>

<a name="key_8_5_4_1"></a>

##### Confirm

<p>Add confirm handle for A element in method renderStartTag if attribute data-content-confirm is set.</p>
<pre><code>type: a
attribute: 
  data-content-confirm: Are you sure to run this href?
  href: '/d/test_button?works=yes'
innerHTML: Confirm href</code></pre>
<pre><code>type: a
attribute: 
  data-content-confirm: Are you sure to run this onclick?
  onclick: alert('You clicked OK in the prompt!')
innerHTML: Confirm onclick</code></pre>

<a name="key_8_6"></a>

### wfElement



<a name="key_8_7"></a>

### wfEvent

<p>This events is handled by system along with custom events.</p>
<pre><code>sys_start
load_config_settings_before
load_config_settings_after
load_theme_config_settings_before
load_theme_config_settings_after
shutdown
request_rewrite_before
request_rewrite_after
module_method_before
security_issue
page_not_found
module_method_after
document_render_before
  document_render_element
    document_render_innerhtml
document_render_after
sys_close</code></pre>
<p>This event should be registred in /theme/folder/folder/config/settings.yml file.
For example using plugin wf/errorhandling method shutdown when event shutdown is thrown.</p>
<pre><code>events:
  shutdown:
    -
      plugin: 'wf/errorhandling'
      method: 'shutdown'
      data: 'some data...'</code></pre>

<a name="key_8_8"></a>

### wfFilesystem



<a name="key_8_8_0"></a>

#### isDir

<p>Return true if a dir or false if a file.</p>
<pre><code>wfFilesystem::isDir('/full/path/to/dir_or_file');</code></pre>

<a name="key_8_8_1"></a>

#### delete_in_dir

<p>Delete all files and folders in dir.</p>
<pre><code>wfFilesystem::delete_in_dir('/full/path/to/dir');</code></pre>

<a name="key_8_9"></a>

### wfGlobals

<p>Handle Globals variable.</p>

<a name="key_8_10"></a>

### wfHelp



<a name="key_8_11"></a>

### wfI18n

<p>Handle I18N.</p>

<a name="key_8_11_0"></a>

#### Auto select

<p>Session params.
Trying to set language once per session from server param HTTP_ACCEPT_LANGUAGE.</p>
<pre><code>i18n:
  auto_select: (true on the set)
  language: (param to set)</code></pre>
<p>To avoid to set param i18n/language in theme settings.</p>
<pre><code>i18n:
  auto_select: false</code></pre>

<a name="key_8_11_1"></a>

#### getLanguagesMore

<p>Returns array.</p>
<pre><code>-
  name: sv
  label: Swedish
-
  name: en
  label: English</code></pre>

<a name="key_8_12"></a>

### wfPlugin



<a name="key_8_12_0"></a>

#### validateParams

<p>Validate params in array.</p>
<pre><code>wfPlugin::validateParams(__CLASS__, __FUNCTION__, (array with validation settings), (array to validate));</code></pre>
<p>Validation settings (example).</p>
<pre><code>mysql: 
  message: Param [key] is missing!
  type: exist
mail: 
  message: Param [key] is missing!
  type: exist</code></pre>
<p>Replace / (slash) with $.</p>
<pre><code>mail$subject: 
  message: Param [key] is missing!
  type: exist</code></pre>
<p>Output (php exit)(example).</p>
<pre><code>PluginMailQueue_create.__construct says: Param mail/subject is missing!</code></pre>

<a name="key_8_13"></a>

### wfRequest

<p>Handle request params.</p>

<a name="key_8_13_0"></a>

#### get

<p>Print variable name.</p>
<pre><code>print_r(wfRequest::get('name'));</code></pre>

<a name="key_8_13_1"></a>

#### getAll

<p>Print all.</p>
<pre><code>print_r(wfRequest::getAll());</code></pre>

<a name="key_8_13_2"></a>

#### set

<p>Set variable name.</p>
<pre><code>wfRequest::set('name', 'James');</code></pre>

<a name="key_8_13_3"></a>

#### rewrite

<p>Handle server variable REQUEST_URI (or HTTP_X_ORIGINAL_URL for windows).</p>

<a name="key_8_14"></a>

### wfServer



<a name="key_8_14_0"></a>

#### typeOfAccept

<p>Check server variable HTTP_ACCEPT for in string.
Returns.</p>
<ul>
<li>page (text/html).</li>
<li>ajax (text/javascript).</li>
<li>json (&#42;/&#42;).</li>
<li>unknown (no match). </li>
</ul>

<a name="key_8_14_1"></a>

#### isHost

<p>Check if host by name (typical localhost for developer purpose).
Returns boolean.</p>

<a name="key_8_15"></a>

### wfSettings



<a name="key_8_15_0"></a>

#### replaceTag

<ul>
<li>Replace [tag] with wfGlobals::get('tag').</li>
</ul>

<a name="key_8_15_1"></a>

#### replaceDir

<ul>
<li>Replace [app_dir]' with wfArray::get($GLOBALS, 'sys/app_dir').</li>
<li>Replace [web_dir]' with wfArray::get($GLOBALS, 'sys/web_dir').</li>
<li>Replace [class]' with wfArray::get($GLOBALS, 'sys/class').</li>
<li>Replace [theme]' with wfSettings::getTheme().</li>
<li>Replace [tag]' with wfGlobals::get('tag').</li>
<li>Replace [la]' with wfI18n::getLanguage().</li>
<li>Replace [host]' with wfServer::getHttpHost().</li>
</ul>

<a name="key_8_16"></a>

### wfUser



<a name="key_8_17"></a>

### wfPhpfunc

<p>Created to handle PHP version 8.2.</p>

<a name="key_8_17_0"></a>

#### str_replace

<p>Handle if param replace is null.</p>

<a name="key_8_17_1"></a>

#### strstr

<p>Handle if param haystack is null.</p>

<a name="key_8_17_2"></a>

#### substr

<p>Handle if param string is null.</p>

<a name="key_8_18"></a>

### wfPhpinfo

<p>Show info if user has role webmaster or webadmin.</p>
<pre><code>/?phpinfo=phpinfo.
/?phpinfo=session.
/?phpinfo=server.
/?phpinfo=cookie.
/?phpinfo=error_fatal.
/?phpinfo=error_deprecated.
/?phpinfo=error_notice.</code></pre>
<p>Show info if user has role webmaster.</p>
<pre><code>/?phpinfo=globals.</code></pre>

<a name="key_8_18_0"></a>

#### session

<pre><code>i18n: (i18n data)
  language: sv
  auto_select: true
sys:
  id: 211195832863c819d939f97400611891 (Last page id)
  page: (Page data, only ten latest)
plugin: (Plugin data)
  twitter:
    bootstrap413v:
      include: true
secure: true (User has sign in)
email: (User email)
username: (User username)
user_id: (User user_id)
role: (User roles)
  - webmaster
  - webadmin
details: (User details)
  log_count_except_this: 96
  last_login_before_today: '2023-01-17 18:38:54'
  days_login_before_today: 1
theme_data:
  version: (Theme version)</code></pre>

<a name="key_8_19"></a>

### wfWidget



<a name="key_8_19_0"></a>

#### handle_data

<p>Handle data passed to widget.</p>
<pre><code>$wData = wfWidget::handle_data($data);</code></pre>
<p>Passing default data.</p>
<pre><code>$defalt = array(
  'height' =&gt; '400px'
);
$wData = wfWidget::handle_data($data, $defalt);</code></pre>

<a name="key_9"></a>

## Special requests

<p>One could run some params to get special methods.
This works only if user has role webmaster or if on localhost.</p>

<a name="key_9_0"></a>

### Phpinfo

<pre><code>/?phpinfo=phpinfo</code></pre>

<a name="key_9_1"></a>

### Session

<pre><code>/?phpinfo=session</code></pre>
<p>Filter by path parameter.</p>
<pre><code>/?phpinfo=session&amp;path=plugin</code></pre>

<a name="key_9_2"></a>

### Server

<pre><code>/?phpinfo=server</code></pre>

<a name="key_9_3"></a>

### Globals

<pre><code>/?phpinfo=globals</code></pre>
<p>Filter by path parameter.</p>
<pre><code>/?phpinfo=globals&amp;path=settings</code></pre>

<a name="key_9_4"></a>

### Error fatal

<pre><code>/?phpinfo=error_fatal</code></pre>

<a name="key_9_5"></a>

### Error deprecated

<pre><code>/?phpinfo=error_deprecated</code></pre>

<a name="key_9_6"></a>

### Error notice

<pre><code>/?phpinfo=error_notice</code></pre>

<a name="key_9_7"></a>

### Load theme

<pre><code>/?loadtheme=_folder_/_folder_</code></pre>

<a name="key_9_8"></a>

### Sign out

<pre><code>/?signout=1</code></pre>

<a name="key_9_9"></a>

### Plugin page

<p>If a developer needs to run plugin code without using theme settings.</p>
<pre><code>/?webmaster_plugin=chart/amcharts_v3&amp;page=demo_serial</code></pre>

<a name="key_10"></a>

## Mandatory plugin

<p>There are two plugins who has to be included.</p>

<a name="key_10_0"></a>

### wf/array

<p>Download from Github.com.</p>
<pre><code>https://github.com/costlund/Buto-Plugin-WfArray.git</code></pre>

<a name="key_10_1"></a>

### wf/arraysearch

<p>Download from Github.com.</p>
<pre><code>https://github.com/costlund/Buto-Plugin-WfArraysearch.git</code></pre>

<a name="key_11"></a>

## Title

<p>Page title are set from layout and page files. </p>
<p>Layout.</p>
<pre><code>settings:
  title: My layout title</code></pre>
<p>Page.</p>
<pre><code>settings:
  title: My page title</code></pre>
<p>Title.</p>
<pre><code>My page title - My layout title</code></pre>
<p>Event example using plugin i18n/translate_v1 will translate between hyphen.</p>
<pre><code>events:
  document_render_title:
    -
      plugin: i18n/translate_v1
      method: translate_title</code></pre>

