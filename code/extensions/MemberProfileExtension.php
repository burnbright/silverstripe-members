<?php

class MemberProfileExtension extends DataExtension{

	private static $has_one = array(
		'Image' => 'Image'
	);

	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab("Root.Image",
			UploadField::create("Image","Profile Image")
		);
	}
	
	public function getProfileLink($action = null) {
		if($directorypage = MembersDirectoryPage::get()->first()){
			return Controller::join_links(
				$directorypage->Link(),
				"view",
				$this->owner->ID,
				$action
			);
		}
		return Controller::join_links(
			Director::baseURL().MemberProfilePage_Controller::config()->url_segment,
			$action
		);
	}

	//allow content editors to CVED (CRUD)

	public function canCreate($member = null) {
		if(Permission::check("CMS_ACCESS_CMSMain")){
			return true;
		}
	}

	public function canView($member = null) {
		if(Permission::check("CMS_ACCESS_CMSMain")){
			return true;
		}
	}

	public function canEdit($member = null) {
		if(Permission::check("CMS_ACCESS_CMSMain")){
			return true;
		}
	}

	public function canDelete($member = null) {
		if(Permission::check("CMS_ACCESS_CMSMain")){
			return true;
		}
	}

}