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
		'edit' => true,
		'EditProfileForm' => '->canEditProfile',
		'updatedetails' => '->canEditProfile',
		'sendpassword' => '->canEditProfile'
	);

	private static $url_segment = 'profile';
	private static $enabled = true;

	protected $member = null;
	
	public function Link($action = null){
		if($this->URLSegment == get_class($this) && $segment = self::config()->url_segment){
			$this->data()->URLSegment = $segment;
		}
		return parent::Link($action);
	}
	
	public function init() {
		if(!self::config()->enabled){
			return $this->httpError(404);
		}
		parent::init();
		if(!$this->member){
			$currentuser = Member::currentUser();
			if(!$currentuser){
				return Security::permissionFailure($this,
					"You must log in to view your profile."
				);
			}else{
				$this->member = $currentuser;
			}
		}
	}

	public function index(){
		if(!$this->member){
			$this->httpError(404, "Member not found");
		}
		return array();
	}

	public function getTitle(){
		if($this->dataRecord->Title){
			return $this->dataRecord->Title;
		}
		return $this->member->Name;
	}
	
	public function getMember() {
		return $this->member;
	}

	public function setMember($member) {
		$this->member = $member;
		return $this;
	}
	
	public function edit() {
		if(!$this->canEditProfile()){
			return Security::permissionFailure($this,
				"You do not have permission to edit this profile."
			);
		}

		return array(
			'Title' => "Edit Profile",
			'Content' => '',
			'Form' => $this->EditProfileForm()
		);
	}
	
	public function EditProfileForm(){
		return new EditProfileForm($this, "EditProfileForm", $this->member);
	}
	
	public function canEditProfile(){
		return (bool)$this->member && $this->member->canEdit(
			Member::currentUser()
		);
	}
	
}
