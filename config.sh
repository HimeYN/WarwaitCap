#!/bin/bash

echo "###########################"
echo "  Initializing databases "
echo "###########################"

python3 withoutGr.py -MC Matrice_des_competences.xlsx
python3 withoutGr.py -W name.xlsx

- name: Uncomment extension mysql, mv conf to, move app dirctory to
     command: sed -i "s/;extension=pdo_mysql/extension=pdo_mysql/g" /etc/php/8.1/cli/php.ini

echo "###########################"
echo " Configuring Apache Server "
echo "###########################"

cd /home/ubuntu/WarwaitCap/
cp -r Projet/Application /var/www/
sudo chmod -R 0755 /var/www/Application

mv App.conf /etc/apache2/sites-available/
public_ip=$(curl -s ifconfig.me) && \
    echo "PUBLIC_IP=$public_ip" > /etc/environment && \
    sed -i "s/ServerName\s*<SERVER_NAME>/ServerName $public_ip/g" /etc/apache2/sites-available/App.conf && \
    sed -i "s/#ServerName\s*<SERVER_NAME>/ServerName $public_ip/g" /etc/apache2/apache2.conf && \
    echo "ServerName $public_ip" >> /etc/apache2/apache2.conf

sed -i "s/;extension=pdo_mysql/extension=pdo_mysql/g" /etc/php/8.1/cli/php.ini

sudo a2enmod php8.1
sudo a2ensite App.conf
sudo a2dissite 000-default.conf

echo "###########################"
echo " Reloading Apache Server "
echo "###########################"

sudo systemctl reload apache2

