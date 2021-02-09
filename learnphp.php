<?php

include 'header.php';
include 'forms/functions.php';
/*********************************************************************************************************
  What This Page Does:

  link: http://www.oninapps.com/ [PAGE PATH GOES HERE]
  general description: This page [SHORT DESCRIPTION GOES HERE]

  page flow:

  Use this so we can see how many pages we have started refactoring: %%$$refactorproject$$%%

*********************************************************************************************************/

/*********************************************************************************************************
  General To Do List:

   - fill out general description above
   - fill in first debug line
   - Add PHP error reporting at the top of the page
   - Make sure the session start is checking to see if a session is already started
   - Add debug variable output to bottom of page (or appropriate place)
   - strip trailing whitespace
   - Format
   - Finish up the page explanation
   - Make sure page access/security is set up correctly and remember that PHP header relocation will not work if anything has already been printed to the page
   - Make sure includes files use the new ROOT_DIR or INC_DIR from the config.php file
   - Make sure the dbcon.php file is called with "require_once" rather than "include"
   - Make sure this is set above the call to the dbcon.php file: $oldcon = 'no';
   - Queries: remove to their own line and then set that to print in debug mode   
   - Queries: use double quotes and not the dot thing
   - Queries: make sure we're checking to see if there are rows returned before trying to loop through them
   - Make folders and filenames lower case wherever possible
   - Duplicate code
   - Functions (search for %%**%% to find places already identified as being 'functionable')
   - Separate page access
   - Separate CSS
   - Separate PHP
   - Separate Javascript
   - Separate HTML
   - wherever possible, put ID numbers where needed (either in code, on page, or easter eggs for super users)
   - For user-inputted data - make sure it's safe and formatted correctly (uppercase/lowercase, etc)
   - if it's on this page remove "doo doo baby" as an error message, smh

  For This Page
   -

*********************************************************************************************************/

  // turn on error reporting on local
  $subdomain = join('.', explode('.', $_SERVER['HTTP_HOST'], -2));
  if ($subdomain != "www" && $subdomain != "staging" && $subdomain != "vmsstaging" && $subdomain != "master" && $subdomain != "") {
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    $debug = "y"; // toggle as needed
  } else {
    $debug = "no"; // always leave this as no so that debug won't show on production    
  }

  $debugOutput = "";
  if ($debug == "y") {
    $debugOutput .= "LINE : Debug turned on for [ENTER PAGE AND PATH HERE] <br>";
    echo "DEBUG OUTPUT FOR vms_wfs/header.php: $debugOutput"; // copy this code block and move to where you want to output the debug for this page (then delete this line from the first code block and delete the other line from the duplicated code block)
  }
$reqid = getvalue('reqid', 0);

$groupID = getvalue('groupid', 19);
$reqid = getvalue('reqid', 0);
$gid = getGID();


if (isset($_POST['act']) && $_POST['act'] == 'deny') {

	// Update the req entry
	$mysql = 'UPDATE vms_requisition SET status="Denied" WHERE reqID='.$reqid;
	$mysqli->query( $mysql );
	
	// And let's insert into the approvers
	$mysql = 'INSERT INTO vms_requisition_approvers (reqID, userID, roleID, apprDate, status) VALUES ('.$reqid.', '.$_SESSION['id'].', '.$roleID.', NOW(), "Denied")';
	$mysqli->query( $mysql );
	
	// Redirect
	header('Location: requisitions.php'); // use the php redirect here rather than printing something that then has to be parsed with js
}

$positionTitle = "";
$PLDept = "";
if ($reqid != "") {
	$reqinfo = $mysqli->query( 'SELECT * FROM vms_requisition WHERE reqID='.$reqid )->fetch_array();
	$positionTitle = $reqinfo['position'];
	$PLDept = $reqinfo['deptID'];
}

$modappr = 0;
if (array_key_exists('modappr', $_GET)) { $modappr = $_GET['modappr']; }

?>

<!-- *** start vms_client/requisition_form.php -->
<div class="desktop-only">

	<?php
		if ($modappr == 1)
			{ print_breadcrumb ('actlog.php', 'Activities'); }
		else
			{ print_breadcrumb ('requisitions.php', 'Requisitions'); }
	?>

	<div class="desktop-content">
		<div class="mainContent-card">
		
			<h1 class="mainContent-card-title">	<?php
				if ($reqid == '') { 
					print 'New Requisition'; 
				}	else { 
					print 'Modify Requisition'; 
				}
			?></h1>
			<form id="form_test" method="post">
				<input type="hidden" name="reqid" value="<?php print $reqid; ?>">
				<input type="hidden" name="resub" value="<?php if (isset($_GET['resub'])) { print $_GET['resub']; } ?>">
				<input type="hidden" name="act" value="save">
				<div class="column">
				
					<div id="positionTitle_wrap" class="form-field-wrap form-width-400" data-column="1">
						<label for="positionTitle" class="label-text">Position Title</label>
						<div><input type="text" name="positionTitle" id="positionTitle" placeholder="" value="<?php print $positionTitle; ?>" data-lpignore="true"></div>
						<div id="helper_positionTitle" class="helper"></div>
						<div id="error_positionTitle" class="error"></div>
					</div>
					
					<div id="PLDept_wrap" class="form-field-wrap" data-column="1">
						<label for="PLDept" class="label-text">PL / Dept</label>
						<div><select name="PLDept" id="PLDept">
								<option disabled selected value> select an option </option><?php
							// Companies
							$mysql = 'SELECT T1.CompID, T2.nameComp FROM pc_users_access AS T1 JOIN pc_comp_profile AS T2 ON T1.CompID=T2.CompID WHERE T1.UserID='.$_SESSION['id'];
							$optlist = $mysqli->query( $mysql );
							while ($optitem = $optlist->fetch_array()) {
								if ($PLDept == 'c'.$optitem['CompID']) { $selt='selected'; } else { $selt=''; }
								print '<option value="c'.$optitem['CompID'].'" '. $selt .'>'.$optitem['nameComp'].'</option>';
							}
							// Sub Groups
							$mysql = 'SELECT T1.SubCID, T2.SubName FROM pc_users_access AS T1 JOIN pc_sub_component AS T2 ON T1.SubCID=T2.SubID WHERE T1.UserID='.$_SESSION['id'];
							$optlist = $mysqli->query( $mysql );
							while ($optitem = $optlist->fetch_array()) {
								if ($PLDept == 's'.$optitem['SubCID']) { $selt='selected'; } else { $selt=''; }
								print '<option value="s'.$optitem['SubCID'].'" '. $selt .'>~'.$optitem['SubName'].'</option>';
							}
						?></select></div>
						<div id="error_PLDept" class="error"></div>
					</div>
				
					<?php
						$error_func_list = "error += check_for_entry('positionTitle', 'You must enter a position title');\n";
						$error_func_list .= "error += check_selected_entry('PLDept', 'You must select a PL / Dept');\n";
					
						$mysql = 'SELECT T1.*, T2.inputFunction, T2.validation, T3.value 
									FROM vms_form_fields AS T1 
									JOIN vms_form_field_types AS T2 ON T1.fieldTypeID=T2.ID 
									LEFT JOIN vms_requisition_data AS T3 ON T1.ID=T3.fieldID AND T3.reqID='.$reqid.'
									WHERE groupID='.$gid.' AND roleID<='.$roleID.' AND formID="reqForm" ORDER BY T1.column ASC, T1.sortOrder ASC';

						print_form_fields($mysql);
						$error_func_list .= print_error_func_list($mysql);

					?>
				
					<div class="form-field-wrap">
						<div><label>Requisition Notes</label></div>
						<div><textarea name="req_notes"><?php if (isset($_POST['req_notes'])) { print $_POST['req_notes']; } ?></textarea></div>
					</div>
							
					<div class="button-group button_wrap">
						<div>
							<a href="requisitions.php" class="needhand cancel" class="close">Cancel</a>
						</div>				
						<div>
							<div type="submit" onclick="javascript:$('#form_test').submit();" class="needhand form-button"><?php
								if ($reqid == '')
									{ print 'Submit'; }
								else if ($modappr == 1)
									{ print 'Approve'; }
								else
									{ print 'Save'; }
							?></div>
						</div>
					</div>
				</div> <!-- this closes the last column -->
				
			</form>		
		</div>
	</div>
	
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div id="add_form" class="modalContent">
				<div class="modal-body">
					<div class="inner" style="line-height:22px;">
						<div id="modalContent_success" class="modalContent" style="display:none; text-align:center;">
							<div class="modal-message-header">SUCCESS!</div>
							<div class="modal-message-text"><?php
								if ($modappr == 1)
									{ print 'This requisition has been modified'; }
								else
									{ print 'Your requisition has been submitted for approval'; }
							?></div>
							<div type="submit" onclick="$('#myModal').modal('hide');" class="needhand form-button" style="width: 60px; margin: 0 auto;">OK</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<script>

	$('#myModal').on('hidden.bs.modal', function () {
	<?php
		if ($modappr == 1) {
			print 'window.location.replace("act_detail.php?reqid='.$_GET['reqid'].'");';
		} else {
			print 'window.location.replace("requisitions.php");';
		}
	?>
	})
	
	$('#form_test').submit(function(event){
		validate($(this)); // in section_form_emp_add
		event.preventDefault();
	});

	$(document).ready(function(){
		// this function shows/hides reason div based off of users selection to the additional position dropdown.  If a user chooses yes, show reason. Else, hide the div
		$("#field_9").change(function(){
			var optval = $("#field_9 option:selected").val();
			
			if (optval == "Additional position") {
				$("#field_10_wrap").show();
			} else {
				$("#field_10_wrap").hide();
			}
			
			if (optval == "Replacing a position") {
				$("#field_13_wrap").show();
				$("#field_14_wrap").show();
			} else{
				$("#field_13_wrap").hide();
				$("#field_14_wrap").hide();
			}
		}).change();

	});

	function validate(formdata) {
		error = 0;
		<?php print $error_func_list; ?>
		
		if (error == 0) {
			// Hide the buttons
			$('.button_wrap').hide();
			
			// Prep the form data
			var send_data = "",
			i = 0;
			$.each(formdata.serializeArray(), function (index, value) {
				if (i > 0) {
					send_data += ', ';
				}
				send_data += '"' + decodeURI(value.name).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '":';
				send_data += '"' + decodeURI(value.value).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"';
				i++;
			});
			var postdata = JSON.parse('{' + send_data + '}');
			console.log(postdata);
			
			// Ajax call to save the data
			$.ajax({
				url: "requisition_save.php",
				type: "POST",
				data: postdata,
				cache: false,
				success: function (response) {
					// Display the success modal
					console.log(response);
					$('#modalContent_success').show();
					$('#myModal').modal('show');
				}
			});
			
		}
	}
	
	function denial() {
		$('#deny_form').submit();
	}

</script>

<?php
print "<!-- *** end vms_client/requisition_form.php -->";

include 'footer.php';

?>




