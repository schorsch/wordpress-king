<?php
/*
Wordpress Plugin Toolkit 0.86

Helps Plugin authors set up admin menus and options.
Georg Leciejewski

TO DO :
- functionen im plugin sollen auch einen eigenen output machen k?nnen f?r backoffice und frontend?
leere function die nur den namen der aufrufenden function ?bergeben bekommt
- textbox2Array checken!
- hidden field erweitern?
- was soll nur im adminbereich ausgef?hrt werden?
- statt mit tabellen mit labels+divs arbeiten
- AJAX drag options intergrieren?
- options output aus dem switch rausnehmen und ne eigene klassen/functions bauen?
- check filepermissions before copy von neuen dateien!
- redirect nach dem l?schen mit nem js timer versehen oder die l?sch-msg an die pluginseite ?bergeben
- if schleifen zum check ob es corefiles gibt
- maybe upload option field

PROBLEMS
- copy function don?t check if files/dirs are writable and would need chmod options.
- file copying could be done via ftp
- in win server slash problem mit den dateipfaden, potentieller fix in class_dirtool.php
- wenn man nix ?ndert gibt es einen speicherfehler sollte in "you changed nothing"

CREDITS to:
Ozh from  http://planetOzh.com/ the core code came from his themetoolkit
enrico for pointing me to some array work

*/

if (!function_exists('plugintoolkit'))
{
	function plugintoolkit($plugin='',$array='',$file='',$menu='',$newFiles='')
	{
		global ${$plugin};
		//echo   $plugin."<br>";
		if ($plugin == '' or $array == '' or $file == ''or $menu=='')
		{
			die ('No plugin name, plugin option, parent or menue defined in your Plugin');
		}
		${$plugin} = new PluginToolkit($plugin,$array,$file,$menu,$newFiles);
	}
}

if (!class_exists('PluginToolkit'))
{
	/**
	*Class to handle some plugin management functions
	*@author ozh / georg leciejewski
	*@version 0.86
	*@package plugintoolkit
	*/
	class PluginToolkit
	{
		var $option, $infos,$newFiles;
		/**
		*@desc initialization Function
		*@param string $plugin  name of the plugin
		*@param array $array  field options
		*@param  $file  name of the plugin file
		*@param  $menu array with accesslevel+parent of the menu
		*@param  $newFiles array with infos about new coreFiles
		**/
		function PluginToolkit($plugin,$array,$file,$menu,$newFiles)
		{
			$this->newFiles=$newFiles;
			$this->infos['file'] = $file;

			if ($array['debug'])
			{
				if ($this->infos['file'] == $_GET['page'])
				{
					$this->infos['debug'] = 1;
					unset($array['debug']);
				}
			}
			if ($array['delete'])
			{
				$this->infos['showDelete'] = 1;
				unset($array['delete']);
			}
			$this->infos['plugin_shortname']=$plugin;
			$this->infos['menu_options'] = $array;
			$this->infos['show_admin_menu'] = $menu;
			$this->option=array();
			/* Read data from options table */
			$this->read_options();

			add_action('admin_menu', array(&$this, 'add_menu'));
		}

		/**
		*@desc Add an entry to the admin menu area		*
		**/
		function add_menu()
		{
			add_submenu_page($this->infos['show_admin_menu']['parent'], 'Options ' . $this->infos[plugin_shortname], $this->infos[plugin_shortname], $this->infos['show_admin_menu']['access_level'], $this->infos['file'], array(&$this,'admin_menu'));
		}

		/**
		*@desc Check if the Plugin has been loaded at least once (so that this file has been registered as a plugin)
		*
		* */
		function is_installed()
		{
			global $wpdb;
			$where = 'king-'.$this->infos['plugin_shortname'];
			$check = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name = '$where'");
			if ($check == 0)
			{
				return FALSE;
			} else
			{
				return TRUE;
			}
		}
		 /**
		 * @desc Plugin used for the first time (create blank entry in database)		*
		 */
		function do_firstinit()
		{
			global $wpdb;
			$msg='';
			$options = array();
			foreach(array_keys($this->option) as $key)
			{
				$options["$key"]='';
			}
			add_option('king-'.$this->infos['plugin_shortname'],$options, 'Options for Plugin '.$this->infos['plugin_shortname']);
			$msg= "Plugin options added in database (1 entry in table '". $wpdb->options ."')";
			// call to copy new files function $newFiles
			if(!empty($this->newFiles))
			{
				$this->copy_new_corefiles($this->newFiles);
			}
			return $msg;
		}
		/**
		*@desc  Read Plugin options as defined by user and populate the array $this->option
		*/
		function read_options()
		{
			$options = get_option('king-'.$this->infos['plugin_shortname']);
			$options['_________junk-entry________'] = 'nuttin in here';
			/* junk entry to populate the array with at least one value,
			 * removed afterwards, so that the foreach loop doesn't go moo. */
			$this->option = $this->read_option_array($options);
			array_pop($this->option);
			return $this->option;
		}
		/**
		*@desc  Read Array If option is an array recurse this function
		*@param array $options plugin options
		*@return the option to read_options.
		**/
		function read_option_array($options)
		{
			foreach ($options as $key=>$val)
			{
				if(is_array($val))
				{
					$list["$key"] =  $this->read_option_array($val);

				} else
				{
					$list["$key"] = stripslashes($val);
				}
			}
			return $list;
		}
		/**
		*@desc Write Plugin options as defined by user in database		*
		*@param array $options plugin options as array
		*@return message ok or not.
		**/
		function store_options($array)
		{
			global  $wp_roles;
			if(!empty($array[capability]))
			{  // if there is cap stuff

				foreach($array[capability] as $cap_key=>$cap_value)
				{
					list($role_old_key, $role_old_value) = each($array[role_old]);
					if(!empty($role_old_value))
					{
						$oldRole = get_role($role_old_value);
						$oldRole->remove_cap($cap_value);
						$msg.="Removed Capability: ".$cap_value." from Role: ".$role_old_value."<br />";
					}
					unset($role_old_key, $role_old_value, $cap_key, $cap_value, $oldRole);
				}
				unset($array[role_old]); // delete the old array don?t save to db
				//add new caps
				reset ($array[capability]);
				foreach($array[capability] as $cap_key=>$cap_value)
				{
					list($role_new_key, $role_new_value) = each($array[role]);
					$newRole = get_role($role_new_value);
					$newRole->add_cap($cap_value);
					$msg.="Added Capability: ".$cap_value." to Role: ".$role_new_value."<br />";
				}
				unset($role_new_key, $role_new_value, $cap_key, $cap_value, $newRole);
			}//end capability stuff

			//if there is a textbox 2 array
			if(!empty($array['array_Devider']))
			{
				$devider=implode($array['array_Devider']);

				foreach($array['textbox2Array'] as $key=>$value)
				{
					$array['textbox2Array'][$key] = explode($devider,$value);
				}
			}

			if (update_option('king-'.$this->infos['plugin_shortname'],$array))
			{
				return "Options successfully stored!<br />".$msg;
			}
			else
			{
				return "Could not save options !";
			}
		}// end function store_options

		/**
		*@desc Delete options from database. Also assigned capabilities are killed
		*
		**/
		function delete_options()
		{
			if(!empty($this->option[role])||!empty($this->option[capability]))
			{
				global  $wp_roles;
				foreach($this->option[capability] as $cap_key=>$cap_value)
				{
					list($role_key, $role_value) = each($this->option[role]);
					if(!empty($role_value))
					{
						$oldRole = get_role($role_value);
						$oldRole->remove_cap($cap_value);
					}
				}
			}
			delete_option('king-'.$this->infos['plugin_shortname']);
			if(!empty($this->newFiles))
			{
				$this->delete_plugin_corefiles($newFiles);
			}
		}
		/**
		*@desc The Admin Menu printing func
		*
		**/
		function admin_menu ()
		{
			global $wpdb;
			if (@$_POST['action'] == 'store_option')
			{
				unset($_POST['action']);
				$msg = $this->store_options($_POST);
			}
			elseif (@$_POST['action'] == 'delete_options')
			{
				$this->delete_options();
				//redirect to plugin manager currently taken out
			}
			elseif (!$this->is_installed())
			{
				$msg = $this->do_firstinit();
			}

			if (@$msg) print "<div class='updated'><p><b>" . $msg . "</b></p></div>\n";

			echo '<div class="wrap">';

			$check = $this->read_options();

			echo "<h2>Configure ".$this->infos['plugin_shortname']."</h2>";

			echo '<p>This plugin allows you to configure plugin variables , which are :</p>
			<form action="" method="post">
			<input type="hidden" name="action" value="store_option" />
			<table cellspacing="2" cellpadding="5" border="0" width=100% class="editform" summary="edit">';

			/* Print form */
			foreach ($this->infos['menu_options'] as $key=>$val)
			{
				$items='';
				preg_match('/\s*([^{#]*)\s*({([^}]*)})*\s*([#]*\s*(.*))/', $val, $matches);
				if ($matches[3])
				{
					$items = split("\|", $matches[3]);
				}

				print "<tr valign='top'><th scope='row' width='33%'>\n";
				if (@$items)
				{
					$type = array_shift($items);
					switch ($type)
					{
					case 'radio':
						print $matches[1]."</th>\n<td>";
						while ($items)
						{
							$v=array_shift($items);
							$t=array_shift($items);
							$checked='';
							if ($v == $this->option[$key]) $checked='checked';
							print "<label for='${key}${v}'><input type='radio' id='${key}${v}' name='$key' value='$v' $checked /> $t</label>";
							if (@$items) print "<br />\n";
						}
						break;
					case 'textarea':
						$rows=array_shift($items);
						$cols=array_shift($items);
						print "<label for='$key'>".$matches[1]."</label></th>\n<td>";
						print "<textarea name='$key' id='$key' rows='$rows' cols='$cols'>" . $this->option[$key] . "</textarea>\n";
						break;
					case 'checkbox':
						print $matches[1]."</th>\n<td>";
						while ($items)
						{
							$k=array_shift($items);
							$v=array_shift($items);
							$t=array_shift($items);
							$checked='';
							if ($v == $this->option[$k]) $checked='checked';
							print "<label for='${k}${v}'><input type='checkbox' id='${k}${v}' name='$k' value='$v' $checked /> $t</label>";
							if (@$items)
								print "<br />\n";
						}
						break;
					case 'textbox':
						$size=array_shift($items);
						$maxlength=array_shift($items);
						print "<label for='$key'>".$matches[1]."</label></th>\n<td>";
						print "<input type='text' name='$key' id='$key' size='$size' maxlength='$maxlength' value='" . $this->option[$key] . "' />\n";
						break;
					case 'placeholder':
						print $matches[1]."</th>\n<td>";
						print $this->option[$key]."<hr>";  // wird das gebraucht.. ist der name des platzhalters k?nte mann auch en hr einbauen?
						break;

					case 'hidden':
						print "<input type='hidden' name='$key' value='" . $this->option[$key] . "' />\n";
						//	print $this->option[$key];
						break;
					case 'textbox2Array':
						$size=array_shift($items);
						$maxlength=array_shift($items);
						$devider= array_shift($items);
						if(!empty($this->option[textbox2Array][$key]))
						{
						$values_separated = implode($this->option[array_Devider][$key],$this->option[textbox2Array][$key]);
						}
						print "<label for='$key'>".$matches[1]."</label></th>\n<td>";
						print "<input type='text' name='textbox2Array[$key]' id='$key' size='$size' maxlength='$maxlength' value='" .$values_separated. "' />\n";
						print "<input type='hidden' name='array_Devider[$key]' value='$devider' />\n"; //array_Devider[$key]
						break;
					case 'roleselect':
						$myCap=array_shift($items);
						global  $wp_roles;
						//build the role selector
						$role_select = "<select name='role[$key]'>\n";
						foreach($wp_roles->role_names as $role_key => $name)
						{
							$selected = ($this->option[role][$key] == $role_key) ? ' selected="selected"' : '';
							$role_select .= "<option value=\"$role_key\"{$selected}>{$name}</option>\n";
						}
						$role_select .= '</select>';
						print $matches[1]."</th><td>\n";
						print $role_select;
						//alte rolle
						print "<input type='hidden' name='role_old[$key]' value='".$this->option[role][$key]. "' />\n";
						//capability
						print "<input type='hidden' name='capability[$key]' value='$myCap' />\n";
						break;

					}//end type switch
				} else {
					print "<label for='$key'>".$matches[1]."</label></th><td>\n";
					print "<input type='text' name='$key' id='$key' value='" . $this->option[$key] . "' />\n";
				}
				if ($matches[5]) print '<br/>'. $matches[5];
				print "</td></tr>\n";
			}//end options
			echo '</table>
			<p class="submit"><input type="submit" value="Store Options" /></p>
			</form>';

			if ($this->infos['debug'] and $this->option)
			{
				$g = '<span style="color:#006600">';
				$b = '<span style="color:#0000CC">';
				$o = '<span style="color:#FF9900">';
				$r = '<span style="color:#CC0000">';
				echo '<h2>Programmer\'s corner</h2>';
				echo '<p>The array <em>$'. $this->infos['classname'] . '->option</em> is actually populated with the following keys and values :</p>
				<p><pre class="updated">';
				$count = 0;
				foreach ($this->option as $key=>$val)
				{
					$val=str_replace('<','&lt;',$val);
					if ($val)
					{
						print '<span class="ttkline">'.$g.'$'.$this->infos['classname'].'</span>'.$b.'-></span>'.$g.'option</span>'.$b.'[</span>'.$g.'\'</span>'.$r.$key.'</span>'.$g.'\'</span>'.$b.']</span>'.$g.' = "</span>'. $o.$val.'</span>'.$g."\"</span></span>\n";
						$count++;
					}
				}
				if (!$count)
					print "\n\n";
				echo '</pre><p>To disable this report, remove the line "&nbsp;<em>\'debug\' => \'debug\'</em>&nbsp;" in the array you edited at the beginning of this file.</p>';
			}//end Switch items

			if ($this->infos['showDelete'])
			{
				echo '<h2>Delete Plugin Options</h2>
				<p>Completely remove the Plugin Options from your database (reminder: they are all stored in a single entry, in<em>'. $wpdb->options. '</em>). You will be redirected to the <a href="plugins.php">Plugin admin interface</a> to deactivate the plugin.';

				echo '.</p>
				<p><strong>Special notice</strong><br/>
				Press "Delete" only if you intend to deactive + remove the plugin right after this.<br/>
				This is an Helper to keep your database clean, because WP does not have a decent hook to perform uninstall actions when deactivating a plugin. <br />If you set Role Options the added capabilities need to be removed from hand with the rolemanager!(to dynamic for me))
				<br /> If you press this button accidentially the default options(blank) will be reinstalled if you recall the this options page.
				</p>
				<form action="" method="post">
				<input type="hidden" name="action" value="delete_options" />
				<p class="submit"><input type="submit" value="Delete Options" onclick="return confirm(\'Are you really sure you want to kill the Options ?\');"/></p>
				</form>';
			}
			echo '</div>';
		} //end admin menu
		/**
		* @desc Copy new corefiles
		* @param corefile_name
		* @param directory nam of plugin
		*/
		function delete_plugin_corefiles($newFiles)
		{
			include_once('class-kingfiles.php');
			$msg='';
			$dir = new KingFiles();
			$dir->setCurrentDir(ABSPATH.$this->newFiles['coreFolder']);
			$existingdirs= $dir->getDirectories();
			//if backup dir is in place
			if(in_array("king-backup",$existingdirs))
			{
				//only delete original file if found backup
				if(file_exists($dir->getCurrentDirHandle."king-backup/".$this->newFiles['newCoreFile']))
				{
					$dir->deleteFile($this->newFiles['newCoreFile']);
					$msg.= "Pimped File: ".$this->newFiles['newCoreFile']." Deleted from ".$this->newFiles['coreFolder']."<br />";
					//switch directory to backup
					$dir->setCurrentDir(ABSPATH.$this->newFiles['coreFolder']."/king-backup");
					$dir->copyFile($this->newFiles['newCoreFile'],ABSPATH.$this->newFiles['coreFolder']);//needed name of corefile
					$msg.="Original Version of ". $this->newFiles['newCoreFile']." copied back to: \"".$this->newFiles['coreFolder']."\"<br />";
					$dir->deleteFile($this->newFiles['newCoreFile']);
					$msg.="Original ". $this->newFiles['newCoreFile']." was deleted from Backup Folder in \"".$this->newFiles['coreFolder']."/king-backup\"<br />Did you had Problems with our King Plugin?";
				}else
				{
					$msg.=$this->newFiles['newCoreFile']." not found in Backup Folder in \"".$this->newFiles['coreFolder']."/king-backup\"<br />";
				}
			}else
			{
				$msg.="Directory \"king-backup\" not found in: \"".$this->newFiles['coreFolder']."\" Could not copy back original version! Did you deleted it? It was not my fault!<br />";
			}
			if (@$msg)
				print "<div class='updated'><p><b>" . $msg . "</b></p></div>\n";
		}// end delete pimped king file

		/**
		* @desc Copy new corefiles
		* @param $newFiles Array with infos about new files
		* @param directory nam of plugin
		*/
		function copy_new_corefiles($newFiles)
		{
			include_once('class-kingfiles.php');
			$msg='';
			$dir = new KingFiles();
			//Start perform Backup
			$dir->setCurrentDir(ABSPATH.$this->newFiles['coreFolder']);
			$existingdirs= $dir->getDirectories();
			if(!in_array("king-backup",$existingdirs))
			{
				$dir->createDir("king-backup");
				$msg="Directory \"king-backup\" in: \"".$this->newFiles['coreFolder']."\" created<br />";
			}else
			{
				$msg.="Directory \"king-backup\" in: \"".$this->newFiles['coreFolder']."\" already existing<br />";
			}
			//Start backup file
			$existingfiles = $dir->getFiles();
			//if the orig file exists
			if(in_array($this->newFiles['newCoreFile'],$existingfiles))
			{
				// aditional check if file already was backed to not overwrite core backup
				if(file_exists($dir->getCurrentDirHandle."king-backup/".$this->newFiles['newCoreFile']))
				{
					$msg.= "File: ".$this->newFiles['newCoreFile']." exists in Backup Directory. I am not willing to copy it again!<br />";
				}else
				{
					$dir->copyFile($this->newFiles['newCoreFile'],"king-backup");
					$msg.="Original Version of \"".$this->newFiles['newCoreFile']."\" was copied to \"".$this->newFiles['coreFolder']."/king-backup\"<br />";
				}
			//delete file maybe implement a fallback if the next copy dies not work out right!
			$dir->deleteFile($this->newFiles['newCoreFile']);
			$msg.="Original File:". $this->newFiles['newCoreFile']." was killed from your Harddrive <br />... Now you got a Problem! But wait..<br />";
			}else
			{
				$msg.="File: ".$this->newFiles['newCoreFile']." was already deleted in Folder: ".$this->newFiles['coreFolder']." <br />";
			}

			// put new file
			$dir->setCurrentDir(ABSPATH.$this->newFiles['newFolder']);
			$msg.="Fetching new File ".$this->newFiles['newCoreFile']." from ".$this->newFiles['newFolder']."<br />";
			$dir->copyFile($this->newFiles['newCoreFile'],ABSPATH.$this->newFiles['coreFolder']);//needed name of corefile
			$msg.="Highly pimped King-Version of \"". $this->newFiles['newCoreFile']."\" was copied to: \"".$this->newFiles['coreFolder']."\"<br />Have Fun with our King Product!";

			if (@$msg)
				print "<div class='updated'><p><b>" . $msg . "</b></p></div>\n";
		}// end copy_new_corefiles

	} // end class
}// end if class exists
?>
