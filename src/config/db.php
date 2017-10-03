<?php
	class db {
		private $dbhost = 'localhost';
		private $dbuser = 'alolu';
		private $dbname = 'wordpressAPI';
		private $dbpass = 'famille';

		public function connect(){
			$connect_str = 'mysql:dbname='.$this->dbname.';host='.$this->dbhost;
			$dbConnect = new PDO($connect_str,$this->dbuser,$this->dbpass);
			$dbConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			return $dbConnect;
		}
	}
?>