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


/**
 * Handlebars Parsing
 *
 * @param string CSS id of a handlebars template :<script id="entry-template" type="text/x-handlebars-template"> 
 * @param object data key/value pairs
 */
function parse_tpl(src,data) {
    console.log('[parse_tpl] src tpl id: '+src);
    var source   = jQuery('#'+src).html();
    console.log(source);
    var template = Handlebars.compile(source);
    return template(data);    
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
                //mapi('asset','edit',{"asset_id":asset_id,"title":title,"alt":alt});
                
                jQuery( this ).dialog( "close" );
            },
            "Cancel": function() {
                jQuery( this ).dialog( "close" );
            }
        }   
    });
    

    // Define Dropzone for Assets 
    var myDropzone = new Dropzone("div#asset_upload", {url: assman.controller_url+'&class=asset&method=create'});    
    // Refresh the list on success (append new tile to end)
    myDropzone.on("success", function(file,response) {

        response = jQuery.parseJSON(response);
        console.log('[Dropzone Success]', file, response);
//        console.log(response);
        if (response.status == "success") {
            console.log('success here...');
            var data = parse_tpl("page_asset_tpl",response.data.fields);
            jQuery("#page_assets").append(data);
            jQuery(".dz-preview").remove();
       } 
       else {                           
            console.log('problem here...');
            $(".dz-success-mark").hide();
            $(".dz-error-mark").show();
            $(".moxy-msg").show();
            $("#moxy-result").html("Failed");
            $("#moxy-result-msg").html(response.data.msg);
            $(".moxy-msg").delay(3200).fadeOut(400);
       }
    });    
    myDropzone.on("error", function(file,errorMessage) {
        console.log('[Dropzone Error]',file, errorMessage);
    });

}


