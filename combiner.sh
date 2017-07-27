#!/bin/sh
unzip -d /tmp/cc /tmp/template.odt
cd /tmp/cc
year=$(date +%Y)
month=$1
currentDate=$2
dateOne=$3
dateTwo=$4
receivableOnText=$5
receivable=$6;
ss=$7
monthText=$8
sed -i "s/currentDate/$currentDate/g" "content.xml"
sed -i "s/dateOne/$dateOne/g" "content.xml"
sed -i "s/dateTwo/$dateTwo/g" "content.xml"
sed -i "s/receivableOnText/$receivableOnText/g" "content.xml"
sed -i "s/receivable/$receivable/g" "content.xml"
sed -i "s/codeSs/$ss/g" "content.xml"
sed -i "s/month/$monthText/g" "content.xml"
sed -i "s/year/$year/g" "content.xml"
zip charge_account.odt * -r
soffice --headless --convert-to pdf charge_account.odt
mv charge_account.pdf ../.
cd ..
rm -rf /tmp/cc
