<?php

class MembersDirectoryPage extends Page{

	private static $has_one = array(
		'Group' => 'Group'
	);

	function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Main",
			DropdownField::create("GroupID","Group",
				Group::get()->map()->toArray()
			)->setHasEmptyDefault(true)
		);
		return $fields;
	}
	
}

class MembersDirectoryPage_Controller extends Page_Controller{
	
	private static $allowed_actions = array(
		'view' => true
	);

	function getMembers(){
		$members = Member::get();
		$group = $this->Group();
		if($group->exists()){
			$members = $members->innerJoin("Group_Members", "Group_Members.MemberID = Member.ID")
				->filter("Group_Members.GroupID", $group->ID);
		}
		return $members;
	}

	function view() {
		if($member = $this->getMemberFromRequest()) {
			//shift the request params
			$this->request->shiftAllParams();
			$this->request->shift();
			$record = new MemberProfilePage(array(
				'ID' => -1,
				'Content' => '',
				'ParentID' => $this->ID,
				'MemberID' => $member->ID,
				'URLSegment' => 'view/'.$member->ID
			));
			$cont = new MemberProfilePage_Controller($record);
			$cont->setMember($member);
			return $cont;
		}
		$this->httpError(404);
	}

	protected function getMemberFromRequest(){
		return $this->getMembers()->byID(
			(int)$this->request->param('ID')
		);
	}

}