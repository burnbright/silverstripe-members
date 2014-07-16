<?php
/**
 * Bulk loads members & adds them to a group, if specified.
 */
class MemberBulkLoader extends CsvBulkLoader {
	
	static protected $groupname = null;
	
	public $columnMap = array(
      'Name' => '->importFirstAndLastName'
	);
	
	public $duplicateChecks = array(
      'Email' => 'Email' //TODO: allow this to become whatever Member::get_unique_identifier_field(); is
   );
	
   static function importFirstAndLastName(&$obj, $val, $record) {
      $nameParts = explode(' ', trim($val));
		$obj->FirstName = array_shift($nameParts);
		$obj->Surname = join(' ', $nameParts);
   }
   
   protected function processRecord($record, $columnMap, &$results, $preview = false) {
   		//TODO:add callback for doing pre-load stuff
   		$this->extend('preprocess',$record,$columnMap, $results,$preview);
         $id = parent::processRecord($record, $columnMap, $results, $preview); 
   		$member = DataObject::get_by_id('Member',$id);
   		if(self::$groupname && $member){
   			Group::addToGroupByName($member,self::$groupname);
   		}
   		if($member){
			if(!$member->Created){
				$member->Created = date('Y-m-d H:i:s');
			}   			
   			$member->write();
   			$this->extend('postprocess',$member, $record, $columnMap, $results, $preview);//callback for doing other custom stuff
   		}
   		return $id;
   }
	
	/**
	 * Choose the group to add imported members to. Note this must be the group Code, and not Title.
	 */
   static function set_import_group($group){
   		self::$groupname = $group;
   }

}
