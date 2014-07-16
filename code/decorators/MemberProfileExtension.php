<?php
class MemberProfileDecorator extends DataExtension{
	
	function Link($action = 'show'){
		if(!$action) $action = 'show';
		return "MemberProfile/$action/" . $this->owner->ID;
	}
	
}
