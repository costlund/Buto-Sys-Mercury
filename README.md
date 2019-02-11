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
  - [Settings](#key_5_0) 
    - [Enabled](#key_5_0_0) 
    - [Disabled](#key_5_0_1) 
  - [Globals](#key_5_1) 


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

### Settings

<p>Settings params are used by Buto and not to be rendered in browser.</p>

<a name="key_5_0_0"></a>

#### Enabled

<pre><code>type: span
settings:
  enabled: true
innerHTML: Hello World</code></pre>

<a name="key_5_0_1"></a>

#### Disabled

<pre><code>type: span
settings:
  disabled: true
innerHTML: Hello World</code></pre>

<a name="key_5_1"></a>

### Globals

<p>Globals data can be picked up by a string.</p>
<pre><code>type: span
innerHTML: 'globals:_SESSION/username'</code></pre>

