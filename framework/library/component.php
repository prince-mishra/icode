<?php

	class Component {

		private $generatrix;
		private $db;

		public function setGeneratrix($generatrix) {
			$this->generatrix = $generatrix;
			$this->db = $generatrix->getDatabase();
		}

		public function getDb() {
			return $this->db;
		}

	}

?>
