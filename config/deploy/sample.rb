set :stage, :sample
set :deploy_to, 'path_to_class'
ask :branch, proc{`git tag`.split("\n").last}

server 'server:port',
  user: 'class',
  roles: %w{app db},
  ssh_options: {
    user: 'class',
    forward_agent: true,
  }
