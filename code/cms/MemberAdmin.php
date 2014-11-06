<?php

class MemberAdmin extends ModelAdmin{
	
	private static $url_segment = "members";
	private static $menu_title = "Members";
	private static $menu_icon = 'members/images/members-icon.png';

	private static $managed_models = array(
		'Member'
	);

	private static $model_importers = array(
		'Member' => 'MemberBulkLoader'
	);

	public function getEditForm($id = null, $fields = null) {
		$form = parent::getEditForm();

		if($this->modelClass == "Member"){
			if($columns = Member::config()->export_fields){
				$form->Fields()->fieldByName("Member")->getConfig()
					->getComponentByType("GridFieldExportButton")
						->setExportColumns($columns);
			}
		}

		return $form;
	}

}