# Buto-Plugin-BootstrapCarousel_v1

## wfDocument
This file handle html output to browser. It takes html as yml.
```
type: p 
innerHTML: This is an html element in yml.
```

### Render element
This method will echo out html.
```
wfDocument::renderElement($element);
```

### Get content from render
It is possible to capture content from the renderElement method.
Set the capture param to 1 if render and get content or 2 if only get content is involved.
```
wfDocument::$capture = 1;
wfDocument::renderElement($element);
wfFilesystem::saveFile(wfGlobals::getAppDir().$data->get('data/save'), wfDocument::getContent());
```
