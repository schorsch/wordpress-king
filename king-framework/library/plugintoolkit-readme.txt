Version 0.85

DESCRIPTION:
This Class lets you create plugin options for a WP-Plugin.
This is meant to save time coding the admin area of a plugin by rendering option fields for your plugin.
It provides CRUD features for Options and they are stored in an array which is saved to one row in the options table.
To keep the options table clean, there is a delete function which kills your options bevor you deactivate a plugin.
There are several field types you can use for your plugin variables which are explained in detail further on.

-------------
EXAMPLE:
The constructor has 5 variables, not all are always needed. following you see an example initializing call

plugintoolkit(
1.	'myPlugin',

2.	array(
	'setting1' => 'Setting One ## This is setting one.',
	'setting2' => 'Stuff {textarea|6|50} ## Enter some text',
	'setting3' => 'Choice {radio|choice1|Choice One|choice2|Choice Two} ## Chose one.',
	'setting4' => 'Multiple choice {checkbox|mc1|happy|Are you Happy ?|mc2|human|Are you Human ?} ## Chose any',
	'debug' => 'debug',
	'delete' => 'delete',
	),
3.	my-plugin-filename.php ,

4.	array(
		'parent' => 'options-general.php' ,
		'access_level' => 'administrator',
	),
5.	array(
		'newCoreFile' => 'edit-form-advanced.php' ,
		'coreFolder' => 'wp-admin',
		'newFolder' => 'wp-content/plugins/my_plugin/new_core_files',
	)
);


The class constructor take the following options

Options:
1. global plugin / class Name must be unique
------------------------------
------------------------------
2. array
	This array holds the the actual option fields.
	Each of those options must have a unique name
	each has a similar pattern that is

	'varName' => ' DescriptiveLabel {OptionType|Value or optionName or Variables} ## Descriptive text',

	Those are the different Options available
------------------------------
	radio button set

    What it does:
	renders an Option set of radio buttons

	How it looks:
	'setting3' => 'Choice {radio|choice1|Choice One|choice2|Choice Two} ## Chose one.',
	VarName   		Label	type| value	|Label							##description

	Example:
	'gender' => 'Gender {radio|girl|You are a female|boy|You are a male} ## What is your gender ?'
------------------------------
	checkbox

	What it does:
	Renders checkbox Option fields

    How it looks:
	'option_variable' => 'Option Title {checkbox|option_varname1|value1|Text1|option_varname2|value2|Text2} ## optional explanations',

------------------------------
	textbox (advanced)

	What it does:
	textbox with option for field size and max size

	How it looks:
	'option_variable' => 'Option Title {textbox|size|maxlength} ## optional explanations',

------------------------------
	textbox (simple)

	What it does:
	simple textbox with no options for length or max size

	How it looks:
	'option_variable' => 'Option Title ## optional explanations',

------------------------------
	textarea

	What it does:
	Renders a textarea whith options for rows and colums.

	How it looks:
	'option_variable' => '{textarea|rows|colums}',

------------------------------
	roleselect

	What it does:
	It shows a Dropdown select with all current roles in the system. The new capability will be assigned to the selected role on save.
	After selecting another role the cap gets taken away from the old role.
	On Plugin delete all caps will be killed from the roles. After install you have to save on time to assign the new caps

	How it looks:
	'option_variable' => 'Option Title {roleselect|newCapName} ## Give the selected role the new Capability',

------------------------------
	textbox2Array

	What it does: (What it should do)
	shows a textfield and transfers the inserted delimited text to an array

	How it looks:
	'option_variable' => 'Option Title {textbox2Array|size|maxlength|delimiter} ## optional explanations',


------------------------------
	hidden

	What it does:
	Renders a hidden field. the var name is the also the value.

	How it looks:
	'option_variable' => '{hidden}',

------------------------------
	placeholder

	What it does:
	Render a spacer between Option sets. The Option Title outputs as label next to it comes an hr

	How it looks:
	'option_variable' => 'Option Title{placeholder}',
------------------------------
	delete

	What it does:
	creates a delete button for killing the plugin options. The options-table row containing the options will be deleted
	Should definitly be used if you put in new core files, because the original corefiles will be copied back to their original place
	and the changed core file will be deleted.

	How it looks:
	'delete' => 'delete',

------------------------------
	debug

	What it does:
	shows debug informations about whats in your variables

	How it looks:
	'debug' => 'debug',


------------------------------
------------------------------
3. filename, must be unique is just used as a "placeholder" in the url of the backoffice
------------------------------
------------------------------
4.menu options
	parent menu for your plugin -> just look in the url of your menu´s in the backoffice
	access_level -> you can take an existing or your own capability. Make sure the Capability is assigned to at least you the admin
				i took f.ex. an pluginhandler plugin with some roleselects which introduce new cap´s to the selected role.
				this new cap is then added in the inherited plugin
------------------------------
------------------------------
5. core_file changes (I know might be critical!)
	i modded some corefiles and wrapped them with new functions into a plugin.
	Here you have the chance to have the core file copied from your plugindir into the WP core directories.
	This function will automaticly backup and restore your original core file!
	A Backupfolder is created in your corefile-folder f.ex.wp-admin/king-backup which contins the original files
	When upgrading WP you can delete your plugin with the delete Option set, upgrade WP and reactivate the plugin.
	This keeps the changes apart from wp but you might have to recode your corefile-changes again.
	Options:
	newCoreFile -> the name of the corefile
	corefolder -> the folder from your WP root where the corefile is located f.ex. "wp-admin" or "wp-includes"
	newFolder -> the folder from wp-root where your changed corefiles are located. f.ex "wp-content/wp-admin"
				the script will take your new files from there an copy them to the the wp location specified above


