set :stages, %w(production staging)
set :default_stage, "staging"

require 'capistrano/ext/multistage'

set :application, "company-exporter"
set :user, "rent"
set :group, "rent"

default_run_options[:pty] = true
ssh_options[:forward_agent] = true

set :scm, :git
set :repository,  "git@github.com:sjorz/company-exporter.git"
set :deploy_to, "/data/company-exporter"
set :deploy_via, :remote_cache

set :shared_children, ['log', 'pids']

#fix permissions on /data/property-exporter
after("deploy:setup") do
  sudo "aptitude install at -y"
  sudo "chown -R rent:rent /data/company-exporter"
end

#copy the latest init.d script
after('deploy:update') do
  sudo "cp -p #{current_path}/company-exporter /etc/init.d/"
end


namespace :deploy do

  desc 'Start the company exporter as a daemon'
  task :start, :roles => :proc, :only => {:primary => true} do
    sudo "/etc/init.d/company-exporter start_#{deploy_env}"
  end

  desc 'Stop the company exporter'
  task :stop, :roles => :proc, :only => {:primary => true} do
    sudo "/etc/init.d/company-exporter stop"
  end
  
  desc 'Restart the company exporte'
  task :restart, :roles => :proc, :only => {:primary => true} do
    stop
    start
  end

end

