    # -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "debian/jessie64"

  config.vm.network "forwarded_port", guest: 80, host: 8080


  config.vm.provider "virtualbox" do |vb|
     # Display the VirtualBox GUI when booting the machine
     vb.gui = false

    # Customize the amount of memory on the VM:
    vb.memory = "1024"
  end


  config.vm.provision :shell, path: "vagrant/bootstrap.sh"

end
