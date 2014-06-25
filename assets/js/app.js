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

// Globals
var template;
var category_tpl;

/**
 * Handlebars Parsing
 *
 * @param string CSS id of a handlebars template :<script id="entry-template" type="text/x-handlebars-template"> 
 * @param object data key/value pairs
 */
/*
function parse_tpl(src,data) {
    var source   = jQuery('#'+src).html();
    //var template = Handlebars.compile(source);
    return template(data);    
}
*/
function draw_tab() {
    jQuery('#page_assets').html('');
    // JS Hashes do not preserve order. Thus the "Order" array
    var arrayLength = Order.length;
    for (var i = 0; i < arrayLength; i++) {
        var asset_id = Order[i];
        jQuery('#page_assets').append( template(Assets[asset_id]));
    }
    
    var arrayLength = Groups.length;
    for (var i = 0; i < arrayLength; i++) {
        if (Groups[i]) {
            jQuery('#asset_category_filters').append( category_tpl({"group": Groups[i]}));
        }
    }  

    jQuery("#page_assets").sortable({
        change: function( event, ui ) {
            console.log(ui);
        }
    });
    jQuery("#page_assets").disableSelection();


    // Filter page_assets
    // Clone page_assets items to get a second collection for Quicksand plugin (image gallery)
    var $portfolioClone = $("#page_assets").clone();
    
    // Attempt to call Quicksand on every click event handler
    jQuery("#asset_category_filters a").click(function(e){
        
        jQuery("#asset_category_filters li").removeClass("current");
        jQuery("#asset_category_filters li").removeClass("first"); 
        
        // Get the class attribute value of the clicked link
        var $filterClass = $(this).parent().attr("class");

        if ( $filterClass == "all" ) {
            var $filteredPortfolio = $portfolioClone.find("li");
        } else {
            var $filteredPortfolio = $portfolioClone.find("li[data-type~=" + $filterClass + "]");
        }
        
        // Call quicksand
        jQuery("#page_assets").quicksand( $filteredPortfolio, { 
            duration: 800, 
            easing: 'swing' 
        });


        jQuery(this).parent().addClass("current");

    })


}

function define_dialog_boxes() {
    // Edit Asset Form
    // The trick here is reading data out of the JS "Assets" var and into the form when it is displayed,
    // then from the form and back into the JS Assets var when the form is closed.
    jQuery( "#asset_edit_form" ).dialog({
        autoOpen: false,
        height: 600,
        width: 920,
        modal: true,
        closeOnEscape: true,        
        open: function(event, ui) {
            console.log('Open dialog');
            // Sent the asset_id when the link is clicked, e.g. via
            // onclick="javascript:jQuery('#asset_edit_form').data('asset_id', 123).dialog('open');"
            var asset_id = $("#asset_edit_form").data('asset_id');
            //console.log('opened...'+ asset_id);
            console.debug(Assets[asset_id]);
            // Write all values temporarily to the modal
            jQuery('#modal_asset_title').val(Assets[asset_id].title);
            jQuery('#modal_asset_alt').val(Assets[asset_id].alt);
            jQuery('#modal_asset_group').val(Assets[asset_id].group);
            jQuery('#modal_asset_width').text(Assets[asset_id].width);
            jQuery('#modal_asset_height').text(Assets[asset_id].height);
            if (Assets[asset_id].is_image) {
                jQuery('#modal_asset_img').html('<img src="'+Assets[asset_id].url+'" style="max-width:770px; height:auto;margin: 0 auto;display: block;"/>');
            }
            else {
                jQuery('#modal_asset_img').html(''); 
            }
            jQuery('#modal_asset_thumb').html('<img src="'+Assets[asset_id].thumbnail_url+'" style="max-width:500px; height:auto;"/>');
            if (Assets[asset_id].is_active == 1) {  
                jQuery('#modal_asset_is_active').prop('checked', true);
            }
        },
        buttons: {
            "Save": function() {
                // For meta-data specific to the *relation* (i.e. PageAsset), write the values back to the form (ugh)
                // For data specific to the *asset*, we have to fire off an Ajax request
                var asset_id = jQuery("#asset_edit_form").data('asset_id');
                var is_active = jQuery('#modal_asset_is_active:checked').length;
                // And back to the JSON (double-ouch)
                Assets[asset_id].title = jQuery('#modal_asset_title').val();
                Assets[asset_id].alt = jQuery('#modal_asset_alt').val();
                Assets[asset_id].group = jQuery('#modal_asset_group').val(); 
                Assets[asset_id].is_active = is_active;
                jQuery('#asset_is_active_'+asset_id).val(is_active);
                jQuery('#asset_group_'+asset_id).val(Assets[asset_id].group);
                jQuery('#asset_title_'+asset_id).html(Assets[asset_id].title);
                jQuery('#asset_group_vis_'+asset_id).html(Assets[asset_id].group);
                console.log('#asset_is_active_'+asset_id+ ' set to '+is_active);
                // This data here is specific to the Asset... be we can't post back everything:
                // url, thumbnail_url, path will specifically get messed up if posted here.
                // assapi('asset','edit',Assets[asset_id]); // <-- too much!
                assapi('asset','edit', {
                    asset_id: asset_id,
                    title: Assets[asset_id].title,
                    alt: Assets[asset_id].alt
                });
                
                // Update the groups and redraw the groups
                Groups.push(Assets[asset_id].group);
                Groups = array_unique(Groups);
                jQuery('#asset_category_filters').html('<li class="all first"><a href="#">All</a></li>');
                draw_tab();
                
                jQuery( this ).dialog( "close" );
            },
            "Cancel": function() {
                jQuery( this ).dialog( "close" );
            }
        }   
    });

    // Define Dropzone for Assets 
    // This does create an error on save: "Dropzone already attached." boo.
    var myDropzone = new Dropzone("div#asset_upload", {
        url: assman.controller_url+'&class=asset&method=create'
    });    
    // Refresh the list on success (append new tile to end)
    myDropzone.on("success", function(file,response) {
        response = jQuery.parseJSON(response);
        console.log('[Dropzone Success]', file, response);
        if (response.status == "success") {
            // Write data back to parent JS
            var asset_id = response.data.fields.asset_id;
            Assets[asset_id] = response.data.fields;
            Order.push(asset_id);
            draw_tab();
            jQuery(".dz-preview").remove();
       } 
       else {                           
            console.log('There was a problem with your image upload.');
            jQuery(".dz-success-mark").hide();
            jQuery(".dz-error-mark").show();
            show_error(response.data.msg);
       }
    });    
    myDropzone.on("error", function(file,errorMessage) {
        console.log('[Dropzone Error]',file, errorMessage);
    });


    // Drag Drop Item Delete
    $( "#trash-can" ).droppable({
            
        over: function( event, ui ) {
            $(this).addClass('over-trash');
        },
        out: function(event, ui) {
            var id = $(ui.draggable).attr('id');
            $(this).removeClass('over-trash');
        },
        drop: function( event, ui ) {
            var id = $(ui.draggable).attr('id');
            var asset_id = $(ui.draggable).find('img').data('asset_id');
            if (confirm("Are you Sure you want to Delete this Image?")) {
                $(this).removeClass('over-trash');
                var result = assapi('asset','delete', {asset_id: asset_id} );
                $('#'+id).hide();
            }
            $(this).removeClass('over-trash');
            return false;
        }

    });

}

/**
 * Draw our tab, formatting data using handlebarsjs
 *
 */
function page_init() {
    console.debug('[page_init]');
    inited = 1; // flag it as having been initialized
    
    var source   = jQuery('#page_asset_tpl').html();
    template = Handlebars.compile(source);
    category_tpl = Handlebars.compile(jQuery('#asset_group_tpl').html());
    
    draw_tab();
    define_dialog_boxes();
    
}



/**
 * Asset Manager API
 *
 * This is the primary function that drives our simple HTML client. This function
 * can dynamically load/replace parts of a page (sorta a "javascript include"), 
 * and it can approximate the effect of clicking on a standard <a> link, but it's 
 * all Ajax-REST based.
 *
 * @param string classname controller class to be requested for a JSON response
 * @param string methodname 
 * @param hash data any additional data to be included in the request to the controller 
 */
function assapi(classname,methodname,data,callback) {
    data = typeof data !== 'undefined' ? data : {}; // default
    
    console.debug('[assapi]',classname,methodname,data);
    
    // We need to set some POST data, otherwise routing will fail.
    data._assman = Math.random()*10000000000000000;
    // Ajax post
    var url = assman.controller_url+'&class='+classname+'&method='+methodname;
    jQuery.post(url, data, function( response ) {
        console.debug(response);
        if(response.status == 'fail') {
            console.log(response.data.errors);
            var msg = 'Error:<br/>';
            for(var fieldname in response.data.errors) {
                msg = msg + response.data.errors[fieldname] + '<br/>';
            }
            return show_error(msg); 
        }
        else if (response.status == 'success') {
            show_success(response.data.msg);
            if (callback != void 0) {
                callback(response);
            }
        }
    },'json')
    .fail(function() {
        console.error('[assapi] post to %s failed', url);
        return show_error('Request failed.');
    });
}

/**
 * Given an array, make all elements in it unique (like PHP function of the same name)
 * @param array
 * @return array
 */
function array_unique(a) {
    var temp = {};
    for (var i = 0; i < a.length; i++)
        temp[a[i]] = true;
    var r = [];
    for (var k in temp)
        r.push(k);
    return r;
}

/**
 * Show a simple error message, then fade it out and clear it so we can reuse the div.
 */
function show_error(msg) {
    jQuery('#assman_msg').html('<div class="danger">'+msg+'</div>');
}


/**
 * Show a success message, then fade it out and clear it so we can reuse the div.
 */
function show_success(msg) {
    jQuery('#assman_msg').html('<div class="success">'+msg+'</div>')
    .delay(3000).fadeOut(function() {
        jQuery(this).html('');
        jQuery(this).show(); 
    });
}
