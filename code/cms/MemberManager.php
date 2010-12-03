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
	
	static $subject = "Temporary Password";
	
	function sendTempPasswordEmails(){
		
		if($members = DataObject::get('Member',"\"Password\" IS NULL OR \"Password\" = ''")){
			
			foreach($members as $member){
				echo $member->Email." (".$member->Title.")";
				if($member->sendTempPasswordEmail(null,self::$subject)){
					echo " ..success";
				}else{
					echo " ..failed.";
				}
				echo "<br/>";
			}
			return "done";
		}else{
			return "No members found";
		}

	}
	
}
?>
