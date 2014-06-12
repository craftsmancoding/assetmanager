/**
 * This is the javascript that supports our thin HTML "client" to help it 
 * interface with our REST API.
 *
 *
 * @package assman
 */
 
if (typeof jQuery == 'undefined') {
    alert('jQuery is not loaded. Assman HTML client cannot load.');
}
else {
    console.debug('[assman html client]: jQuery loaded.');
}




function page_init() {
    console.debug('[page_init]');
    jQuery(".ui-sortable").sortable();

    // Edit Asset Form
    jQuery( "#asset_edit_form" ).dialog({
        autoOpen: false,
        height: 600,
        width: 800,
        modal: true,
        closeOnEscape: true,        
        open: function(event, ui) {
            // Sent the asset_id when the link is clicked, e.g. via
            // onclick="javascript:jQuery('#asset_edit_form').data('asset_id', 123).dialog('open');"
            var asset_id = $("#asset_edit_form").data('asset_id')
            //console.log('opened...'+ asset_id);
            console.debug(product.RelData.Asset[asset_id]);
            // Write all values temporarily to the modal
            jQuery('#modal_asset_title').val(product.RelData.Asset[asset_id].title);
            jQuery('#modal_asset_alt').val(product.RelData.Asset[asset_id].alt);
            jQuery('#modal_asset_width').text(product.RelData.Asset[asset_id].width);
            jQuery('#modal_asset_height').text(product.RelData.Asset[asset_id].height);
            jQuery('#modal_asset_img').html('<img src="'+product.RelData.Asset[asset_id].url+'" style="max-width:770px; height:auto;"/>');
            if (product.RelData.Asset[asset_id].is_active == 1) {  
                jQuery('#modal_asset_is_active').prop('checked', true);
            }
        },
        buttons: {
            "Save": function() {
                // For meta-data specific to the *relation* (i.e. ProductAsset), write the values back to the form (ugh)
                // For data specific to the *asset*, we have to fire off an Ajax request
                var asset_id = $("#asset_edit_form").data('asset_id');
                var title = jQuery('#modal_asset_title').val();
                var alt = jQuery('#modal_asset_alt').val();
                var is_active = jQuery('#modal_asset_is_active').val();
                
                // And back to the JSON (double-ouch)
                product.RelData.Asset[asset_id].title = title;
                product.RelData.Asset[asset_id].alt = alt;
                product.RelData.Asset[asset_id].is_active = is_active;
                jQuery('#asset_is_active_'+asset_id).val(is_active);

                // This data here is specific to the Asset
                mapi('asset','edit',{"asset_id":asset_id,"title":title,"alt":alt});
                
                $( this ).dialog( "close" );
            },
            "Cancel": function() {
                $( this ).dialog( "close" );
            }
        }   
    });

}


