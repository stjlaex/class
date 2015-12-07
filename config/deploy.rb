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

additional_files_path = "../config_files/"


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
      execute "rm -rf #{deploy_to}/classnew"
      execute "cp -pr #{deploy_to}/releases/#{File.basename release_path} #{deploy_to}/classnew"
      execute "rm -rf #{deploy_to}/current"
	end
  end

  desc "Migrate db"
  task :migrate do
	on roles(:app) do
      db = "#{fetch(:class_db)}"
      dumps = "#{fetch(:dumps_dir)}"
      today = Date.today.strftime("%d-%m-%Y-%H%M%S")
      file = "#{db}-#{today}-pre-migration.sql"
      execute "mysqldump -p$DB_PASS -u class #{db} > #{dumps}/#{file}"
      execute "cd #{dumps} && tar zcvf #{file}.tar.gz #{file}"
      execute "rm #{file}"

      execute "cd #{deploy_to}/classnew && php scripts/migrate_db.php --path=#{deploy_to}"
	end
  end

  desc "Update config file"
  task :update_config do
	on roles(:app) do
#      execute "cat #{deploy_to}/classnew/install/toplevel/school.php > #{deploy_to}/school.php"
#      open("#{deploy_to}/school.php", 'a') do |f|
#        f.puts "Updating school.php with new fields..."
#      end
    end
  end

  desc "Revert symlink"
  task :revertlink do
    on roles(:app) do
      execute "cp -pr #{deploy_to}/releases/#{File.basename release_path} #{deploy_to}/classnew"
      execute "rm -rf #{deploy_to}/current"
    end
  end

  after :finishing, 'deploy:resymlink'
  after :finishing, 'deploy:migrate'
  after :finishing_rollback, 'deploy:revertlink'
end

namespace :database do

  desc "Create a complete dump and download it"
  task :download do
    on roles(:app) do
      db = "#{fetch(:class_db)}"
      dumps = "#{fetch(:dumps_dir)}"
      today = Date.today.strftime("%d-%m-%Y")
      file = "#{db}-#{today}.sql"
      execute "mysqldump -p$DB_PASS -u class #{db} > #{dumps}/#{file}"
      execute "cd #{dumps} && tar zcvf #{file}.tar.gz #{file}"
      download!("#{dumps}/#{file}.tar.gz", "../database")
    end
  end

end

namespace :info do

	desc "Show latest deployed revision"
	task :version do
	  on roles(:app) do
		execute "tail #{deploy_to}/revisions.log -n 1"
	  end
	end

end
