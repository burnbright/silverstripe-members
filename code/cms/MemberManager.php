<?php
class MemberManager extends ModelAdmin{
	
	public static $managed_models = array(
		'Member'
	);
	
	public static $model_importers = array(
		'Member' => 'MemberBulkLoader'
	);
	
	
	static $url_segment = 'members';
	static $menu_title = 'Members';
	
	function sendNewMemberEmails(){
		if($group = DataObject::get_one('Group','Title = "NPSC Members"')){
			if($members = $group->Members()){
				foreach($members as $member){
					echo $member->Email." (".$member->Title.")";
					if($member->sendTempPasswordEmail('NPSCTestSiteEmail','Network website - please test the new site!')){
						echo " ..success";
					}else{
						echo " ..failed.";
					}
					echo "<br/>";
				}
				return "done";
			}else{
				return "No members in the group";
			}
		}else{
			return "No such group";
		}
	}
	
}
?>
