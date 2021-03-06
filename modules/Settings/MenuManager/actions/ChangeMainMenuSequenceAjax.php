<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): JoForce.com
 * ***********************************************************************************/

Class Settings_MenuManager_ChangeMainMenuSequenceAjax_Action extends Settings_Head_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('removeModule');
		$this->exposeMethod('addModule');
		$this->exposeMethod('saveSequence');
	}
	
	public function checkPermission(Head_Request $request) {
                return true;
        }

	public function process(Head_Request $request) {
		$mode = $request->get('mode');
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	function removeModule(Head_Request $request) {
		global $adb, $current_user;
		$user_id = $current_user->id;
		$menuname = $request->get('menuname');
		$admin_status = Settings_MenuManager_Module_Model::isAdminUser();
                $moduleSequence = $request->get('sequence');

                if($admin_status == 'true')
                {
                        if(file_exists("storage/menu/main_menu_".$user_id.".php"))
                        {
			$file_name = "storage/menu/main_menu_".$user_id.".php";
			require($file_name);
			}
			else
			{
			$file_name = "storage/menu/main_menu_".$user_id.".php";
                        require("storage/menu/default_main_menu.php");
			}
        	        foreach($main_menu_array as $key => $array )
                		{
                        	if($menuname == $array['name'])
                        		{
	                                unset($main_menu_array[$key]);
        	                        }
                	        }
			$new_main_menu_array = array_values($main_menu_array);
			$myfile = fopen($file_name , "w") or die("Unable to open file!");
                	fwrite($myfile, "<?php
		".'$main_menu_array'." = " .var_export($new_main_menu_array, true). ";
		?>");
                	fclose($myfile);
		}
		else
		{
			if(file_exists("storage/menu/main_menu_".$user_id.".php"))
			{
				$file_name = "storage/menu/main_menu_".$user_id.".php";
	                        require($file_name);
				foreach($main_menu_array as $key => $array )
                                {
                                if($menuname == $array['name'])
                                        {
                                        unset($main_menu_array[$key]);
                                        }
                                }
	                        $new_main_menu_array = array_values($main_menu_array);
        	                $myfile = fopen($file_name , "w") or die("Unable to open file!");
                	        fwrite($myfile, "<?php
	                ".'$main_menu_array'." = " .var_export($new_main_menu_array, true). ";
        	        ?>");
                	        fclose($myfile);
			}
			else
			{
				$file_name = "storage/menu/main_menu_".$user_id.".php";
	                        require("storage/menu/default_main_menu.php");
				foreach($main_menu_array as $key => $array )
                                {
                                if($menuname == $array['name'] || ((Settings_MenuManager_Module_Model::isPermittedModule($array['tabid'])) !== false))
                                        {
                                        unset($main_menu_array[$key]);
                                        }
                                }
	                        $new_main_menu_array = array_values($main_menu_array);
        	                $myfile = fopen($file_name , "w") or die("Unable to open file!");
                	        fwrite($myfile, "<?php
	                ".'$main_menu_array'." = " .var_export($new_main_menu_array, true). ";
        	        ?>");
                	        fclose($myfile);
			}
		}
		$response = new Head_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}

	function addModule(Head_Request $request) {
		global $adb, $current_user;
		$user_id = $current_user->id;
		$type = $request->get('type');
		$admin_status = Settings_MenuManager_Module_Model::isAdminUser();

		if($type == "module")
		{
			if($admin_status == 'true')
			{
				if(file_exists("storage/menu/main_menu_".$user_id.".php"))
	                        {
        	                        $file_name = "storage/menu/main_menu_".$user_id.".php";
                	                require($file_name);
                        	}
	                        else
        	                {
                	                $file_name = "storage/menu/main_menu_".$user_id.".php";
                        	        require('storage/menu/default_main_menu.php');
	                        }
				$tabid = $request->get('tabid');
				array_push($main_menu_array, ['tabid' =>$tabid, 'type' => $type, 'name' => getTabModuleName($tabid)]);
	                	$myfile = fopen($file_name, "w") or die("Unable to open file!");
	        	        fwrite($myfile, "<?php
			".'$main_menu_array'." = " .var_export($main_menu_array, true). ";
			?>");
        		        fclose($myfile);
			}
			else
			{
				if(file_exists("storage/menu/main_menu_".$user_id.".php"))
                                {       
                                        $file_name = "storage/menu/main_menu_".$user_id.".php";
                                        require($file_name);
					foreach($main_menu_array as $key => $array)
                                	{
						if($array['type'] == 'module')
						{
	                                        	if((Settings_MenuManager_Module_Model::isPermittedModule($array['tabid'])) == false)
		                                        {
        		                                unset($main_menu_array[$key]);
                		                        }
						}
                        	        }
					$main_menu_array = array_values($main_menu_array);
					$tabid = $request->get('tabid');
	                                array_push($main_menu_array, ['tabid' =>$tabid, 'type' => $type, 'name' => getTabModuleName($tabid)]);
        	                        $myfile = fopen($file_name, "w") or die("Unable to open file!");
                	                fwrite($myfile, "<?php
                        	        ".'$main_menu_array'." = " .var_export($main_menu_array, true). ";
                                	?>");
	                                fclose($myfile);
                                }
                                else    
                                {       
                                        $file_name = "storage/menu/main_menu_".$user_id.".php";
                                        require('storage/menu/default_main_menu.php');
					foreach($main_menu_array as $key => $array)
                                        {
                                                if((Settings_MenuManager_Module_Model::isPermittedModule($array['tabid'])) !== false)
                                                {
                                                unset($main_menu_array[$key]);
                                                }
                                        }
                                        $main_menu_array = array_values($main_menu_array);

	                                $tabid = $request->get('tabid');
        	                        array_push($main_menu_array, ['tabid' =>$tabid, 'type' => $type, 'name' => getTabModuleName($tabid)]);
                	                $myfile = fopen($file_name, "w") or die("Unable to open file!");
                        	        fwrite($myfile, "<?php
	                        	".'$main_menu_array'." = " .var_export($main_menu_array, true). ";
	        	                ?>");
        	                        fclose($myfile);
				}
			}
		}
		
		else
		{
                	if(file_exists("storage/menu/main_menu_".$user_id.".php"))
                                {
                                        $file_name = "storage/menu/main_menu_".$user_id.".php";
                                        require($file_name);
                                }
                        else
                                {
                                        $file_name = "storage/menu/main_menu_".$user_id.".php";
                                        require('storage/menu/default_main_menu.php');
                                }
			$linkname = $request->get('linkname');
	                $linkurl = $request->get('linkurl');
                        array_push($main_menu_array, ['name' => $linkname, 'linkurl' => $linkurl, 'type' => $type]);
	                $myfile = fopen($file_name, "w") or die("Unable to open file!");
        	        fwrite($myfile, "<?php
		        ".'$main_menu_array'." = " .var_export($main_menu_array, true). ";
		        ?>");
                        fclose($myfile);

		}
		$response = new Head_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}

	function saveSequence(Head_Request $request) {
		global $adb, $current_user;
		$user_id = $current_user->id;
		$admin_status = Settings_MenuManager_Module_Model::isAdminUser();
		$moduleSequence = $request->get('sequence');
	
		if($admin_status == 'true')
		{
			if(file_exists("storage/menu/main_menu_".$user_id.".php"))
			{
				$file_name = "storage/menu/main_menu_".$user_id.".php";
				require($file_name);
			}
			else
			{
				$file_name = "storage/menu/main_menu_".$user_id.".php";
				require('storage/menu/default_main_menu.php');
			}
			$new_main_menu_array = [];
                       
        	        foreach($moduleSequence as $menuname => $sequence)
                                {
                       	        foreach($main_menu_array as $array )
                               	        {
                                       	if( $menuname == $array['name'])
                                               	{
	                                        array_push($new_main_menu_array, $array);
        	                                }       
                                        }
                       	        }
	                $myfile = fopen($file_name, "w") or die("Unable to open file!");
                        fwrite($myfile, "<?php
		        ".'$main_menu_array'." = " .var_export($new_main_menu_array, true). ";
		        ?>");
               	        fclose($myfile);

		}
		else
		{
			if(file_exists("storage/menu/main_menu_".$user_id.".php"))
                        {       
                                $file_name = "storage/menu/main_menu_".$user_id.".php";
                                require($file_name); 
                                $new_main_menu_array = [];
                                
                                foreach($moduleSequence as $menuname => $sequence)
                                        {
                                        foreach($main_menu_array as $array )
                                                {
                                                if( $menuname == $array['name'])
                                                        {
                                                        array_push($new_main_menu_array, $array);
                                                        }
                                                }
                                        } 
                                $myfile = fopen($file_name, "w") or die("Unable to open file!");
                                fwrite($myfile, "<?php
                        ".'$main_menu_array'." = " .var_export($new_main_menu_array, true). ";
                        ?>");
                                fclose($myfile);
                        
                        }
                        else    
                        {       
                                $file_name = "storage/menu/main_menu_".$user_id.".php";
                                $new_main_menu_array = [];
                                
                                require('storage/menu/default_main_menu.php');
                                foreach($moduleSequence as $menuname => $sequence)
                                        {
                                        foreach($main_menu_array as $array )
                                                {
                                                if( $menuname == $array['name'] ){
							if( (Settings_MenuManager_Module_Model::isPermittedModule($array['tabid'])) !== false)
                                                        {
                                                        array_push($new_main_menu_array, $array);
                                                        }
						}
                                                }
                                        } 
                                $myfile = fopen($file_name, "w") or die("Unable to open file!");
                                fwrite($myfile, "<?php
                        ".'$main_menu_array'." = " .var_export($new_main_menu_array, true). ";
                        ?>");
                                fclose($myfile);
                        }
		}

		$response = new Head_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}
}
?>
