#proc01
server "192.168.0.157", :proc, :primary => true

#proc02
server "192.168.0.154", :proc

set :deploy_env, 'staging'
set :rails_env, 'staging'