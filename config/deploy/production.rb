set :gateway, '119.252.88.22' #vyatta firewall

role :proc, "192.168.1.170", :primary => true 

set :deploy_env, 'production'
set :rails_env, 'production'