<?php
/*  Gelsheet Project, version 0.0.1 (Pre-alpha)
 *  Copyright (c) 2008 - Ignacio Vazquez, Fernando Rodriguez, Juan Pedro del Campo
 *
 *  Ignacio "Pepe" Vazquez <elpepe22@users.sourceforge.net>
 *  Fernando "Palillo" Rodriguez <fernandor@users.sourceforge.net>
 *  Juan Pedro "Perico" del Campo <pericodc@users.sourceforge.net>
 *
 *  Gelsheet is free distributable under the terms of an GPL license.
 *  For details see: http://www.gnu.org/copyleft/gpl.html
 *
 */
	class UserController extends FrontController {

		private $currentUser;

		public function __construct($currentUser= null){

			if ($currentUser== null){


				if (isset($_SESSION['user']['id'])){

					$this->currentUser= $_SESSION['user']['id'];

				}

			}
			else {

				$this->currentUser= $currentUser;

			}


		}

		public function __destruct(){}

		public function getCurrentUser(){

			return $this->currentUser;

		}



	
		

		/*returns all users from database*/
		public function getUsers(){

			$sql= "SELECT * FROM ".table("users")."";

			$result= mysql_query($sql);

			$users= array();

			while ($row= mysql_fetch_row($result)){

				$users[] = array(
					'userId'	=>	$row->userId	,
					'userName'	=> 	$row->userName
				);

			}

			return $users;


		}


	}


?>