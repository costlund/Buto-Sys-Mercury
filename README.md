# Buto-Sys-Mercury

<p>Mercury is a Buto system software. 
It runs on any server supporting PHP. 
It has no dependencies like any type of database. 
The main configuration of a Buto system is done with yml files.
Only 22 files with .php extension for the hole framework.
HTML code is done with yml in the exact same way but with the benefit to add extra Buto system params.
One could work with Buto without any knowledge of PHP when building a theme. Only in plugin development PHP skills are needed.</p>

- [Theme](#key_0) 
  - [Hello World](#key_0_0) 
    - [Config](#key_0_0_0) 
    - [Page](#key_0_0_1) 
- [Plugin](#key_1) 
  - [Widgets](#key_1_0) 
  - [Pages](#key_1_1) 
  - [Events](#key_1_2) 
  - [Methods](#key_1_3) 
- [Application dir](#key_2) 
- [Folders](#key_3) 
  - [config](#key_3_0) 
  - [plugin](#key_3_1) 
  - [sys](#key_3_2) 
  - [theme](#key_3_3) 
  - [public_html](#key_3_4) 
- [Settings](#key_4) 
  - [System](#key_4_0) 
    - [Basic settings](#key_4_0_0) 
    - [HTTP_USER_AGENT](#key_4_0_1) 
  - [Theme](#key_4_1) 
    - [I18N](#key_4_1_0) 
- [Element](#key_5) 
  - [Attribute](#key_5_0) 
    - [JSON](#key_5_0_0) 
  - [Settings](#key_5_1) 
    - [Enabled](#key_5_1_0) 
    - [Disabled](#key_5_1_1) 
    - [I18N](#key_5_1_2) 
    - [Server name](#key_5_1_3) 
  - [Globals](#key_5_2) 
  - [Render](#key_5_3) 
- [Events](#key_6) 
  - [Settings](#key_6_0) 
  - [Method](#key_6_1) 
  - [System events](#key_6_2) 
  - [Fire event](#key_6_3) 
  - [Globals](#key_6_4) 
- [Lib](#key_7) 
  - [wfArray](#key_7_0) 
  - [wfArraySearch](#key_7_1) 
  - [wfConfig](#key_7_2) 
  - [wfCrypt](#key_7_3) 
  - [wfDate](#key_7_4) 
  - [wfDocument](#key_7_5) 
    - [Globals](#key_7_5_0) 
    - [Mode](#key_7_5_1) 
  - [wfElement](#key_7_6) 
  - [wfEvent](#key_7_7) 
  - [wfFilesystem](#key_7_8) 
  - [wfGlobals](#key_7_9) 
  - [wfHelp](#key_7_10) 
  - [wfI18n](#key_7_11) 
  - [wfPlugin](#key_7_12) 
  - [wfRequest](#key_7_13) 
  - [wfServer](#key_7_14) 
  - [wfSettings](#key_7_15) 
  - [wfUser](#key_7_16) 
- [Special requests](#key_8) 
  - [Phpinfo](#key_8_0) 
  - [Session](#key_8_1) 
  - [Server](#key_8_2) 
  - [Globals](#key_8_3) 
  - [Load theme](#key_8_4) 
  - [Sign out](#key_8_5) 
  - [Webmaster page](#key_8_6) 


<a name="key_0"></a>

## Theme

<p>Buto can have multiple themes. But in most cases there is only one involved when an application is in production.</p>

<a name="key_0_0"></a>

### Hello World

<p>An Hello World example where theme is in location /theme/hello/world. This theme only make use of one plugin wf/doc to render a page.</p>

<a name="key_0_0_0"></a>

#### Config

<p>In /config/settings.yml</p>
<pre><code>plugin_modules:
  doc:
    plugin: 'wf/doc'
default_class: doc
default_method: home</code></pre>

<a name="key_0_0_1"></a>

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

<a name="key_1"></a>

## Plugin

<p>Plugins can have one or many of this purposes. A theme must make use of at least one plugin to work properly.</p>
<ul>
<li>Widgets</li>
<li>Pages</li>
<li>Events</li>
<li>Methods</li>
</ul>
<p>Some plugins are like a complete application. And some are designed just to include a javascript file with a widget.</p>

<a name="key_1_0"></a>

### Widgets

<p>A widget should be some HTML element on a page.</p>

<a name="key_1_1"></a>

### Pages

<p>A page where there could be layout pages involved.</p>

<a name="key_1_2"></a>

### Events

<p>Buto system or plugins can fire events.</p>

<a name="key_1_3"></a>

### Methods

<p>Methods used from other plugins.</p>

<a name="key_2"></a>

## Application dir

<p>Application dir contains this folders.</p>
<ul>
<li>config</li>
<li>plugin</li>
<li>sys</li>
<li>theme</li>
<li>public_html</li>
</ul>

<a name="key_3"></a>

## Folders

<p>Folder descriptions.</p>

<a name="key_3_0"></a>

### config

<p>Must have system settings file settings.yml.</p>

<a name="key_3_1"></a>

### plugin

<p>Plugins is stored in two folder levels. In first level there should only be folders. In second level there is plugin data files.</p>

<a name="key_3_2"></a>

### sys

<p>Buto system folder.</p>

<a name="key_3_3"></a>

### theme

<p>Themes is stored in two folder levels. In first level there should only be folders. In second level there is theme data files.</p>

<a name="key_3_4"></a>

### public_html

<p>Web root folder. One should point your web server Apache/IIS to this folder. This folder can have any name.</p>

<a name="key_4"></a>

## Settings

<p>File /config/settings.yml tells Buto which theme to render along with a few params.</p>

<a name="key_4_0"></a>

### System

<p>This file is read before theme settings file.</p>
<pre><code>/config/settings.yml.</code></pre>

<a name="key_4_0_0"></a>

#### Basic settings

<p>The file must contain this settings. This theme has location /theme/my/theme.</p>
<pre><code>theme: my/theme
timezone: Europe/Paris</code></pre>

<a name="key_4_0_1"></a>

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

<a name="key_4_1"></a>

### Theme

<p>This file contains plugin settings for widget, pages, events.</p>
<pre><code>/theme/xxx/yyy/config/settings.yml</code></pre>

<a name="key_4_1_0"></a>

#### I18N

<p>I18N settings.</p>
<pre><code>i18n:
  language: sv
  languages:
    - sv
    - de
    - en</code></pre>

<a name="key_5"></a>

## Element

<p>Elements are just as any HTML element but with extra settings attribute for Buto to handle.</p>
<pre><code>type: p
settings:
  disabled: true
attribute:
  style: 'color:red'
innerHTML: Hello World</code></pre>

<a name="key_5_0"></a>

### Attribute

<p>Attributes are set in same way as HTML.</p>
<pre><code>type: span
attribute:
  title: Hello Title
innerHTML: Hello World</code></pre>

<a name="key_5_0_0"></a>

#### JSON

<p>If attribute is an array it will be a JSON string.</p>
<pre><code>type: span
attribute:
  data:
    name: Title
    id: 123
innerHTML: Hello World</code></pre>
<p>HTML</p>
<pre><code>&lt;span data="{&amp;quot;name&amp;quot;:&amp;quot;Title&amp;quot;,&amp;quot;id&amp;quot;:123}" onclick="console.log(JSON.parse(this.getAttribute('data')))"&gt;Hello World&lt;/span&gt;</code></pre>

<a name="key_5_1"></a>

### Settings

<p>Settings params are used by Buto and not to be rendered in browser.</p>

<a name="key_5_1_0"></a>

#### Enabled

<pre><code>type: span
settings:
  enabled: true
innerHTML: Hello World</code></pre>
<p>Enabled could also have yml-string.</p>
<pre><code>enabled: 'yml:/theme/[theme]/config/settings.yml:plugin_modules/account/settings/allow/change_email'</code></pre>

<a name="key_5_1_1"></a>

#### Disabled

<pre><code>type: span
settings:
  disabled: true
innerHTML: Hello World</code></pre>
<p>Disabled could also have yml-string.</p>
<pre><code>disabled: 'yml:/theme/[theme]/config/settings.yml:plugin_modules/account/settings/allow/change_email'</code></pre>

<a name="key_5_1_2"></a>

#### I18N

<p>Disable I18N.</p>
<pre><code>type: span
settings:
  i18n: false
innerHTML: Hello World</code></pre>

<a name="key_5_1_3"></a>

#### Server name

<p>Show element depending on server name.</p>
<pre><code>type: span
settings:
  server_name:
    allow: true
    item:
      - localhost
innerHTML: Hello World</code></pre>

<a name="key_5_2"></a>

### Globals

<p>Globals data can be picked up by a string.</p>
<pre><code>type: span
innerHTML: 'globals:_SESSION/username'</code></pre>

<a name="key_5_3"></a>

### Render

<p>Render element from plugin.</p>
<pre><code>wfDocument::renderElement($element);</code></pre>
<p>Render element to an variable. Set capture=1 of also render. Could be handy when send html as mail body.</p>
<pre><code>wfDocument::$capture=2;
wfDocument::renderElement($element);
$content = wfDocument::getContent();</code></pre>

<a name="key_6"></a>

## Events

<p>Events are fired by system or plugins.</p>

<a name="key_6_0"></a>

### Settings

<p>Events are methods registrared in theme settings file.</p>
<pre><code>events:
  page_not_found:
    -
      plugin: 'wf/pagenotfound'
      method: handler
      data:
        location_url: '/d/pagenotfound'</code></pre>

<a name="key_6_1"></a>

### Method

<p>Method example.</p>
<pre><code>public function event_handler(){
  // Do stuff...
}</code></pre>

<a name="key_6_2"></a>

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
sys_close</code></pre>

<a name="key_6_3"></a>

### Fire event

<p>A plugin can fire event like this.</p>
<pre><code>wfEvent::run('_any_name_', array('some_data' =&gt; null));</code></pre>

<a name="key_6_4"></a>

### Globals

<p>Last event is stored in globals to be detected later. Can be detected in __construct method for security reasons.</p>
<pre><code>$GLOBALS['sys']['event'] = array('plugin' =&gt; 'wf/pagenotfound', 'method' =&gt; 'handler');</code></pre>
<p>Can be checked like this.</p>
<pre><code>if(wfGlobals::get('event/plugin')=='wf/pagenotfound'){
  // 
}</code></pre>

<a name="key_7"></a>

## Lib

<p>Methods.</p>

<a name="key_7_0"></a>

### wfArray



<a name="key_7_1"></a>

### wfArraySearch



<a name="key_7_2"></a>

### wfConfig



<a name="key_7_3"></a>

### wfCrypt



<a name="key_7_4"></a>

### wfDate



<a name="key_7_5"></a>

### wfDocument

<p>Handle elements.</p>

<a name="key_7_5_0"></a>

#### Globals

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

<a name="key_7_5_1"></a>

#### Mode

<p>Default mode is HTML but could also be SVG. SVG mode render text elements.</p>
<pre><code>wfDocument::setModeSvg();
wfDocument::renderElement($svg-&gt;get());
wfDocument::setModeHtml();</code></pre>

<a name="key_7_6"></a>

### wfElement



<a name="key_7_7"></a>

### wfEvent



<a name="key_7_8"></a>

### wfFilesystem



<a name="key_7_9"></a>

### wfGlobals

<p>Handle Globals variable.</p>

<a name="key_7_10"></a>

### wfHelp



<a name="key_7_11"></a>

### wfI18n



<a name="key_7_12"></a>

### wfPlugin



<a name="key_7_13"></a>

### wfRequest



<a name="key_7_14"></a>

### wfServer



<a name="key_7_15"></a>

### wfSettings



<a name="key_7_16"></a>

### wfUser



<a name="key_8"></a>

## Special requests

<p>One could run some params to get special methods.</p>

<a name="key_8_0"></a>

### Phpinfo

<p>Webmaster can view phpinfo via /?phpinfo=phpinfo.</p>

<a name="key_8_1"></a>

### Session

<p>Webmaster can view session via /?phpinfo=session.</p>

<a name="key_8_2"></a>

### Server

<p>Webmaster can view server via /?phpinfo=server.</p>

<a name="key_8_3"></a>

### Globals

<p>Webmaster can view globals via /?phpinfo=globals.</p>

<a name="key_8_4"></a>

### Load theme

<p>Webmaster can change theme via /?loadtheme=<em>folder</em>/<em>folder</em>.</p>

<a name="key_8_5"></a>

### Sign out

<p>One could sign out via /?signout=1.</p>

<a name="key_8_6"></a>

### Webmaster page

<p>Webmaster can go to /?webmaster_plugin=chart/amcharts_v3&amp;page=demo to view a page and there is no need of setup in theme settings.yml.</p>

