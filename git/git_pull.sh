cd ../../../sys
find . -maxdepth 1 -mindepth 1 -type d -exec sh -c '(echo "" && echo "[sys]" && echo {} && cd {} && git pull && echo)' \;
cd ../sys/mercury/git

cd ../../../theme
find . -maxdepth 2 -mindepth 2 -type d -exec sh -c '(echo "" && echo "[theme]" && echo {} && cd {} && git pull && echo)' \;
cd ../sys/mercury/git

cd ../../../plugin
find . -maxdepth 2 -mindepth 2 -type d -exec sh -c '(echo "" && echo "[plugin]" && echo {} && cd {} && git pull && echo)' \;
cd ../sys/mercury/git

