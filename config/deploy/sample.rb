# path_to_local_classis/config/deploy/demo.rb
# add ~/.ssh/id_rsa.pub to server:/home/class/.ssh/authorised_keys
# to run deployment: cap sample deploy

set :stage, :sample
set :deploy_to, 'path_to_classnew_directory'
set :class_db, 'class_db_name'
set :dumps_dir, 'temporary_remote_path_for_dumps'
set :data_dir, 'eportfolio_path'

ask :branch, proc{`git tag`.split("\n").last}

server 'server:port',
  user: 'class',
  roles: %w{app db},
  ssh_options: {
    user: 'class',
    forward_agent: true,
  }
