<?php

  class branches extends Model {
    public function __construct($database) {
      $this->construct($database, "branches", array(
				"id" => "int",
				"repo" => "varchar_128",
				"branch" => "varchar_64",
				"active" => "int"
      ));
    }
  }

  class commits extends Model {
    public function __construct($database) {
      $this->construct($database, "commits", array(
				"id" => "int",
				"repo" => "varchar_128",
				"commit" => "varchar_64",
				"tree" => "varchar_64",
				"parent" => "varchar_64",
				"author_name" => "varchar_128",
				"author_email" => "varchar_128",
				"author_time" => "int",
				"committer_name" => "varchar_128",
				"committer_email" => "varchar_128",
				"committer_time" => "int",
				"message" => "varchar_1024"
      ));
    }
  }

  class emails extends Model {
    public function __construct($database) {
      $this->construct($database, "emails", array(
				"id" => "int",
				"timestamp" => "int",
				"from" => "varchar_128",
				"subject" => "varchar_512",
				"body" => "text",
				"budget" => "varchar_128"
      ));
    }
  }

  class projects extends Model {
    public function __construct($database) {
      $this->construct($database, "projects", array(
				"id" => "int",
				"category" => "varchar_256",
				"name" => "varchar_256",
				"slug" => "varchar_128",
				"details" => "varchar_1024",
				"image1" => "varchar_256",
				"image2" => "varchar_256",
				"image3" => "varchar_256",
				"image4" => "varchar_256",
				"image5" => "varchar_256",
				"image6" => "varchar_256",
				"priority" => "int"
      ));
    }
  }

  class repoaccess extends Model {
    public function __construct($database) {
      $this->construct($database, "repoaccess", array(
				"id" => "int",
				"repo" => "varchar_128",
				"user" => "varchar_128",
				"active" => "int",
				"created" => "int",
				"updated" => "int"
      ));
    }
  }

  class repositories extends Model {
    public function __construct($database) {
      $this->construct($database, "repositories", array(
				"id" => "int",
				"name" => "varchar_512",
				"description" => "varchar_512",
				"group" => "varchar_128",
				"created" => "int"
      ));
    }
  }

  class requests extends Model {
    public function __construct($database) {
      $this->construct($database, "requests", array(
				"group" => "varchar_128",
				"repo" => "varchar_128",
				"user" => "varchar_128",
				"id" => "int"
      ));
    }
  }

  class tree extends Model {
    public function __construct($database) {
      $this->construct($database, "tree", array(
				"id" => "int",
				"repo" => "varchar_128",
				"mode" => "int",
				"type" => "varchar_10",
				"commit" => "varchar_64",
				"object_size" => "int",
				"file_name" => "varchar_64"
      ));
    }
  }

  class users extends Model {
    public function __construct($database) {
      $this->construct($database, "users", array(
				"id" => "int",
				"email" => "varchar_256",
				"password" => "varchar_256",
				"name" => "varchar_256",
				"permissions" => "int",
				"email1" => "varchar_256",
				"email2" => "varchar_256",
				"email3" => "varchar_256",
				"created" => "int"
      ));
    }
  }

?>