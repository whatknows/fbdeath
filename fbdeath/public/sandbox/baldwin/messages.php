<?php
/*
	Epilogue Group

	advisor: Jed B.

	members: 
		Anita Marie G.
		Baldwin C.
		Nafiri K.
		Nithin J.

*/

	require_once "private/includes/header.php";

	if (!$epilogue->is_loggedin())
		die('You are not logged in.');


	if (isset($_POST['message']) && is_numeric($_POST['message_id'])) {
		$epilogue->save_message($user, $_POST['message_id'], $_POST['message']);
		$conf = g_msg('Message saved!');
	}
?>

<div class="container" style="margin-top: 100px">
	<?=$conf?>

	<div id="DUHCOMPOSINSPOT" class="well" style="display: none;">
			<div><form method="post"><button class="pull-right btn btn-primary" type="submit">Save</button>
									
									<input id="trustee" class="span2 typeahead" data-provide="typeahead" autocomplete="off" type="text" placeholder="Recipient" required>
									<input type="hidden" id="trusteeId" name="message_id" />
								
								<span class="type_loading" style="display: none">
									<i class="icon-spinner icon-spin"></i>
								</span></div>
			<textarea style="width:100%; height:170px;" name="message" placeholder="Type your message here..." required></textarea></form>

	</div>
	<div class="well">
		<h1><button class="pull-right btn btn-success" onclick="DISPERSONCOMPOSIN()"><i class="icon-pencil"></i> Compose</button><i class="icon-envelope"></i> Messages</h1>

<?php
#
# Hi jed, if you're reading this. dis person aint composing so....
#		i just do the add of duh first fifdee charamacterz.
#
#	thx.
#

$query = $sql->raw_query("SELECT * FROM `epi_messages` WHERE `user_id` = '". $epilogue->id ."'");
while ($msg = mysql_fetch_array($query)) {
	$recip = $user->graph_call_on_user($msg['recepient_id'], 'fields=name');
	echo 'To: '. $recip->name;
	echo '<blockquote>'. substr($msg['message_content'], 0, 50) .'...</blockquote>';
}



?>




	</div>

</div>


<script type="text/javascript">
function DISPERSONCOMPOSIN() {
	$('#DUHCOMPOSINSPOT').fadeIn(400);
}
		$(function(){

			var trusteeObjs = {};
			var trusteeNames = [];

			//get the data to populate the typeahead (plus an id value)
			var throttledRequest = _.debounce(function(query, process){
				//get the data to populate the typeahead (plus an id value)
				$.ajax({
					type: 'GET'
					,data: 'query='+ query
					,url: 'trustees.json'
					,cache: false
					,success: function(data){


						//reset these containers every time the user searches
						//because we're potentially getting entirely different results from the api
						trusteeObjs = {};
						trusteeNames = [];

						//Using underscore.js for a functional approach at looping over the returned data.
						_.each( data, function(item, ix, list){

							//for each iteration of this loop the "item" argument contains
							//1 trustee object from the array in our json, such as:
							// { "id":7, "name":"Pierce Brosnan" }

							//add the label to the display array
							trusteeNames.push( item.name );

							//also store a hashmap so that when bootstrap gives us the selected
							//name we can map that back to an id value
							trusteeObjs[ item.name ] = item;
						});

						//send the array of results to bootstrap for display
						process( trusteeNames );
					}
				});
			}, 300);


			$(".typeahead").typeahead({
				source: function ( query, process ) {

					$(".type_loading").show();
					//here we pass the query (search) and process callback arguments to the throttled function
					throttledRequest( query, process );


				}
        ,highlighter: function( item ){
          var trustee = trusteeObjs[ item ];
 					$(".type_loading").hide();         
          return '<div class="trustee" style="display: table-cell;">'
                +'<div style="height: 30px; width: 30px; background: url(\'' + trustee.photo + '\') top center; float: left; vertical-align: top;"></div>'
                +'<div style="vertical-align: top; display: table-cell; padding-left: 10px;"><strong>' + trustee.name + '</strong></div>'
                +'</div>';
        }
				, updater: function ( selectedName ) {
          
          //note that the "selectedName" has nothing to do with the markup provided
          //by the highlighter function. It corresponds to the array of names
          //that we sent from the source function.

					//save the id value into the hidden field
					$( "#trusteeId" ).val( trusteeObjs[ selectedName ].id );

					//return the string you want to go into the textbox (the name)
					return selectedName;
				}
			});
});
</script>

<?php
	require_once "private/includes/footer.php";
?>