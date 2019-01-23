# Buto-Sys-Mercury

Buto framework.


## config/settings.yml

### Rewrite depending on HTTP_USER_AGENT.

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
