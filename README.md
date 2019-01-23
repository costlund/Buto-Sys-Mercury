# Buto-Sys-Mercury

Mercury is a Buto system software.

## Application dir
Application dir contains this folders. 

*In this case web dir is public_html. But it can have any name*


- config
- plugin
- sys
- theme
- public_html


## Config - Folder
Must have file settings.yml. Global settings for Buto system.

### Theme
This file must contain this settings. This theme has location /theme/my/theme from application dir.
```
theme: my/theme
timezone: Europe/Paris
```




### HTTP_USER_AGENT - Rewrite.

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
