<h1>Buto-Sys-Mercury<p><p>Mercury is a Buto system software. 
It runs on any server supporting PHP. 
It has no dependencies like any type of database. 
The main configuration of a Buto system is done with yml files.
Only 22 files with .php extension for the hole framework.
HTML code is done with yml in the exact same way but with the benefit to add extra Buto system params.</p><ul>  <li>    <a href="#key_0">Theme  <li>    <a href="#key_1">Plugin  <ul>    <li>      <a href="#key_1_0">Widgets    <li>      <a href="#key_1_1">Pages    <li>      <a href="#key_1_2">Events    <li>      <a href="#key_1_3">Methods  <li>    <a href="#key_2">Application dir  <li>    <a href="#key_3">Folders  <ul>    <li>      <a href="#key_3_0">config    <li>      <a href="#key_3_1">plugin    <li>      <a href="#key_3_2">sys    <li>      <a href="#key_3_3">theme    <li>      <a href="#key_3_4">public_html  <li>    <a href="#key_4">Settings  <ul>    <li>      <a href="#key_4_0">System    <ul>      <li>        <a href="#key_4_0_0">Basic settings      <li>        <a href="#key_4_0_1">HTTP_USER_AGENT    <li>      <a href="#key_4_1">Theme    <ul>      <li>        <a href="#key_4_1_0">I18N<a name="key_0" href="#!"><h2>Theme<p><p>Buto can have multiple themes. But in most cases there is only one involved when an application is in production.</p><a name="key_1" href="#!"><h2>Plugin<p><p>Plugins can have one or many of this purposes. A theme must make use of at least one plugin to work properly.</p>
<ul>
<li>Widgets</li>
<li>Pages</li>
<li>Events</li>
<li>Methods</li>
</ul>
<p>Some plugins are like a complete application. And some are designed just to include a javascript file with a widget.</p><a name="key_1_0" href="#!"><h3>Widgets<p><p>A widget should be some HTML element on a page.</p><a name="key_1_1" href="#!"><h3>Pages<p><p>A page where there could be layout pages involved.</p><a name="key_1_2" href="#!"><h3>Events<p><p>Buto system or plugins can fire events.</p><a name="key_1_3" href="#!"><h3>Methods<p><p>Methods used from other plugins.</p><a name="key_2" href="#!"><h2>Application dir<p><p>Application dir contains this folders.</p>
<ul>
<li>config</li>
<li>plugin</li>
<li>sys</li>
<li>theme</li>
<li>public_html</li>
</ul><a name="key_3" href="#!"><h2>Folders<p><p>Folder descriptions.</p><a name="key_3_0" href="#!"><h3>config<p><p>Must have system settings file settings.yml.</p><a name="key_3_1" href="#!"><h3>plugin<p><p>Plugins is stored in two folder levels. In first level there should only be folders. In second level there is plugin data files.</p><a name="key_3_2" href="#!"><h3>sys<p><p>Buto system folder.</p><a name="key_3_3" href="#!"><h3>theme<p><p>Themes is stored in two folder levels. In first level there should only be folders. In second level there is theme data files.</p><a name="key_3_4" href="#!"><h3>public_html<p><p>Web root folder. One should point your web server Apache/IIS to this folder. This folder can have any name.</p><a name="key_4" href="#!"><h2>Settings<p><p>File /config/settings.yml tells Buto which theme to render along with a few params.</p><a name="key_4_0" href="#!"><h3>System<p><p>This file is read before theme settings file.</p>
<pre><code>/config/settings.yml.</code></pre><a name="key_4_0_0" href="#!"><h4>Basic settings<p><p>The file must contain this settings. This theme has location /theme/my/theme.</p>
<pre><code>theme: my/theme
timezone: Europe/Paris</code></pre><a name="key_4_0_1" href="#!"><h4>HTTP_USER_AGENT<p><p>One could change theme depending on user agent. Example to rewrite theme param from my/theme to my/next_theme if HTTP_USER_AGENT contains chrome.</p>
<pre><code>theme: my/theme
http_user_agent:
  '*chrome*':
    rewrite:
      set:
        -
          path_to_key: theme
          value: my/next_theme</code></pre>
<p>Now it should be like this.</p>
<pre><code>theme: my/next_theme</code></pre><a name="key_4_1" href="#!"><h3>Theme<p><p>This file contains plugin settings for widget, pages, events.</p>
<pre><code>/theme/xxx/yyy/config/settings.yml</code></pre><a name="key_4_1_0" href="#!"><h4>I18N<p><p>I18N settings.</p>
<pre><code>i18n:
  language: sv
  languages:
    - sv
    - de
    - en</code></pre>