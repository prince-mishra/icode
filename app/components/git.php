<?php

	class Git {

		private $repo_base = '/home/git/repositories/';

		private function execute($command) {
			ob_start();
			system($command);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		
		private function execute2($command) {
			$output = array();
			$ret = 0;
			exec($command, $output, $ret);
			return $output;
		}
		/*
		public function showDB() {
			$requests = new requests($this->getDB());
			$all_requests = $requests->select('*');
			foreach($all_requests as $request)
				display($request);	
		}
		public function commit() {
			$dir = '/var/www/gitrepos';
			chdir($dir);
			$command = 'git add .';
			system($command);
			$command = 'git commit -a -m "added repo"';
			system($command);
			$command = 'git push origin master';

			system($command);
		}
		
		*/
		
		/**
		 * Get diff between given revisions as text.
			git diff master test---->gives the diff between master and test 
 		*/
		public function git_diff($repo, $from, $to) {
			$command = 'git --git-dir=' . $this->repo_base . $repo . '.git diff  '. $from . '' . $to;
			return join("\n", execute2($command));
		}

/*diff --stat shows only the lines tat have changed and limits te length of shown text*/
		public function git_diffstat($repo, $commit, $commit_base = null) {
			if (is_null($commit_base)) {
			$commit_base = "$commit^";
			}
			$command = 'git --git-dir=' . $this->repo_base . $repo . '.git diff --stat  '. $commit_base . '..' . $commit;
			return join("\n", execute2($command));
		}

		
/**
 * Get project information from config and git, name/description and HEAD
 * commit info are returned in an array.
 */
		public function get_project_info($repo) {
			$info = array();
			$info['name'] = $repo;
			$info['description'] = file_get_contents($this->repo_base . $repo . '.git/description');
		//	display($info['name']);			
			//display($info['description']);

			//$headinfo = git_get_commit_info($repo, 'HEAD');
			$headinfo = $this->git_rev_list($repo);			
			//$info['head_stamp'] = $headinfo['author_utcstamp'];
			//$info['head_datetime'] = gmstrftime($conf['datetime'], $headinfo['author_utcstamp']);
			//$info['head_hash'] = $headinfo['h'];
			//$info['head_tree'] = $headinfo['tree'];
			display($headinfo);
			return $info;
		}		
		
		public function git_ls_tree2($repo, $tree)	{
			$entries = array();
			$command = 'git --git-dir=' . $this->repo_base . $repo . '.git ls-tree -l '. $tree;
			$output = execute2($command);
	// 100644 blob 493b7fc4296d64af45dac64bceac2d9a96c958c1    .gitignore
	// 040000 tree 715c78b1011dc58106da2a1af2fe0aa4c829542f    doc
			foreach ($output as $line) {
				$parts = preg_split('/\s+/', $line, 4);
				$entries[] = array('name' => $parts[3], 'mode' => $parts[0], 'type' => $parts[1], 'hash' => $parts[2]);
			}

			return $entries;
		}

/**
 * Get information about the given object in a tree, or null if not in the tree.
 */
	public function git_ls_tree_part($repo, $tree, $name) {
		$entries = git_ls_tree2($repo, $tree);
		foreach ($entries as $entry) {
			if ($entry['name'] === $name) {
				return $entry;
			}
		}
		return null;
	}
	
	public function git_search_commits($repo, $type, $string){
	
			if ($type == 'change') {
				//$cmd = 'log -S'. escapeshellarg($string);
				//$command = 'git --git-dir=' . $this->repo_base . $repo . '.git log -S' . escapeshellarg($string);
			}
			elseif ($type == 'commit') {
				$command = 'log -i --grep='. escapeshellarg($string);
			}
			elseif ($type == 'author') {
				//$cmd = 'log -i --author='. escapeshellarg($string);
				$command = 'git --git-dir=' . $this->repo_base . $repo . '.git log -i --author=' . escapeshellarg($string);
			}
			elseif ($type == 'committer') {
				$command = 'log -i --committer='. escapeshellarg($string);
			}
			else {
				die('Unsupported type');
			}
			$data = $this->execute($command);
			print_r($data);
				$encoded_data = urlencode($data);
				$lines = explode("%0A", $encoded_data);
				//print_r($lines);
			$results = array();
			$results = array('repo'=>$repo);
			foreach ($lines as $line) {
				$line = str_replace('%00', '', $line);
				$line = trim(urldecode($line));
				//if (preg_match('/^commit (.*?)$/', $line, $matches)) {
					//$result[] = $matches[1];
					if(strpos($line,'commit')===0){
						$commit=substr($line,7,40);
						$result['commit']=$commit;
						$results[]=$result;
					}
				//}
				//display($line);
			}
			//display($results);
			return $results;

		}

		
//original here		
		
		
		public function git_search($repo,$string) {
			display($repo);
			display($string);
			if(file_exists($this->repo_base . $repo . '.git') && is_dir($this->repo_base . $repo . '.git')) {
				//$command = 'git --git-dir=' . $this->repo_base . $repo . '.git log --author = ' . $string;
				$command = 'git --git-dir="/var/www/Generatrix/.git" log --author="prince"';
				$data = $this->execute($command);
				display ( $data );
				$encoded_data = urlencode($data);
				$lines = explode("%0A", $encoded_data);
				$count = 0;
				$line_count = 0;

				$search = array();
				$search = array('repo' => $repo);
				foreach($lines as $line) {
					/*$line = str_replace('%00', '', $line);
					$line = trim(urldecode($line));
					*/
					print_r($line);				
				
				
				}
				
			}//if ends
		}	
		public function git_rev_list($repo, $branch = 'HEAD', $count = 100) {
			if(file_exists($this->repo_base . $repo . '.git') && is_dir($this->repo_base . $repo . '.git')) {
				//$command = 'GIT_DIR=' . $this->repo_base . $repo . '.git git rev-list --header --max-count=' . $count. ' ' . $branch;
				$command = 'git --git-dir=' . $this->repo_base . $repo . '.git rev-list --header --max-count=' . $count. ' ' . $branch;
				//display ( $command );
				$data = $this->execute($command);
				display ( $data );
				$encoded_data = urlencode($data);
				$lines = explode("%0A", $encoded_data);

				$count = 0;
				$line_count = 0;

				$commits = array();
				$commit = array('repo' => $repo);
				foreach($lines as $line) {
					$line = str_replace('%00', '', $line);
					$line = trim(urldecode($line));

					// Read commit id
					if( (($count % 6) == 5) ) {
						if(!isset($commit['message'])) {
							$commit['message'] = $line;
						} else {
							$commit['message'] .= ' ' . $line;
						}

						$next_line = isset($lines[$line_count + 1]) ? $lines[$line_count + 1] : '';
						$next_line = str_replace('%00', '', $next_line);
						$next_line = urldecode($next_line);

						if(!isset($lines[$line_count + 1]) || ( (strlen($next_line) == 40) && (strpos($next_line, ' ') === false) )) {
							$commits[] = $commit;
							$commit = array('repo' => $repo);
							$count = 0;
						}
					}

					if(strpos($line, 'committer') === 0) {
						$new_line = trim(str_replace('committer ', '', $line));
						$breakup = explode(' ', $new_line);

						$timezone = $breakup[count($breakup) - 1];
						$time = $breakup[count($breakup) - 2];
						$email = $breakup[count($breakup) - 3];
						$email = str_replace('<', '', $email);
						$email = str_replace('>', '', $email);

						$name = array();
						for($i = 0; $i <= count($breakup) - 4; $i++) {
							$name[] = $breakup[$i];
						}
						$author_name = implode(' ', $name);

						$commit['committer_name'] = $author_name;
						$commit['committer_email'] = $email;
						$commit['committer_time'] = $time;
						$count = 5;
					}

					if(strpos($line, 'author') === 0) {
						$new_line = trim(str_replace('author ', '', $line));
						$breakup = explode(' ', $new_line);

						$timezone = $breakup[count($breakup) - 1];
						$time = $breakup[count($breakup) - 2];
						$email = $breakup[count($breakup) - 3];
						$email = str_replace('<', '', $email);
						$email = str_replace('>', '', $email);

						$name = array();
						for($i = 0; $i <= count($breakup) - 4; $i++) {
							$name[] = $breakup[$i];
						}
						$author_name = implode(' ', $name);

						$commit['author_name'] = $author_name;
						$commit['author_email'] = $email;
						$commit['author_time'] = $time;
						$count = 4;
					}

					if(strpos($line, 'parent') === 0) {
						$commit['parent'] = trim(str_replace('parent ', '', $line));
						$count = 3;
					}

					if(strpos($line, 'tree') === 0) {
						$commit['tree'] = trim(str_replace('tree ', '', $line));
						$count = 2;
					}

					if( (($count % 6) == 0) && (strlen($line) == 40) ) {
						$commit['commit'] = trim($line);
						$count = 1;
					}

					$line_count++;
				}
				return $commits;
			}
		}
		
		public function git_tree($repo) {
		   if(file_exists($this->repo_base . $repo . '.git') && is_dir($this->repo_base . $repo . '.git')) {
			$command = 'git --git-dir=' . $this->repo_base . $repo.'.git ls-tree -l HEAD';
			//display ( $command );
			$data = $this->execute($command);
			//display ( $data );
			$encoded_data = urlencode($data);
			//display($encoded_data);
			$encoded_data=substr($encoded_data,0,-3);
			$lines = explode("%0A", $encoded_data);
			//print_r($lines);
			$trees=array();
			$tree =array('repo'=>$repo);
			foreach($lines as $line) {
				$sublines=explode("%09",$line);
				foreach($sublines as $subline) {
				        $subline = str_replace('+', '', $subline);
				        $subline = trim(urldecode($subline));
				        if((strlen($subline))>40) {
						
					//display($subline);
						$tree['mode']=substr($subline,0,5);
						$tree['type']=substr($subline,6,4);
						$tree['commit']=substr($subline,10,40);
						$tree['object_size']=substr($subline,50);
						}//if ends
						else
						{
					 		$tree['file_name']=$subline;
					 //display($subline);
						}//else ends 
				}//foreach ends
				$trees[]=$tree;
			}//foreach ends	
				
			return $trees;
		   }	
		}
		
	       public function git_tags() {
	       			$command = 'git --git-dir=' . $this->repo_base .'nikant/.git tag';
	       			display($command);
	       			$data = $this->execute($command);
	       			display($data);
	       			}
	       
               public function show_branch($repo) {
               		if(file_exists($this->repo_base . $repo . '.git') && is_dir($this->repo_base . $repo . '.git')) {
				//$command = 'GIT_DIR=' . $this->repo_base . $repo . '.git git rev-list --header --max-count=' . $count. ' ' . $branch;
				$command = 'git --git-dir=' . $this->repo_base . $repo . '.git branch';
				//$command = 'git --git-dir="/var/www/Generatrix/.git" branch';
				//display ( $command );
				//$command = 'git --git-dir="/home/git/repositories/iCode.git" branch';
				$data = $this->execute($command);
//display ( $data );
				$encoded_data = urlencode($data);
				$lines = explode("%0A", $encoded_data);
                                //print_r($lines);
				//$count = 0;
				$line_count = 0;
				$branches =array();
				$branch =array('repo'=>$repo);
				foreach($lines as $line) {
					if(strpos($line,'%2A')===0) {
						  $line = str_replace('%2A', '', $line);
						  $line = trim(urldecode($line));
						  $branch['branch']=$line;
						  $branch['active']=1;
						  $branches[]=$branch;
					 }//if ends
				 	elseif((strpos($line,'++')===0)) {
				  
					  $line = trim(urldecode($line));
					  $branch['branch']=$line;
					  $branch['active']=0;
					  $branches[]=$branch;
					 }//elseif ends
				} 
				return $branches;
				  
        	           }//if ends
                   }//function ends
                   
                   //public function  
        
	}	

?>
