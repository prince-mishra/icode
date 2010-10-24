<?php

	$repos = $this->get('repos');
	//display($repos);
	$count = 1;
	foreach($repos as $repo) {
		$data = array();

		$fields = array('id', 'name', 'description', 'group', 'created');
		foreach($fields as $field) {
			$data[$field] = checkArray($repo, $field) ? $repo[$field] : '';
		}

		echo $count . ". ";
		echo "<a href='" . href('/projects/info/' . $data['name']) . "'>" . substr($data['name'], 0, 30) . "</a> ";
		echo "<span style='color: grey; font-size: 9px;'>" . substr($data['description'], 0, 60) . "</span>";
		//echo date("d M Y", $data['created']);
		//echo " (<a href='" . href('/posts/remove/' . $data['id']) . "'>del</a>)";
		echo "<br />";

		$count++;
	}

?>
