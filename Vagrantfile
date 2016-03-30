    # -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

  config.vm.synced_folder ".", "/vagrant",  :owner=> 'www-data', :group=>'users', :mount_options => ['dmode=777', 'fmode=777']

  config.vm.define "normal" do |normal|

    normal.vm.network "forwarded_port", guest: 80, host: 8080

    normal.vm.box = "boxcutter/debian82"

    normal.vm.provider "virtualbox" do |vb|
      vb.gui = false
      vb.memory = "512"
    end

    normal.vm.provision :shell, path: "vagrant/normal/bootstrap.sh"

  end

  config.vm.define "frontendtests" do |frontendtests|

    frontendtests.vm.box = "boxcutter/ubuntu1404-desktop"

    frontendtests.vm.provider "virtualbox" do |vb|
      vb.gui = true
      vb.memory = "1024"
    end

    frontendtests.vm.provision :shell, path: "vagrant/frontendtests/bootstrap.sh"

  end

end
