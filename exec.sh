#!/bin/sh
BASEDIR=$(dirname $0)
cd $BASEDIR
SUBJECT=$(php jane.php --cc=12)
cd sender
echo $SUBJECT
echo $BOSS_EMAIL
php sender.php --to=$BOSS_EMAIL --subject=$SUBJECT --body='Total adelantos: $0' --attach='/tmp/charge_account.pdf'
rm /tmp/template.odt
rm /tmp/charge_account.pdf
