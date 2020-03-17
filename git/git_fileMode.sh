cd ../../../sys
find . -maxdepth 1 -mindepth 1 -type d -exec sh -c '(echo "[sys]" && echo {} && cd {} && git config core.fileMode false -s && echo)' \;
cd ../sys/mercury/git

cd ../../../theme
find . -maxdepth 2 -mindepth 2 -type d -exec sh -c '(echo "[theme]" && echo {} && cd {} && git config core.fileMode false -s && echo)' \;
cd ../sys/mercury/git

cd ../../../plugin
find . -maxdepth 2 -mindepth 2 -type d -exec sh -c '(echo "[plugin]" &&echo {} && cd {} && git config core.fileMode false -s && echo)' \;
cd ../sys/mercury/git

