#!/bin/bash

echo "* NANDOTESS LOCALHOST DEPLOY *";
cd ~/Sites-LightSpeed/@git/lsx-login;
gulp compile-sass;
gulp compile-js;
gulp wordpress-lang;

echo "* LSX.MAMP:8888 *";
rm -Rf ~/Sites-LightSpeed/@mamp/lsx.mamp/wp-content/plugins/lsx-login;
rsync -a \
	--exclude='.git' \
	--exclude='.idea' \
	--exclude='.sass-cache' \
	--exclude='node_modules' \
	--exclude='.DS_Store' \
	--exclude='.gitignore' \
	--exclude='gulpfile.js' \
	--exclude='package.json' \
	~/Sites-LightSpeed/@git/lsx-login ~/Sites-LightSpeed/@mamp/lsx.mamp/wp-content/plugins;

echo "* TSA-V2.MAMP:8888 *";
rm -Rf ~/Sites-LightSpeed/@mamp/tsa-v2.mamp/wp-content/plugins/lsx-login;
rsync -a \
	--exclude='.git' \
	--exclude='.idea' \
	--exclude='.sass-cache' \
	--exclude='node_modules' \
	--exclude='.DS_Store' \
	--exclude='.gitignore' \
	--exclude='gulpfile.js' \
	--exclude='package.json' \
	~/Sites-LightSpeed/@git/lsx-login ~/Sites-LightSpeed/@mamp/tsa-v2.mamp/wp-content/plugins;
