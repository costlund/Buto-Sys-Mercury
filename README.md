# Buto-Sys-Mercury
- [Application dir](#Application_dir)
- [Config folder](#Config_folder)
  - [Theme](#Config_folder_theme)
  - [Rewrite HTTP_USER_AGENT](#Config_folder_rewrite_HTTP_USER_AGENT)
- [Plugin folder](#Plugin_folder)

Mercury is a Buto system software. 
It runs on any server supporting PHP. 
It has no dependencies like any type of database. 
The main configuration of a Buto system is done with yml files.
Only 22 files with .php extension for the hole framework.
<a name="Application_dir"></a>
## Application dir
Application dir contains this folders. In this case web dir is public_html. But it can have any name. One should point your web server Apache/IIS to public_html.
- config
- plugin
- sys
- theme
- public_html

<a name="Config_folder"></a>
## Config folder
Must have file settings.yml. Global settings for Buto system.
<a name="Config_folder_theme"></a>
### Theme
This file must contain this settings. This theme has location /theme/my/theme from application dir.
```
theme: my/theme
timezone: Europe/Paris
```
<a name="Config_folder_rewrite_HTTP_USER_AGENT"></a>
### Rewrite HTTP_USER_AGENT
One could change theme depending on user agent. Example to rewrite theme param from example/one to example/two HTTP_USER_AGENT contains chrome.
```
theme: example/one
```
```
http_user_agent:
  '*chrome*':
    rewrite:
      set:
        -
          path_to_key: theme
          value: example/two
```
<a name="Plugin_folder"></a>
## Plugin folder