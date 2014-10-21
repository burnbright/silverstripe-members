<?php

class MemberRegistrationForm extends Form{

	private static $allowed_actions = array(
		'register'
	);
	
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
		
		$actions = new FieldList(
			$register = new FormAction('register',"Register")		
		);
		$validator = new MemberRegistration_Validator(
			Member::get_unique_identifier_field(),
			'FirstName',
			'Surname'
		);
		parent::__construct($controller, $name, $fields, $actions, $validator);
		
		if(class_exists('SpamProtectorManager')) {
			$this->enableSpamProtection();
		}
		
		$this->extend('updateRegistration');
	}
	
	public function register($data, $form){

		//log out existing user
		if($member = Member::currentUser()){
			$member->logOut();
		}
		
		$member = Member::create();
		$form->saveInto($member);
		$member->write();
		$this->extend('onRegister');
		$member->logIn();
		
		if($back = Session::get("BackURL")){
			Session::clear("BackURL");
			return $this->Controller()->redirect($back);
		}
		
		return $this->Controller()->redirect($this->Controller()->Link());
	}
	
}

class MemberRegistration_Validator extends Member_Validator{
	
	function php($data) {
		$valid = parent::php($data);
	
		$identifierField = Member::config()->unique_identifier_field;
	
		$member = Member::get()
					->filter($identifierField, $data[$identifierField])
					->first();

		if(is_object($member) && $member->isInDB()) {
			$uniqueField = $this->form->Fields()->dataFieldByName($identifierField);
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