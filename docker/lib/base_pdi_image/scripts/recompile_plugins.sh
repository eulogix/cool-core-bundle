#!/usr/bin/env bash

cd /opt/kettle-plugins
git pull
chmod +x buildall.sh
./buildall.sh
rm -rf plugins
mkdir plugins
mkdir plugins/binary-file-output
cp "binary-file-output/target/uber-binary-file-output-0.0.1-SNAPSHOT.jar" plugins/binary-file-output/binary-file-output.jar
mkdir plugins/pst-input \
cp "pst-input/target/uber-pst-input-0.0.1-SNAPSHOT.jar" plugins/pst-input/pst-input.jar

cd /opt/cool-kettle-integration
git pull
chmod +x buildall.sh
./buildall.sh
rm -rf plugins
mkdir plugins
mkdir plugins/file-get-properties
cp "file-get-properties/target/uber-file-get-properties-0.0.1-SNAPSHOT.jar" plugins/file-get-properties/file-get-properties.jar
mkdir plugins/file-set-properties
cp "file-set-properties/target/uber-file-set-properties-0.0.1-SNAPSHOT.jar" plugins/file-set-properties/file-set-properties.jar
mkdir plugins/file-search
cp "file-search/target/uber-file-search-0.0.1-SNAPSHOT.jar" plugins/file-search/file-search.jar
mkdir plugins/file-uploader
cp "file-uploader/target/uber-file-uploader-0.0.1-SNAPSHOT.jar" plugins/file-uploader/file-uploader.jar
mkdir plugins/file-delete
cp "file-delete/target/uber-file-delete-0.0.1-SNAPSHOT.jar" plugins/file-delete/file-delete.jar
mkdir plugins/notification-send
cp "notification-send/target/uber-notification-send-0.0.1-SNAPSHOT.jar" plugins/notification-send/notification-send.jar
mkdir plugins/filerepo-uploader
cp "filerepo-uploader/target/uber-filerepo-uploader-0.0.1-SNAPSHOT.jar" plugins/filerepo-uploader/filerepo-uploader.jar
mkdir plugins/template-render
cp "template-render/target/uber-template-render-0.0.1-SNAPSHOT.jar" plugins/template-render/template-render.jar

cd /opt
rm -rf plugins_compiled
mkdir plugins_compiled
cp -R kettle-plugins/plugins/* plugins_compiled/
cp -R cool-kettle-integration/plugins/* plugins_compiled/
