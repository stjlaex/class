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

config_files_path = "../config/"


before :deploy, 'deploy:confirm'

namespace :deploy do

  desc "Confirmation"
  task :confirm do
    puts <<-EOF

    ************************** WARNING ***************************
    If you type [y] you will deploy to #{fetch(:stage)}.
    **************************************************************

    EOF
    ask :answer, "Are you sure you want to deploy to #{fetch(:stage)}?: "
    if fetch(:answer) != 'y'
      abort
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
      today = DateTime.now.strftime("%d-%m-%Y-%H-%M-%S")
      file = "#{db}-#{today}-pre-migration.sql"
      execute "mysqldump -p$DB_PASS -u class #{db} > #{dumps}/#{file}"
      execute "cd #{dumps} && tar zcvf #{file}.tar.gz #{file}"
      execute "rm #{dumps}/#{file}"

      execute "cd #{deploy_to}/classnew && php scripts/migrate_db.php --path=#{deploy_to}"
	end
  end

  desc "Update config file"
  task :update_config do
	on roles(:app) do
      config_files_path.each do |file|
        if File.exists?(file)
          upload!("#{file}", "/tmp/config_files")
        else
          puts "File #{file} not found"
        end
      end
	  #execute "cp -p #{deploy_to}/school.php #{deploy_to}/config_backup/"
	  #execute "cp -p #{deploy_to}/schoolarrays.php #{deploy_to}/config_backup/"
	  #execute "cp -p #{deploy_to}/schoollang.php #{deploy_to}/config_backup/"
	  #execute "cp -p #{deploy_to}/dbh_connect.php #{deploy_to}/config_backup/"
	  #execute "cp -pr #{deploy_to}/images #{deploy_to}/config_backup/"
      #execute "cp -pr /tmp/config_files/* #{deploy_to}/"
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

namespace :install do

  desc "Download school config files"
  task :download_config do
    on roles(:app) do
	  download!("#{deploy_to}/school.php", "../config")
	  download!("#{deploy_to}/schoolarrays.php", "../config")
	  download!("#{deploy_to}/schoollang.php", "../config")
	  download!("#{deploy_to}/dbh_connect.php", "../config")
	  download!("#{deploy_to}/images", "../config", :recursive => true)
	end
  end

  desc "Upload school config files"
  task :upload_config do
    on roles(:app) do
	  #upload!("../config/school.php", "#{deploy_to}/school.php")
	  #upload!("../config/schoolarrays.php", "#{deploy_to}/schoolarray.php")
	  #upload!("../config/schoollang.php", "#{deploy_to}/schoollang.php")
	  #upload!("../config/dbh_connect.php", "#{deploy_to}/dbh_connect.php")
	  #upload!("../config/images", "#{deploy_to}/school.php", :recursive => true)
	end
  end

  desc "Create database for school"
  task :create_db do
	on roles(:app) do
	  #createdb_query = "CREATE DB #{fetch(:class_db)}"
	  #execute "mysql -p$DB_PASS -u class -e \"#{createdb_query}\""
	  #upload!("../database/sample.sql", "#{deploy_to}/db_dumps")
	  #importdb_query = "source #{deploy_to}/db_dumps/sample.sql"
	  #execute "mysql -p$DB_PASS -u class -e \"#{importdb_query}\""
	end
  end

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
