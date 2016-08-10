<?php

class MemberRegistrationPage_Controller extends Page_Controller{
	
	private static $allowed_actions = array(
		'Form'
	);
	private static $url_segment = "register";
	private static $enabled = true;
	
	function Link($action = null){
		return Controller::join_links(
			Director::baseURL(), self::config()->url_segment, $action
		);
	}
	
	function init(){
		if(!self::config()->enabled){
			return $this->httpError(404);
		}
		if($backurl = $this->getRequest()->getVar("BackURL")){
			Session::set("BackURL", $backurl);
		}
		parent::init();
	}
	
	function Title(){
		return _t("MemberRegistrationPage.TITLE", "Register");
	}
	
	function Form(){
		return MemberRegistrationForm::create($this, "Form");
	}
	
} 
