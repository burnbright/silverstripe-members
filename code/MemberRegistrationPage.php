<?php

class MemberRegistrationPage_Controller extends Page_Controller{
	
	static $url_segment = "register";
	
	function Link($action = null){
		return Controller::join_links(Director::baseURL(),self::$url_segment,$action);
	}
	
	function Title(){
		return "Register";
	}
	
	function Form(){
		return new MemberRegistrationForm($this,"Form");
	}
	
} 