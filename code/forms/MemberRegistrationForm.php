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
		$validator = new MemberRegistration_Validator(Member::get_unique_identifier_field(),'FirstName','Surname');
		parent::__construct($controller, $name, $fields, $actions,$validator);
		
		if(class_exists('SpamProtectorManager')) {
			SpamProtectorManager::update_form($this, null, array());
		}
		
		$this->extend('updateRegistration');
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

class MemberRegistration_Validator extends Member_Validator{
	
	function php($data) {
		$valid = parent::php($data);
	
		$identifierField = Member::get_unique_identifier_field();
	
		$SQL_identifierField = Convert::raw2sql($data[$identifierField]);
		$member = DataObject::get_one('Member', "\"$identifierField\" = '{$SQL_identifierField}'");
	
		if(is_object($member) && $member->isInDB()) {
			$uniqueField = $this->form->dataFieldByName($identifierField);
			$this->validationError(
				$uniqueField->id(),
				sprintf(
					_t(
						'Member.VALIDATIONMEMBEREXISTS',
						'A member already exists with the same %s'
					),
					strtolower($identifierField)
				),
				'required'
			);
			$valid = false;
		}
	
		// Execute the validators on the extensions
		if($this->extension_instances) {
			foreach($this->extension_instances as $extension) {
				if(method_exists($extension, 'hasMethod') && $extension->hasMethod('updatePHP')) {
					$valid &= $extension->updatePHP($data, $this->form);
				}
			}
		}
	
		return $valid;
	}
	
}