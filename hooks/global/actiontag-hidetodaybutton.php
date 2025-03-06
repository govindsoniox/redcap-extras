<?php
	
/**
	This is a hook utility function that works as an action tag that will 
        hide the [Today] or [Now] button for date/datetime fields
	tagged with @HIDETODAYBUTTON or @HIDENOWBUTTON in the Field Annotation.

	Luke Stevens, Murdoch Childrens Research Institute, to work with hooks framework by 
	Andy Martin
	Stanford University
	https://github.com/123andy/redcap-hook-framework

	
	Install this file within the hooks resources directory e.g. as 
	hooks/resources/actiontag_hidetodaybutton.php

	To use actiontag_hidetodaybutton as a global hook include the following code
	block in server/global/global_hooks.php

	// INCLUDE @HIDETODAYBUTTON action tag
	$file = HOOK_PATH_FRAMEWORK . "resources/actiontag_hidetodaybutton.php";
	if (file_exists($file)) {
		include_once $file;
	} else {
		hook_log ("Unable to include $file for project $project_id while in " . __FILE__);
	}
	// INCLUDE @HIDETODAYBUTTON action tag


	OR, to use actiontag_hidetodaybutton as a project-level hook for project X include 
	the following code block in server/pidX/custom_hooks.php

	if ($hook_event == 'redcap_data_entry_form' || $hook_event == 'redcap_survey_page') {

		// INCLUDE other hook function scripts here
		// ...
		   
		// INCLUDE @HDIETODAYBUTTON action tag
		$file = HOOK_PATH_FRAMEWORK . "resources/actiontag_hidetodaybutton.php";
		if (file_exists($file)) {
			include_once $file;
		} else {
			hook_log ("Unable to include $file for project $project_id while in " . __FILE__);
		}
	}

**/

$term = '@HIDETODAYBUTTON @HIDENOWBUTTON';
hook_log("Starting $term for project $project_id", "DEBUG");

///////////////////////////////
//	Enable hook_functions and hook_fields for this plugin (if not already done)
if (!isset($hook_functions)) {
	$file = HOOK_PATH_FRAMEWORK . 'resources/init_hook_functions.php';
	if (file_exists($file)) {
		include_once $file;
		
		// Verify it has been loaded
		if (!isset($hook_functions)) { hook_log("ERROR: Unable to load required init_hook_functions."); return; }
	} else {
		hook_log ("ERROR: In Hooks - unable to include required file $file while in " . __FILE__);
	}
}

// See if the term defined in this hook is used on this page
if (!isset($hook_functions['@HIDETODAYBUTTON']) && !isset($hook_functions['@HIDENOWBUTTON'])) {
	hook_log ("Skipping $term on $instrument of $project_id - not used.", "DEBUG");
	return;
}
//////////////////////////////


# Step 1 - Create array of fields to inject
$startup_vars = array();
foreach($hook_functions['@HIDETODAYBUTTON'] as $field => $details) {
	$startup_vars[] = $field;
}
foreach($hook_functions['@HIDENOWBUTTON'] as $field => $details) {
	$startup_vars[] = $field;
}
?>

<script type='text/javascript'>
$(document).ready(function() {
	var dtFields = <?php print json_encode($startup_vars); ?>;

        // Loop through each field_name
	$(dtFields).each(function(i, field_name) {
		//console.log('i: ' + i);console.log(field_name);
                
		// Find the date/datetime field's input 
		var thisInput = $('input:text[name="' + field_name + '"]');
                // Find its sibling button and hide it
                $(thisInput).siblings('button').hide();
	});
});
</script>
