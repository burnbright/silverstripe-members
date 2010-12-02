<?php
class MemberProfileDecorator extends DataObjectDecorator{
	
	function Link($action = 'show'){
		if(!$action) $action = 'show';
		return "MemberProfile/$action/" . $this->owner->ID;
	}
	
}
?>
