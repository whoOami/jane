#!/bin/sh
BASEDIR=$(dirname $0)
cd $BASEDIR
php jane.php --cc
rm /tmp/template.odt
rm /tmp/charge_account.pdf
