<?php

class EditProfileForm extends Form{

	protected $member;
	
	public function __construct($controller, $name, Member $member) {
		$this->member = $member;

		$fields = $this->member->getMemberFormFields();

		$fields->push(new HiddenField('ID','ID',$this->member->ID));
		$fields->removeByName('Password');
		$actions = new FieldList(
			new FormAction('updatedetails','Update')
		);

		//TODO: add validator to check if changed email is taken
		$validator = new Member_Validator(
			'FirstName',
			'Surname',
			'Email'
		);
		parent::__construct($controller, $name, $fields, $actions, $validator);

		if($passwordfield = $this->getChangePasswordField()){
			$fields->push($passwordfield);
		}

		$this->loadDataFrom($this->member);
		$this->member->extend('updateEditProfileForm', $this);
	}

	public function updatedetails($data, $form) {
		$form->saveInto($this->member);
		if(Member::config()->send_frontend_update_notifications){
			$this->sendUpdateNotification($data);
		}
		$this->member->write();
		$form->sessionMessage("Your member details have been updated.", "good");
		return $this->controller->redirectBack();
	}

	public function sendUpdateNotification($data) {
		$name = $data['FirstName']." ".$data['Surname'];
		$body = "$name has updated their details via the website. Here is the new information:<br/>";
		$notifyOnFields = Member::config()->frontend_update_notification_fields ?: DataObject::database_fields('Member'); 
		$changedFields = $this->member->getChangedFields(true, 2);
		$send = false;

		foreach($changedFields as $key => $field) {
			if(in_array($key, $notifyOnFields)) {
				$body .= "<br/><strong>$key:</strong><br/>" .
					"<strike style='color:red;'>" . $field['before'] . "</strike><br/>" . 
					"<span style='color:green;'>" . $field['after'] . "</span><br/>";
				$send = true;
			}
		}
		
		if($send){
			$email = new Email(
				Email::config()->admin_email,
				Email::config()->admin_email,
				"Member details update: $name",
				$body
			);
			$email->send();
		}
	}

	protected function getChangePasswordField() {
		if($this->member->ID != Member::currentUserID()){
			return;
		}
		$backurl = Controller::join_links(
			$this->controller->Link(),
			"edit"
		);
		return new LiteralField('ChangePasswordLink', 
			'<div class="field"><p>
					<a href="Security/changepassword?BackURL='.$backurl.'">change password</a>
				</p>
			</div>'
		);
	}

}
