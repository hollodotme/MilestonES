VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # box-config
  config.vm.box = "devops000"
  config.vm.box_url = "http://box.3wolt.de/devops000/"
  config.vm.box_check_update = true
  config.vm.box_version = "~> 1.0.0"

  #config.vm.provider "virtualbox" do |v|
  #    v.gui = true
  #end

  # network-config
  #config.vm.network "public_network", type: "dhcp"
  config.vm.network "private_network", ip: "192.168.0.50"
  config.vm.boot_timeout = 600

  # SSH-config
  config.ssh.username = "root"
  config.ssh.password = '\g}xr+e#p@g1'
  config.ssh.insert_key = true

  # hostname
  config.vm.hostname = "MilestonES"
  config.vm.post_up_message = "--\nWelcome to MilestonES!\n\nThis VM delivers:\n\Application under: http://www.mileston-es.de\n\tDocumentation under: http://doc.mileston-es.de\n\tTests under: http://test.mileston-es.de\n\n--\n"

  # provisioners
  # ------------

  # nginx configs, copy and link
  config.vm.provision "file", source: "env/nginx/doc.conf", destination: "/etc/nginx/sites-available/doc"
  config.vm.provision "file", source: "env/nginx/test.conf", destination: "/etc/nginx/sites-available/test"
  config.vm.provision "file", source: "env/nginx/dist.conf", destination: "/etc/nginx/sites-available/dist"
  config.vm.provision "file", source: "env/vagrant/id_rsa", destination: "/root/.ssh/id_rsa"
  config.vm.provision "file", source: "env/vagrant/ssh_config", destination: "/root/.ssh/config"

  # shell commands
  config.vm.provision "shell", path: "env/vagrant/bootstrap.sh", run: "always"

end
