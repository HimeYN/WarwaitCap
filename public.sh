public_ip=$(curl -s ifconfig.me) && \
    echo "PUBLIC_IP=$public_ip" > /etc/environment && \
    sed -i "s/ServerName\s*<SERVER_NAME>/ServerName $public_ip/g" /etc/apache2/sites-available/App.conf && \
    sed -i "s/#ServerName\s*<SERVER_NAME>/ServerName $public_ip/g" /etc/apache2/apache2.conf && \
    echo "ServerName $public_ip" >> /etc/apache2/apache2.conf
