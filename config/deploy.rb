# add ~/.ssh/id_rsa.pub to server:/home/class/.ssh/authorised_keys
# to run deployment: cap sample deploy

set :application, 'classis'
set :repo_url, 'git@github.com:LearningData/classis.git'

set :deploy_via, :remote_cache
set :scm, :git
set :use_sudo, false
set :pty, true
set :current_dir, "class"

set :keep_releases, 3

config_files_path = "../config/"
downloads = "../dumps"


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
	  execute "rm -rf #{deploy_to}/class"
	  execute "cp -pr #{deploy_to}/releases/#{File.basename release_path} #{deploy_to}/class"
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

	  execute "cd #{deploy_to}/class && php scripts/migrate_db.php --path=#{deploy_to}"
	end
  end

  desc "Download school config files"
  task :download_config do
	on roles(:app) do
	  db = "#{fetch(:class_db)}"
	  dumps = "#{fetch(:dumps_dir)}"
	  today = Date.today.strftime("%d-%m-%Y")
	  file = "#{db}-config-#{today}"
	  execute "tar zpcvf #{dumps}/#{file}.tar.gz -C #{deploy_to} school.php index.php dbh_connect.php images schoolarrays.php schoollang.php"
	  run_locally do
		if !Dir.exists?("#{downloads}/#{file}")
		  execute "mkdir -p #{downloads}/#{file}"
		end
	  end
	  download!("#{dumps}/#{file}.tar.gz", "#{downloads}/#{file}/")
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
	  execute "cp -pr #{deploy_to}/releases/#{File.basename release_path} #{deploy_to}/class"
	  execute "rm -rf #{deploy_to}/current"
	end
  end

  after :finishing, 'deploy:resymlink'
  after :finishing, 'deploy:migrate'
  after :finishing_rollback, 'deploy:revertlink'
end

namespace :install do

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
	  #upload!("#{downloads}/sample.sql", "#{deploy_to}/db_dumps")
	  #importdb_query = "source #{deploy_to}/db_dumps/sample.sql"
	  #execute "mysql -p$DB_PASS -u class -e \"#{importdb_query}\""
	end
  end

  desc "Create epfdirectory"
  task :create_epfdir do
	  on roles(:app) do
		  #execute "mkdir #{data_dir}"
		  #upload!("#{downloads}/sample-config-00000000.tar.gz", "#{data_dir})
		  #tar -xzvf #{data_dir}/sample-config-00000000.tar.gz
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
	  execute "cd #{dumps} && rm #{file}"
	  run_locally do
		if !Dir.exists?("#{downloads}/")
		  execute "mkdir #{downloads}"
		end
	  end
	  download!("#{dumps}/#{file}.tar.gz", "#{downloads}")
	end
  end

  desc "Create a complete data dump and download it"
  task :download_data do
	on roles(:app) do
	  db = "#{fetch(:class_db)}"
	  dumps = "#{fetch(:dumps_dir)}"
	  data = "#{fetch(:data_dir)}"
	  today = Date.today.strftime("%d-%m-%Y")
	  file = "#{db}-data-#{today}"
	  execute "tar zpcvf #{dumps}/#{file}.tar.gz --exclude='sessions/*' --exclude='cache/images/*' --exclude='cache/reports/*' -C #{data} ."
	  run_locally do
		if !Dir.exists?("#{downloads}/#{file}")
		  execute "mkdir -p #{downloads}/#{file}"
		end
	  end
	  download!("#{dumps}/#{file}.tar.gz", "#{downloads}/#{file}/")
	end
  end

  desc "Download templates only for the school"
  task :download_templates do
	on roles(:app) do
	  db = "#{fetch(:class_db)}"
	  dumps = "#{fetch(:dumps_dir)}"
	  today = Date.today.strftime("%d-%m-%Y")
	  file = "#{db}-templates-#{today}"
	  templates = capture("mysql -N -B -p$DB_PASS -u class #{db} -e 'SELECT transform FROM report WHERE transform!=\"\" GROUP BY transform;'")
	  execute "mkdir -p #{dumps}/#{db}_templates"
	  templates.split(/\r?\n|\r/).each do | line |
		['xsl','css','js'].each do | extension |
		  template_file = "#{deploy_to}/templates/#{line}.#{extension}"
		  if test("[ -f #{template_file} ]")
		    execute "cp -p #{template_file} #{dumps}/#{db}_templates/"
		  end
		end
      end
	  execute "tar zpcvf #{dumps}/#{file}.tar.gz -C #{dumps}/#{db}_templates ."
	  run_locally do
	  	if !Dir.exists?("#{downloads}/#{file}")
	  	  execute "mkdir -p #{downloads}/#{file}"
	  	end
	  end
	  download!("#{dumps}/#{file}.tar.gz", "#{downloads}/#{file}/")
	end
  end

end

namespace :info do

  desc "Show latest deployed revision"
  task :version do
	SSHKit.config.output_verbosity = Logger::ERROR
	on roles(:app) do
	  revision = capture "tail #{deploy_to}/revisions.log -n 1"
	  puts revision
	end
  end

  desc "Show ssh connection"
  task :sshconfig do
	SSHKit.config.output_verbosity = Logger::ERROR
	on roles(:app) do |host|
	  connection = "#{host.user}@#{host.hostname}:#{host.port}#{deploy_to}"
	  puts connection
	end
  end

end
