<!-- 
The following script is a handlebarjs template.
Note: handlebars cannot use placeholders with periods (e.g. {{Asset.url}} fails)
-->
<script id="page_asset_tpl" type="text/x-handlebars-template" style="cursor:pointer;">
<li class="li_product_image" id="product-asset-{{asset_id}}">
	<div class="img-info-wrap" onclick="javascript:jQuery('#asset_edit_form').data('asset_id', '{{asset_id}}').dialog('open');">  
        <img src="{{thumbnail_url}}?rand=" alt="{{alt}}" width="{{thumbnail_width}}" height="{{thumbnail_height}}"/>
	    <input type="hidden" name="PageAssets[asset_id][]" value="{{asset_id}}"/>
        <input type="hidden" id="asset_is_active_{{asset_id}}" name="PageAssets[is_active][]" class="asset_is_active" value="1" />
	</div>
</li>
</script>

<div id="assets_tab" class="content">	
    <div class="dropzone-wrap" id="asset_upload">

    	<ul class="clearfix" id="page_assets">

        </ul>


    	<div class="dz-default dz-message"><span>Drop files here to upload</span></div>

         <div id="trash-can" class="drop-delete">
            <span>Drag Image Here to Delete</span>
        </div>

    </div>
    
	<!-- ======== ASSET MODAL DIALOG BOX ====== -->
	<div id="asset_edit_form" title="Edit Asset">
	
        <label for="modal_asset_title">Title</label>
        <input type="text" id="modal_asset_title" value="" />
        <label for="modal_asset_alt">Alt</label>
        <input type="text" id="modal_asset_alt" value="" />
        <label for="modal_asset_is_active">Is Active?</label>
        <input type="checkbox" id="modal_asset_is_active" value="1" />
        
        <label for="modal_asset_thumbnail_override">Manual Thumbnail Override</label>
        <input type="text" id="modal_asset_thumbnail_override" value="" placeholder="http://"/>
        
        <p>Full Dimensions: <span id="modal_asset_width"></span> x <span id="modal_asset_height"></span></p>
    
        <span id="modal_asset_thumb"></span>
        
        <span id="modal_asset_img"></span>
	</div>		

</div>