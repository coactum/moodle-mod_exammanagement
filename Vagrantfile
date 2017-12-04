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
  config.hostmanager.aliases = %w(panda33.uni-paderborn.de.vm panda34.uni-paderborn.de.vm)

config.vm.provider :virtualbox do |vb|
  vb.customize ["modifyvm", :id, "--memory", "1024"]
  vb.customize ["modifyvm", :id, "--cpus", "2"]
end

config.vm.synced_folder "www", "/var/www", create: true, owner: 'www-data', group: 'www-data'
config.vm.synced_folder "vhosts", "/var/vhosts", create: true, owner: 'www-data', group: 'www-data'
config.vm.synced_folder "data33", "/var/data33", create: true, owner: 'www-data', group: 'www-data'
config.vm.synced_folder "data34", "/var/data34", create: true, owner: 'www-data', group: 'www-data'

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

  # mysql
  ~/coactum-dotfiles/installer/apps/mysql57.sh

  # apt-get install mariadb-server -y
  # apt-get install software-properties-common dirmngr -y
  # apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xF1656F24C74CD1D8
  # # add-apt-repository 'deb [arch=amd64,i386,ppc64el] http://ftp.hosteurope.de/mirror/mariadb.org/repo/10.2/debian stretch main'

  # apt-get update
  # apt-get install mariadb-server -y

  ##############
  # Moodle 3.3 #
  ##############

  mkdir -p /var/www/moodle33/
  cd /var/www/moodle33/

  echo "Retrieving Moodle 3.3 ..."
  git clone --branch MOODLE_33_STABLE https://github.com/moodle/moodle.git .
  # LATEST_VERSION=$(git tag | awk '{print $1}' | grep -v '}$' | grep -v 'beta' | grep -v 'rc' | sed 's/^v//' | sort -t. -k 1,1n -k 2,2n -k 3,3n | tail -n1)
  # echo "Checking out Moodle version ${LATEST_VERSION}..."
  # git checkout "tags/v${LATEST_VERSION}" -b "v${LATEST_VERSION}"


  mysql -uroot -e "CREATE DATABASE moodle33"
  mysql -uroot -e "CREATE USER 'moodle33'@'localhost' IDENTIFIED BY 'moodle33'";
  mysql -uroot -e "GRANT ALL PRIVILEGES ON moodle33.* TO 'moodle33'@'localhost'";

  echo "Installing Moodle 3.3 ..."
  cd /var/www/moodle33
  sudo -u www-data /usr/bin/php admin/cli/install.php --lang='de' --wwwroot='http://panda33.uni-paderborn.de.vm' --dataroot='/var/data33' --dbtype='mysqli' --dbname='moodle33' --dbuser='moodle33' --dbpass='moodle33' --fullname='Moodle 3.3' --shortname='moodle33' --adminpass='Admin1!' --agree-license --non-interactive --allow-unstable
  mysql moodle33 -e "UPDATE \\`mdl_user\\` SET \\`email\\` = 'admin@test.abcde' WHERE \\`mdl_user\\`.\\`id\\` = 2"

  # /usr/bin/php admin/cli/install_database.php --lang='de' --wwwroot='http://panda.uni-paderborn.de.vm' --dataroot='/var/data' --dbtype='mysqli' --dbname='moodle' --dbuser='moodle' --dbpass='moodle' --fullname='Moodle' --shortname='moodle' --adminpass='Admin1!' --agree-license --non-interactive

  cd /var/www/moodle33/mod
  git clone --branch develop https://installuser:steamcoactum16@git.coactum.de/coactum/coactum-moodle-exammanagement.git exammanagement
  git remote remove origin
  git remote add origin ssh://gogs@git.coactum.de:2222/coactum/coactum-moodle-exammanagement.git

  # install phpMyAdmin
  cd /var/www/moodle33/
  composer create-project phpmyadmin/phpmyadmin --repository-url=https://www.phpmyadmin.net/packages.json --no-dev

  # Moodle Developer Settings
  cd /var/www/moodle33
  cat <<EOF >> config.php

@error_reporting(E_ALL | E_STRICT); // NOT FOR PRODUCTION SERVERS!
@ini_set(‘display_errors’, ‘1’); // NOT FOR PRODUCTION SERVERS!
\\$CFG->debug = (E_ALL | E_STRICT); // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
\\$CFG->debugdisplay = 1; // NOT FOR PRODUCTION SERVERS!

EOF

  # Creating Test Course
  cd /var/www/moodle33
  sudo -u www-data php admin/tool/generator/cli/maketestcourse.php --shortname=Testkurs1 --size=S

  ##############
  # Moodle 3.4 #
  ##############

  mkdir -p /var/www/moodle34/
  cd /var/www/moodle34/

  echo "Retrieving Moodle 3.4 ..."
  git clone --branch MOODLE_34_STABLE https://github.com/moodle/moodle.git .
  # LATEST_VERSION=$(git tag | awk '{print $1}' | grep -v '}$' | grep -v 'beta' | grep -v 'rc' | sed 's/^v//' | sort -t. -k 1,1n -k 2,2n -k 3,3n | tail -n1)
  # echo "Checking out Moodle version ${LATEST_VERSION}..."
  # git checkout "tags/v${LATEST_VERSION}" -b "v${LATEST_VERSION}"


  mysql -uroot -e "CREATE DATABASE moodle34"
  mysql -uroot -e "CREATE USER 'moodle34'@'localhost' IDENTIFIED BY 'moodle34'";
  mysql -uroot -e "GRANT ALL PRIVILEGES ON moodle34.* TO 'moodle34'@'localhost'";

  echo "Installing Moodle 3.4 ..."
  cd /var/www/moodle34
  sudo -u www-data /usr/bin/php admin/cli/install.php --lang='de' --wwwroot='http://panda34.uni-paderborn.de.vm' --dataroot='/var/data34' --dbtype='mysqli' --dbname='moodle34' --dbuser='moodle34' --dbpass='moodle34' --fullname='Moodle 3.4' --shortname='moodle34' --adminpass='Admin1!' --agree-license --non-interactive --allow-unstable
  mysql moodle34 -e "UPDATE \\`mdl_user\\` SET \\`email\\` = 'admin@test.abcde' WHERE \\`mdl_user\\`.\\`id\\` = 2"

  # /usr/bin/php admin/cli/install_database.php --lang='de' --wwwroot='http://panda.uni-paderborn.de.vm' --dataroot='/var/data' --dbtype='mysqli' --dbname='moodle' --dbuser='moodle' --dbpass='moodle' --fullname='Moodle' --shortname='moodle' --adminpass='Admin1!' --agree-license --non-interactive

  cd /var/www/moodle34/mod
  git clone --branch develop https://installuser:steamcoactum16@git.coactum.de/coactum/coactum-moodle-exammanagement.git exammanagement
  git remote remove origin
  git remote add origin ssh://gogs@git.coactum.de:2222/coactum/coactum-moodle-exammanagement.git

  # install phpMyAdmin
  cd /var/www/moodle34/
  composer create-project phpmyadmin/phpmyadmin --repository-url=https://www.phpmyadmin.net/packages.json --no-dev

  # Moodle Developer Settings
  cd /var/www/moodle34
  cat <<EOF >> config.php

@error_reporting(E_ALL | E_STRICT); // NOT FOR PRODUCTION SERVERS!
@ini_set(‘display_errors’, ‘1’); // NOT FOR PRODUCTION SERVERS!
\\$CFG->debug = (E_ALL | E_STRICT); // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
\\$CFG->debugdisplay = 1; // NOT FOR PRODUCTION SERVERS!

EOF

  # Creating Test Course
  cd /var/www/moodle34
  sudo -u www-data php admin/tool/generator/cli/maketestcourse.php --shortname=Testkurs1 --size=S

shell

end
