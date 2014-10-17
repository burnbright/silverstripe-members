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
	
	public function ProfileLink($action = null) {
		if($directorypage = MembersDirectoryPage::get()->first()){
			return Controller::join_links(
				$directorypage->Link(),
				"view",
				$this->owner->ID,
				$action
			);
		}
	}

}