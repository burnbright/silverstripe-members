<?php

class MemberRegistrationPage_Controller extends Page_Controller{
	
	private static $allowed_actions = array(
		'Form'
	);
	private static $url_segment = "register";
	
	function Link($action = null){
		return Controller::join_links(
			Director::baseURL(), self::config()->url_segment, $action
		);
	}
	
	function init(){
		parent::init();
		if($backurl = $this->getRequest()->getVar("BackURL")){
			Session::set("BackURL", $backurl);
		}
	}
	
	function Title(){
		return _t("MemberRegistrationPage.TITLE", "Register");
	}
	
	function Form(){
		return new MemberRegistrationForm($this, "Form");
	}
	
} 