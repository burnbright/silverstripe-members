<?php

class MemberRegistrationForm extends Form{
	
	function __construct($controller,$name = "MemberRegistrationForm", $fields = null){
		if(!$fields){
			$restrictfields = array(
				Member::get_unique_identifier_field(),'FirstName','Surname'
			);
			$fields = singleton('Member')->scaffoldFormFields(array(
				'restrictFields' => $restrictfields,
				'fieldClasses' => array(
					'Email' => 'EmailField'
				)
			));
		}
		
		$fields->push(new ConfirmedPasswordField("Password"));
		
		$actions = new FieldSet(
			$register = new FormAction('register',"Register")		
		);
		$validator = new Member_Validator(Member::get_unique_identifier_field());
		parent::__construct($controller, $name, $fields, $actions,$validator);
		
		if(class_exists('SpamProtectorManager')) {
			SpamProtectorManager::update_form($this, null, array());
		}
		
		$this->extend('updateRegistration');
	}
	
	function validate(){
		$valid = parent::validate();
		$this->extend('updateValidation',$valid);
		return $valid;
	}
	
	function register($data,$form){
		//log out existing user
		if($member = Member::currentUser()){
			$member->logOut();
		}
		
		$member = Object::create("Member");
		$form->saveInto($member);
		$member->write();
		$this->extend('onRegister');
		$member->logIn();
		
		//Redirect
		if($back = Session::get("BackURL")){
			Director::redirect($back);
			return;
		}
		
		Director::redirect($this->Controller()->Link());
		return;
	}
	
}