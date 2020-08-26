#!/bin/bash

# Package es2015 package.
cat ./dist/charteredcollege-custom-elements/runtime-es2015.js \
./dist/charteredcollege-custom-elements/polyfills-es2015.js \
./dist/charteredcollege-custom-elements/scripts.js \
./dist/charteredcollege-custom-elements/main-es2015.js > charteredcollege-ce.js

echo "Packaged es2015 project into charteredcollege-ce.js"

# Package es5 package.
cat ./dist/charteredcollege-custom-elements/runtime-es5.js \
./dist/charteredcollege-custom-elements/polyfills-es5.js \
./dist/charteredcollege-custom-elements/scripts.js \
./dist/charteredcollege-custom-elements/main-es5.js > charteredcollege-ce-es5.js

echo "Packaged es5 project into charteredcollege-ce-es5.js"
