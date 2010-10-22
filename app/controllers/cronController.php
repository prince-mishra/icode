<?php

	/*
		You can do the following in the controller

		1. TO DISPLAY ERRORS :
				display_error("Calls to the function <strong>display_error($message)</strong> are displayed like this");
				display_warning("Calls to the function <strong>display_warning($message)</strong> are displayed like this");
				display_system("Calls to the function <strong>display_system($message)</strong> are displayed like this");
				display("Calls to the function <strong>display($message)</strong> are displayed like this");

		2. TO HANDLE DATABASES :
				If you have the following table
				CREATE TABLE IF NOT EXISTS `students` (
					`id` int(11) NOT NULL auto_increment,
					`name` varchar(64) NOT NULL,
					`phone` varchar(64) NOT NULL,
					`status` varchar(128) NOT NULL,
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

				Then run php index.php generatrix preparedb

				This would create a class students and you can run
				$students = new students($this->getDb());
				$students_data = $students->select("*", "WHERE id=5");
				$students_data = $students->delete("WHERE id=5");
				$students_data = $students->update(array("name" => "sudhanshu"), "WHERE id=5");
				$students_data = $students->insert(array("name" => "sudhanshu", "phone" => "1234567890", "status" => "working on generatrix"));

		3. TO PASS VALUES TO THE VIEW :
				$this->set("sample", "This is sample content which was set in the controller");
				$this->set("students_data", $students_data);
	*/

	class cronController extends Controller {

		public function base() {

		}
		
		public function gitosis() {
			$gitosis = '/var/www/gitosis-admin/';

			chdir($gitosis);
			//system('git pull origin master');

			$data = parse_ini_file($gitosis . 'gitosis.conf', true);
			
			
			/* database hasn't been created. create the database according to schema here and come back*/
			/* For creating database, make the suitable databse in the mysql, and then in Generatrix folder, run prepareModel. 
			It will automatically creates php classes of the same*/
			
			$repositories = new repositories($this->getDb());
			$users = new users($this->getDb());
			$repoaccess = new repoaccess($this->getDb());

			foreach($data as $block => $block_contents) {
				$block_name = $block;
				$block_name = str_replace('group ', '', $block_name);

				$writable = isset($block_contents['writable']) ? $block_contents['writable'] : false;
				$members = isset($block_contents['members']) ? $block_contents['members'] : false;

				if($writable && $members) {
					$repos = explode(' ', $writable);
					foreach($repos as $repo) {
						// Check if repo exists
						if(trim($repo) != '') {
							$repo_data = $repositories->select('*', 'WHERE name="' . $repo . '"');
							if(!isset($repo_data[0]['id'])) {
								$repositories->insert(array(
									'name' => $repo,
									'description' => 'No description was provided',
									'group' => $block_name,
									'created' => time()
								));
								display('Added repo ' . $repo);
							}

							// Check permissions
							$users_array = explode(' ', $members);
							foreach($users_array as $member) {
								if(trim($member) != '') {
									$user_access = $repoaccess->select('*', 'WHERE repo="' . $repo . '" AND user="' . $member . '"');
									if(!isset($user_access[0]['id'])) {
										$repoaccess->insert(array(
											'repo' => $repo,
											'user' => $member,
											'active' => 1,
											'created' => time(),
											'updated' => time()
										));
										display('Gave user ' . $member . ' access to repo ' . $repo);
									}

									// TODO : It seems some users are getting deleted and added again

									// Check if any access has been removed
									$repo_access = $repoaccess->select('*', 'WHERE repo="' . $repo . '"');
									if(count($repo_access) != count($members)) {
										foreach($repo_access as $access_details) {
											if(!in_array($access_details['user'], $users_array)) {
												$repoaccess->delete('WHERE repo="' . $repo . '" AND user="' . $access_details['user'] . '"');
												display('Removed user ' . $access_details['user'] . ' from repo ' . $repo);
											}
										}
									}
								}
							}
						}

					}

				}
			}
		}

		public function commits() {
			$git = new Git();
			$repositories = new repositories($this->getDb());
			$commits = new commits($this->getDb());

			$all_repos = $repositories->select('*');
			foreach($all_repos as $repo) {
				display ( $repo );
				$repo_name = isset($repo['name']) ? $repo['name'] : false;
				if($repo_name) {
					$repo_commits = $git->git_rev_list($repo_name);
					display ( $repo_commits );
					if(count($repo_commits) > 0) {
						foreach($repo_commits as $repo_commit) {
							if(isset($repo_commit['repo']) && isset($repo_commit['commit']) && ($repo_commit['repo'] != '') && ($repo_commit['commit'] != '')) {
								$old_data = $commits->select('*', 'WHERE repo="' . $repo_commit['repo'] . '" AND commit="' . $repo_commit['commit'] . '"');
								if(!isset($old_data[0]['id'])) {
									$commits->insert($repo_commit);
								}
							}
						}
					}
				}
			}
		}
		
		
		public function info() {
			$git = new Git();
			$repo = 'Generatrix';
			$info=$git->get_project_info($repo);
			display($info);
		}
		public function write() {
			$git = new Git();
		/*	$sampleData = array(
			'first' => array(
                   	'first-1' => 1,
                    	'first-2' => 2,
                    	'first-3' => 3,
                    	'first-4' => 4,
                    	'first-5' => 5,
                	),
                	'second' => array(
                   	 'second-1' => 1,
                    	'second-2' => 2,
                    	'second-3' => 3,
                   	 'second-4' => 4,
                    	'second-5' => 5,
                	));*/
                	if(isset($_POST['reponame'])) {
                		$sampleData = array('group newteam' => array('writable' => $_POST['reponame'],'members' => 'prince@prince-laptop'));
                		$git->write_ini_file($sampleData, './newdata.conf', true);
                	}
			
		}
		
		public function search() {
		$git = new Git();
		$repo = 'Generatrix';
		$type= 'author';
		$string = 'prince';
		$search_result = $git->git_search_commits($repo,$type,$string);
		//$print_r($search_result);
		//$search_result = $git->git_search($repo,$string);
			
		//display($search_result);
		}
		
		public function tree() {
			$git = new Git();
			$repositories = new repositories($this->getDb());
			$tree = new tree($this->getDb());
			//$tree_data=$tree->delete();
			//$repo_name = 'gitosis-admin';
			$all_repos = $repositories->select('*');
			//$repo_tree = $git->git_tree($repo_name);
			//$repo_tree = $git->git_tree();
			/*foreach($repo_tree as $repo_tree_term)
			{
			        
				$tree->insert($repo_tree_term);
			}*/
			foreach($all_repos as $repo) {
				display ( $repo );
				$repo_name = isset($repo['name']) ? $repo['name'] : false;
				if($repo_name) {
					$repo_trees = $git->git_tree($repo_name);
					display ( $repo_trees );
					if(count($repo_trees) > 0) {
						foreach($repo_trees as $tree_term) {
							if(isset($tree_term['repo']) && isset($tree_term['commit']) && ($tree_term['repo'] != '') && ($tree_term['commit'] != '')) {
								$old_data = $tree->select('*', 'WHERE repo="' . $tree_term['repo'] . '" AND commit="' . $tree_term['commit'] . '"');
								if(!isset($old_data[0]['id'])) {
									$tree->insert($tree_term);
								}
							}
						}
					}
				}
			}
				
		}
		
		public function tags() {
			$git = new Git();
			$repo_tags = $git->git_tags();
			}
		
		
		 public function branch() {
			$git = new Git();
			$repositories = new repositories($this->getDb());
			
			$branches = new branches($this->getDb());
			$all_repos = $repositories->select('*');
			//$branches_data = $branches->delete();
			//$repo_name='iCode';
			//$repo_branchs = $git->show_branch($repo_name);
			/*foreach($repo_branchs as $repo_branch)
			{
			        
				$branches->insert($repo_branch);
			}*/
			
			foreach($all_repos as $repo) {
				//display ( $repo );
				$repo_name = isset($repo['name']) ? $repo['name'] : false;
				if($repo_name) {
					$repo_branchs = $git->show_branch($repo_name);
					
					//$branches->delete('WHERE repo="'.$repo_name.'"');
					 
					if(count($repo_branchs) > 0) {
						foreach($repo_branchs as $repo_branch) {
							if(isset($repo_branch['repo']) && isset($repo_branch['branch']) && ($repo_branch['repo'] != '') && ($repo_branch['branch'] != '')) {
								 $old_data = $branches->select('*', 'WHERE repo="' . $repo_branch['repo'] . '" AND branch="' . $repo_branch['branch'] . '"');
								if(!isset($old_data[0]['id'])) {
									$branches->insert($repo_branch);
								}
							}
						}
					}
				}
				$repo_branchs_indb=$branches->select('*', 'WHERE repo="'.$repo_name.'"');
				if(count($repo_branchs)!=count($repo_branchs_indb)) {
				 foreach($repo_branchs_in_db as $branch_in_db) {
				  if(!in_array($branch_in_db,$repo_branchs)) {
				  	$branches->delete('WHERE repo="'.$repo_name.'" AND branch="' .$branch_in_db . '"');
				  }
				}
			    }  
			}
		}

	}
	

?>
