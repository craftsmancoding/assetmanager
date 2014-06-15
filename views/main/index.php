Asset Manager Here.

<button id="opener">open the dialog</button>
<div id="dialog" title="Dialog Title">I'm a dialog</div>
 
<script>
$( "#dialog" ).dialog({ autoOpen: false });
$( "#opener" ).click(function() {
  $( "#dialog" ).dialog( "open" );
});
</script>