#!/bin/sh
if [ -f /usr/bin/composer ] || [ -f /usr/local/bin/composer ]; then
	echo "Actualizando dependencias."
	composer install
else
	if [ -f composer.phar ]; then
		echo "Actualizando dependencias."
		./composer.phar install
	else
		if [ -f /usr/bin/curl ]; then
			echo "Composer no encontrado, descargando composer"
			curl -sS https://getcomposer.org/installer | php
		else 
			echo "Â¿Al parecer CURL no estÃ¡ instalado, desea instalarlo? [S/N]";
			read response
			response=$(echo $response | tr 'a-z' 'A-Z')
			if [ $response == "S" ] || [ $response == "SI" ]; then 
				echo "Descargando CURL"
				sudo apt-get install curl	
				echo "Composer no encontrado, descargando composer"
				curl -sS https://getcomposer.org/installer | php
			else
				echo "Se instalarÃ¡ composer a travÃ©s de php..."
				php -r "readfile('https://getcomposer.org/installer');" | php
			fi
		fi
		echo "Actualizando dependencias."
		./composer.phar install
		echo "Â¿Desea instalar Composer permanentemente en su sistema? [S/N]";
		read response
		response=$(echo $response | tr 'a-z' 'A-Z')
		if [ $response == "S" ] || [ $response == "SI" ]; then 
			echo "Instalando composer..."
			sudo mv composer.phar /usr/local/bin/composer
		else
			rm composer.phar
		fi
	fi
fi
cp .env.dist .env
$EDITOR .env
git submodule update --init
sleep 10
cd sender
composer install
cp .env.dist .env
$EDITOR .env
