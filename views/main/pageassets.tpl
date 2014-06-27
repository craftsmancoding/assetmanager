<!-- 
The following script is a handlebarjs template.
Note: handlebars cannot use placeholders with periods (e.g. {{Asset.url}} fails)
-->
<script id="page_asset_tpl" type="text/x-handlebars-template">
<li class="li_product_image" data-id="id-{{asset_id}}" data-type="{{group}}" id="product-asset-{{asset_id}}" style="cursor:pointer;">
	<div class="img-info-wrap" onclick="javascript:jQuery('#asset_edit_form').data('asset_id', '{{asset_id}}').dialog('open');">  
        <img src="{{thumbnail_url}}?rand=" data-asset_id="{{asset_id}}" alt="{{alt}}" width="{{thumbnail_width}}" height="{{thumbnail_height}}"/>
	    <input type="hidden" name="PageAssets[asset_id][]" value="{{asset_id}}"/>
        <input type="hidden" id="asset_group_{{asset_id}}" name="PageAssets[group][]" value="{{group}}"/>
        <input type="hidden" id="asset_is_active_{{asset_id}}" name="PageAssets[is_active][]" class="asset_is_active" value="1" />
        <div class="img-info-inner">
            <p class="asset-id-ph"><span id="asset_title_{{asset_id}}">{{title}}</span> ({{asset_id}})</p>
            <p class="asset-title-ph" id="asset_group_vis_{{asset_id}}">Group: <strong>{{group}}</strong></p>
        </div>
	</div>
</li>
</script>


<script id="asset_group_tpl" type="text/x-handlebars-template">
<li class="{{group}}"><a href="#">{{group}}</a></li>
</script>


<!-- ========================================= CONTENT ======================================== -->
<div id="assets_tab" class="content">	
    <div id="assman_msg"></div>

    <ul id="asset_category_filters"> 
        <li class="all first"><a href="#">All</a></li> 
    </ul>

    <div class="dropzone-wrap clearfix" id="asset_upload">

    	<ul class="clearfix" id="page_assets"></ul>
        <div class="dz-link-wrap clearfix">
            <div class="dz-label">Drop files here to upload or</div>
            <div class="dz-default dz-message"><span>Select Files</span></div>
        </div>
    	

    </div>

    <div id="trash-can" class="drop-delete">
        <span>Drag Image Here to Delete</span>
    </div>

    
	<!-- ======== ASSET MODAL DIALOG BOX ====== -->
	<div id="asset_edit_form" title="Edit Asset">
        <div id="assman_msg"></div>
        <div class="asset-edit-inner">
            
            <div class="clearfix">
                <div class="span70 pull-left">
                    <div class="row-input">
                         <label class="row-lbl" for="modal_asset_title">Title</label>
                         <input class="row-field" type="text" id="modal_asset_title" value="" />
                    </div>
                   
                    
                    <div class="row-input">
                         <label class="row-lbl" for="modal_asset_alt">Alt</label>
                        <input class="row-field" type="text" id="modal_asset_alt" value="" />
                    </div>

                    <div class="row-input">
                         <label class="row-lbl" for="modal_asset_group">Group</label>
                        <input class="row-field" type="text" id="modal_asset_group" value="" />
                    </div>
                   
                    
                    <div class="row-input">
                         <label class="row-lbl" for="modal_asset_is_active">Is Active?</label>
                        <input class="row-field" type="checkbox" id="modal_asset_is_active" value="1" />
                    </div>

                    <div class="row-input">
                         <label class="row-lbl" for="modal_asset_thumbnail_override">Thumbnail Override</label>
                        <input class="row-field" type="text" id="modal_asset_thumbnail_override" value="" placeholder="http://"/>
                    </div>

                </div>

                <div class="span20 pull-left">
                    <div class="row-input">
                        <span id="modal_asset_thumb"></span>
                    </div>

                </div>
            </div>
            
            <div class="span100">
                <div class="row-input">
                    <label class="row-lbl">Full Dimensions:</label> 
                    <div class="non-input"><span id="modal_asset_width"></span> x <span id="modal_asset_height"></span></div>
                </div> 

                <div class="row-input">
                    <span id="modal_asset_img"></span>
                </div>

            </div>
        </div>
	</div>		

</div>