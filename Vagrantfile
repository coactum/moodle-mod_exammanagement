VAGRANTFILE_API_VERSION = "2"
ENV['VAGRANT_DEFAULT_PROVIDER'] = 'virtualbox'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "coactum-debian-9.1"
  config.vm.hostname = 'coactum-moodle'
  config.vm.network :private_network, ip: '192.168.42.56'
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.ignore_private_ip = false
  config.hostmanager.include_offline = true
  config.hostmanager.aliases = %w(panda.uni-paderborn.de.vm)

config.vm.provider :virtualbox do |vb|
  vb.customize ["modifyvm", :id, "--memory", "1024"]
  vb.customize ["modifyvm", :id, "--cpus", "2"]
end

config.vm.synced_folder "www", "/var/www", create: true, owner: 'www-data', group: 'www-data'
config.vm.synced_folder "vhosts", "/var/vhosts", create: true, owner: 'www-data', group: 'www-data'
config.vm.synced_folder "data", "/var/data", create: true, owner: 'www-data', group: 'www-data'

# config.vm.synced_folder "www", "/var/www", id: "vagrant-www", :nfs => true
# config.vm.synced_folder "vhosts", "/var/vhosts", id: "vagrant-vhosts", :nfs => true
# config.vm.synced_folder "data", "/var/data", id: "vagrant-data", :nfs  => true
#
# config.vm.synced_folder "www", "/var/www", id: "vagrant-www", :nfs => { :mount_options => ["dmode=777","fmode=666"] }
# config.vm.synced_folder "vhosts", "/var/vhosts", id: "vagrant-vhosts", :nfs  => { :mount_options => ["dmode=777","fmode=666"] }
# config.vm.synced_folder "data", "/var/data", id: "vagrant-data", :nfs  => { :mount_options => ["dmode=777","fmode=666"] }

config.vm.provision "shell", inline: <<-shell
 apt-get update
 apt-get upgrade --show-upgraded -y
shell

config.vm.provision "shell", inline: <<-shell
  curl -sS https://git.coactum.de/coactum/coactum-dotfiles/raw/master/installer/install.sh --user installuser:steamcoactum16 | bash

  ~/coactum-dotfiles/installer/apps/apacheVhosts.sh

  # php70
  apt-get install -y --no-install-recommends \
  	php7.0 \
  	php7.0-cli \
  	php7.0-common \
  	php7.0-curl \
  	php-gd \
  	php7.0-intl \
  	php7.0-json \
  	php7.0-ldap \
  	php7.0-mcrypt \
  	php7.0-mysql \
  	php7.0-readline \
  	php7.0-tidy \
    php7.0-zip \
  	php-http \
  	php-pear \
    php-mbstring \
    php-xmlrpc \
    php-soap

  # update php.ini -> timezone
  sed -i "s/;date.timezone =/date.timezone = Europe\\/Berlin/" /etc/php/7.0/apache2/php.ini
  sed -i "s/;date.timezone =/date.timezone = Europe\\/Berlin/" /etc/php/7.0/cli/php.ini

  service apache2 restart

  # composer
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

  #Module mpm_event disabled.
  #Enabling module mpm_prefork.
  #apache2_switch_mpm Switch to prefork
  #apache2_invoke: Enable module php7.0

  # Moodle
  mkdir -p /var/www/moodle/
  cd /var/www/moodle/

  echo "Retrieving latest stable Moodle version..."
  git clone https://github.com/moodle/moodle.git .
  # LATEST_VERSION=$(git tag | awk '{print $1}' | grep -v '}$' | grep -v 'beta' | grep -v 'rc' | sed 's/^v//' | sort -t. -k 1,1n -k 2,2n -k 3,3n | tail -n1)
  # echo "Checking out Moodle version ${LATEST_VERSION}..."
  # git checkout "tags/v${LATEST_VERSION}" -b "v${LATEST_VERSION}"

  # mysql
  ~/coactum-dotfiles/installer/apps/mysql57.sh

  # apt-get install mariadb-server -y
  # apt-get install software-properties-common dirmngr -y
  # apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xF1656F24C74CD1D8
  # # add-apt-repository 'deb [arch=amd64,i386,ppc64el] http://ftp.hosteurope.de/mirror/mariadb.org/repo/10.2/debian stretch main'

  # apt-get update
  # apt-get install mariadb-server -y

  mysql -uroot -e "CREATE DATABASE moodle"
  mysql -uroot -e "CREATE USER 'moodle'@'localhost' IDENTIFIED BY 'moodle'";
  mysql -uroot -e "GRANT ALL PRIVILEGES ON moodle.* TO 'moodle'@'localhost'";


  echo "Installing Moodle..."
  cd /var/www/moodle
  sudo -u www-data /usr/bin/php admin/cli/install.php --lang='de' --wwwroot='http://panda.uni-paderborn.de.vm' --dataroot='/var/data' --dbtype='mysqli' --dbname='moodle' --dbuser='moodle' --dbpass='moodle' --fullname='Moodle' --shortname='moodle' --adminpass='Admin1!' --agree-license --non-interactive --allow-unstable

  # /usr/bin/php admin/cli/install_database.php --lang='de' --wwwroot='http://panda.uni-paderborn.de.vm' --dataroot='/var/data' --dbtype='mysqli' --dbname='moodle' --dbuser='moodle' --dbpass='moodle' --fullname='Moodle' --shortname='moodle' --adminpass='Admin1!' --agree-license --non-interactive

  cd /var/www/moodle/mod
  git clone --branch develop https://installuser:steamcoactum16@git.coactum.de/coactum/coactum-moodle-exammanagement.git exammanagement

  # install phpMyAdmin
  cd 
  composer create-project phpmyadmin/phpmyadmin --repository-url=https://www.phpmyadmin.net/packages.json --no-dev



shell

end
