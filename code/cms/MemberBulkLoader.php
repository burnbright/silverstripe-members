<?php

/**
 * Add some additional functionality to MemberCsvBulkLoader
 */
class MemberBulkLoader extends MemberCsvBulkLoader {
	
	public $columnMap = array(
		'Name' => '->importFirstAndLastName'
	);
	
	static function importFirstAndLastName(&$obj, $val, $record) {
		$nameParts = explode(' ', trim($val));
		$obj->FirstName = array_shift($nameParts);
		$obj->Surname = join(' ', $nameParts);
	}
	
	public function processRecord($record, $columnMap, &$results, $preview = false) {
		$skip = false;
		$this->extend('preprocess', $record, $columnMap, $results, $preview, $skip);
		if($skip){
			return 0;
		}
		$id = parent::processRecord($record, $columnMap, $results, $preview);
		if($member = Member::get()->byID($id)){
			$this->extend('postprocess', $member, $record, $columnMap, $results, $preview);//callback for doing other custom stuff
			$member->write();
			$member->destroy();
			unset($member);
		}
		return $id;
	}

}
