<?php
class WPSecurityController extends SecurityController{
	
	function __construct(){}
	
	function getLoggedUser(){
		return true;
	}
	
	function isLoggedIn(){
		return true;
	}
	
	function canWrite($bookId){
		return true;
	}
	
	function canRead($bookId){
		return true;
	}
	
	function canDelete($bookId){
		return true;
	}
	
	function canCreate($parentResourceId){
		return true;
	}
	
	function checkLoggedIn() {
		return true;
	}
	
	function checkWrite($bookId) {
		return true;
	}
	
	function checkRead($bookId) {
		return true;
	}
	
	function checkCreate() {
		return true;
	}
	
	function checkDelete($bookId) {
		return true;
	}

}
	