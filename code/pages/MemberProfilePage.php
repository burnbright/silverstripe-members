<?php

class MemberProfilePage extends Page{

	/**
	 * Allow viewing draft site when page is fake.
	 */
	public function canViewStage($stage = 'Live', $member = null) {
		return ($this->ID == -1) ? true : parent::canViewStage($stage, $member);
	}

}

class MemberProfilePage_Controller extends Page_Controller{

	private static $allowed_actions = array(
		'index' => true,
		'edit' => '->canEditProfile',
		'EditProfileForm' => '->canEditProfile',
		'updatedetails' => '->canEditProfile',
		'sendpassword' => '->canEditProfile'
	);
	
	protected $member = null;
	static $updatenotifications = false;
	private static $url_segment = 'profile';
	
	static function notify_of_updates($notify = true){
		self::$updatenotifications = $notify;
	}
	
	function Link($action = ''){
		return Controller::join_links(self::config()->url_segment, $action);
	}
	
	function init() {
		parent::init();
		if(!$this->member){
			$this->member = Member::currentUser();
		}
		if(!$this->member){
			Security::permissionFailure(
				$this,
				"You need to register or sign in before editing your profile."
			);
			return;
		}
	}

	function getTitle(){
		if($this->dataRecord->Title){
			return $this->dataRecord->Title;
		}
		return $this->member->Name;
	}
	
	function getMember() {
		return $this->member;
	}

	function setMember($member) {
		$this->member = $member;
		return $this;
	}
	
	function edit() {		
		$this->Title = "Edit Profile";
		$this->Content = '<p>Update your details using this form.</p>';
		$this->Form = $this->EditProfileForm();

		return array();
	}
	
	function EditProfileForm(){
		$fields = $this->member->getMemberFormFields();
		$fields->push(new LiteralField('ChangePasswordLink', 
			'<div class="field">
				<p>
					<a href="Security/changepassword">change password</a>
				</p>
			</div>'
		));
		$fields->push(new HiddenField('ID','ID',$this->member->ID));
		$fields->removeByName('Password');
		$actions = new FieldList(
			new FormAction('updatedetails','Update')
		);
		//TODO: add validator to check if changed email is taken
		$validator = new RequiredFields(
			'FirstName',
			'Surname',
			'Email'
		);
		$form =  new Form($this,"EditProfileForm",$fields,$actions,$validator);
		$form->loadDataFrom($this->member);
		$this->member->extend('updateEditProfileForm',$form);
		return $form;
	}
	
	function updatedetails($data,$form){
		$form->saveInto($this->member);
		$this->member->write();
		if(self::$updatenotifications){
			$name = $data['FirstName']." ".$data['Surname'];
			$body = "$name has updated their details via the website. Here is the new information:<br/>";
			foreach($this->member->getAllFields() as $key => $field){
				if(isset($data[$key])){
					$body .= "<br/>$key: ".$data[$key];
					$body .= ($field != $data[$key])? "  <span style='color:red;'>(changed)</span>" : "";
				}
			}
			$email = new Email(
				Email::getAdminEmail(),
				Email::getAdminEmail(),
				"Member details update: $name",
				$body
			);
			$email->send();
		}
		$form->sessionMessage("Your member details have been updated.", "good");
		$this->redirect($this->Link('edit'));
		return false;
	}
	
	function sendnewpassword(){
		if($m = Member::get()->byId((int)Director::urlParam('ID'))){
			$m->sendTempPasswordEmail();
			return array(
				'Title' => 'Password sent',
				'Content' => 'A new password has been sent to '.$m->Email.'.'
			);
		}
		$this->redirect(Director::absoluteBaseURL());
		return false;
	}

	function canEditProfile(){
		return (bool)Member::currentUserID();
	}
	
}
