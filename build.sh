#!/bin/bash
# Ask for version
echo "-- MeTools build script --"
echo "Please, enter the version number: "
read version
version="metools-$version"

# Create build/MeTools
rm -r -f build/MeTools
mkdir -p build/MeTools

# Copy in build/MeTools
cp -R Config/ Console/ Controller/ Lib/ Locale/ Model/ Utility/ \
Vendor/ View/ webroot/ COPYNG README.md build/MeTools

# Enter build/MeTools
cd build/MeTools

# Delete uncompressed files
rm -f webroot/css/datepicker.css webroot/css/default.css \
webroot/css/forms.css webroot/css/syntaxhighlighter.css \
webroot/js/bootstrap-datepicker.it.js webroot/js/bootstrap-datepicker.js \
webroot/js/default.js webroot/js/slugify.js

# Go back to build/
cd ../

# Create the tar archive
tar -czf $version.tar.gz MeTools/*
echo "The file $version.tar.gz was created"