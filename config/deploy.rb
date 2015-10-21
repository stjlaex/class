# add ~/.ssh/id_rsa.pub to server:/home/class/.ssh/authorised_keys
# to run deployment: cap sample deploy

set :application, 'classis'
set :repo_url, 'git@github.com:LearningData/classis.git'

set :deploy_via, :remote_cache
set :scm, :git
set :use_sudo, false
set :pty, true
set :current_dir, "classnew"

set :keep_releases, 3

additional_files_path = "/home/user/config_files"

#config_files = %w{
#  #{additional_files_path}school.php
#  #{additional_files_path}dbh_connect.php
#  #{additional_files_path}schoolarrays.php
#  #{additional_files_path}schoolarrays.php
#  #{additional_files_path}schoollogo.png
#  #{additional_files_path}classis.sql
#}

namespace :deploy do

  desc "Upload config files"
  task :upload_config_files do
    on roles(:app) do
      config_files.each do |file|
        if File.exists?(file)
          upload!("#{file}", "/tmp/config_files")
        else
          puts "File #{file} not found"
        end
      end
      execute "cp /tmp/config_files/* #{deploy_to}/"
    end
  end

  desc "Recreate symlink"
  task :resymlink do
    on roles(:app) do
#      execute "mkdir classnew"
#      time = Time.now.to_i
#      execute "mv #{deploy_to}/classnew #{deploy_to}/classnew.#{time}"
      execute "rm -rf #{deploy_to}/classnew"
#      execute "mv #{deploy_to}/current #{deploy_to}/classnew"
#      execute "ln -s releases/#{File.basename release_path} #{deploy_to}/classnew"
      execute "cp -pr #{deploy_to}/releases/#{File.basename release_path} #{deploy_to}/classnew"
      execute "rm -rf #{deploy_to}/current"

#      execute "cat #{deploy_to}/classnew/install/toplevel/school.php > #{deploy_to}/school.php"
#      open("#{deploy_to}/school.php", 'a') do |f|
#        f.puts "Updating school.php with new fields..."
#      end
    end
    on roles(:db) do
      #execute "php #{deploy_to}/classnew/lib/migrate_db.php"
#      open("#{deploy_to}/school.php", 'a') do |f|
#        f.puts "Migrating database, applying patches"
#      end
    end
  end
  desc "Revert symlink"
  task :revertlink do
    on roles(:app) do
#      execute "mv #{deploy_to}/current #{deploy_to}/classnew"
#      release = execute "readlink #{deploy_to}/current"
#      execute "cp -pr #{release} #{deploy_to}/classnew"
      execute "cp -pr #{deploy_to}/releases/#{File.basename release_path} #{deploy_to}/classnew"
      execute "rm -rf #{deploy_to}/current"
    end
  end

  after :finishing, 'deploy:resymlink'
  after :finishing_rollback, 'deploy:revertlink'
  #after :finishing, 'deploy:upload_config_files'
end
