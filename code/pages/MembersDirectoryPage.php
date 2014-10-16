<?php

class MembersDirectoryPage extends Page{
	
}

class MembersDirectoryPage_Controller extends Page_Controller{
	
	private static $allowed_actions = array(
		'view' => true
	);

	function getMembers(){
		return Member::get();
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
				'MemberID' => $member->ID
			));
			$cont = new MemberProfilePage_Controller($record);
			$cont->setMember($member);
			return $cont;
		}
		$this->httpError(404);
	}

	protected function getMemberFromRequest(){
		return Member::get()->byID(
			(int)$this->request->param('ID')
		);
	}


}