<?php
class SendTempPasswordDecorator extends DataObjectDecorator{
	
	private $called = false;
	
	function sendTempPasswordEmail($template = null, $subject = null,$extradata = null){
		//set expiry
		$tp = ($template) ? $template : 'TempPasswordEmail';
		$sbj = ($subject) ? $subject : "Temporary Password";
		
		$data = array('CleartextTempPassword' => $this->owner->setupTempPassword());
		if($extradata)
			$data = array_merge($data,$extradata);
		
		$body = $this->owner->customise($data)->renderWith($tp);
		
		if(Email::validEmailAddress($this->owner->Email)){
			$email = new Email(Email::getAdminEmail(),$this->owner->Email,$sbj,$body);
			if($email->send()){
				return true;
			}
			return false;
		}
		return false;
	}
	
	function setupTempPassword($expires = false){
		if($expires){
			$this->owner->PassworExpiry = date($expires); //TODO: not working yet
		}
		$password = substr(md5(microtime()),0,6);
		$this->owner->changePassword($password);
		return $password;
	}
	
	/** Adds 'send temp password' link **/
	function updateCMSFields(FieldSet &$fields) {
		//requirements: ajax link
		if(!$this->called) //hack because member getCMSFields calls parent, which also has the extend->('updateCMSFields')
			$fields->addFieldToTab('Root.Actions',new LiteralField('TempPasswordLink','<a href="'.$this->owner->Link('sendnewpassword').'" target="new">send temp password</a>'));
		$this->called = true;
	}
	
	//edit details form that removes the password expiry
	
}
?>
