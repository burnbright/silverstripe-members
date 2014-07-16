<?php
class MemberProfile extends Page_Controller{
	
	protected $member = null;
	static $updatenotifications = false;
	static $url_segment = 'profile';
	
	static function notify_of_updates($notify = true){
		self::$updatenotifications = $notify;
	}
	
	function Link($action = ''){
		return Controller::join_links(self::$url_segment,$action);
	}
	
	function init(){
		parent::init();
		$this->member = Controller::CurrentMember();
		$this->Title = 'Member';
		if(!$this->member){
			Security::permissionFailure($this);
		}
	}
	
	function getMember(){
		return $this->member;
	}
	
	function index(){
		if(Member::currentUser())
			return array('Content' => '<p><a href="'.$this->Link('edit').'">edit profile</a></p>');
		return array();
	}
	
	function edit(){
		if($this->member){
			$this->Title = "Edit Profile";
			$this->Content = '<p>Update your details using this form.</p>';
			$this->Form = $this->EditProfileForm();
		}else{
			$this->redirect(Director::absoluteBaseURL()); //if no member, then direct home
		}
		return array();
	}
	
	function EditProfileForm(){
		$fields = $this->member->getMemberFormFields();
		$fields->push(new LiteralField('ChangePasswordLink','<div class="field"><p><a href="Security/changepassword">change password</a></p></div>'));
		$fields->push(new HiddenField('ID','ID',$this->member->ID));
		$fields->removeByName('Password');
		$actions = new FieldSet(
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
					$body .= ( $field != $data[$key])? "  <span style='color:red;'>(changed)</span>" : "";
				}
			}
			$email = new Email(Email::getAdminEmail(),Email::getAdminEmail(),"Member details update: $name",$body);
			$email->send();
		}
		$form->sessionMessage("Your member details have been updated.","good");
		$this->redirect($this->Link('edit'));
		return false;
	}
	
	function sendnewpassword(){
		//TODO: add ajax support
		if(Director::urlParam('ID') && Permission::check('ADMIN') && $m = DataObject::get_by_id('Member',Director::urlParam('ID'))){
			$m->sendTempPasswordEmail();
			return array(
				'Title' => 'Password sent',
				'Content' => 'A new password has been sent to '.$m->Email.'.'
			);
		}
		$this->redirect(Director::absoluteBaseURL());
		return false;
	}
	
}
