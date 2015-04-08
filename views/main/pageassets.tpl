<!-- 
The following script is a handlebarjs template.
Note: placeholders with periods map to specific nodes of the object (e.g. {{Asset.url}} fails unless your data has 
an Assets node with a url key)
-->
<script id="page_asset_tpl" type="text/x-handlebars-template">
<li class="li_page_image" data-id="{{asset_id}}" data-type="{{group}}" id="page-asset-{{asset_id}}" style="cursor:pointer;">
	<div class="img-info-wrap" onclick="javascript:jQuery('#asset_edit_form').data('asset_id', '{{asset_id}}').dialog('open');">  

        <img src="{{Asset.thumbnail_url}}" alt="{{Asset.alt}}" width="{{Asset.thumbnail_width}}" height="{{Asset.thumbnail_height}}" onclick="javascript:open_asset_modal('{{Asset.asset_id}}');" class="{{#unless is_active}}inactive{{/unless}}" style="cursor:pointer;"/>
	    <input type="hidden" name="PageAssets[asset_id][]" class="asset_id" value="{{asset_id}}" />
        <input type="hidden" id="asset_group_{{asset_id}}" name="PageAssets[group][]" value="{{group}}"/>
        <input type="hidden" id="asset_is_active_{{asset_id}}" name="PageAssets[is_active][]" class="asset_is_active" value="{{is_active}}" />
        <div class="img-info-inner">
            <p class="asset-id-ph"><span id="asset_title_{{asset_id}}">{{group}}</span> ({{asset_id}})</p>
            <p class="asset-title-ph" id="asset_group_vis_{{asset_id}}"><strong>{{Asset.basename}}</strong></p>
        </div>
	</div>
</li>
</script>


<script id="asset_group_tpl" type="text/x-handlebars-template">
    <li class="{{group}}"><a href="#">{{group}}</a></li>
</script>

<!-- !asset_modal_tpl -->
<script id="asset_modal_tpl" type="text/x-handlebars-template">
    <form id="asset_modal_form">
        <div id="asset_modal_form-inner">
        	<h3>Edit Asset ({{asset_id}})</h3>
        	
            <input type="hidden" name="asset_id" value="{{asset_id}}"/>
            <input type="hidden" name="Asset.asset_id" value="{{asset_id}}"/>
            
                <div class="asset-edit-inner">
                    
                    <div class="clearfix">
                        <div class="span70 pull-left">
                            <div class="row-input">
                                 <label class="row-lbl" for="modal_asset_title">Title</label>
                                 <input class="row-field" type="text" name="Asset.title" id="modal_asset_title" value="{{Asset.title}}" />
                            </div>
                           
                            
                            <div class="row-input">
                                 <label class="row-lbl" for="modal_asset_alt">Alt</label>
                                <input class="row-field" type="text" name="Asset.alt" id="modal_asset_alt" value="{{Asset.alt}}" />
                            </div>
        
                            <div class="row-input">
                                 <label class="row-lbl" for="modal_asset_group">Group</label>
                                 <select name="group" id="group-select">
                                    <option></option>
                                    {{#each Groups}}
                                        <option>{{this}}</option>
                                    {{/each}}
                                 </select>
                                <a href="{{manage_groups_url}}" class="btn">Manage Groups</a>
                            </div>
                            
                            <div class="row-input">
                                <label class="row-lbl" for="modal_asset_is_active">Is Active?</label>
                                <input type="hidden" name="is_active" value=""/>
                                <input class="row-field" type="checkbox" name="is_active" id="modal_asset_is_active" value="1" {{#if is_active}}checked="checked"{{/if}}/>
                            </div>
        
                            <!--div class="row-input">
                                 <label class="row-lbl" for="modal_asset_thumbnail_override">Thumbnail Override</label>
                                <input class="row-field" type="text" name="Asset.thumbnail_url" id="modal_asset_thumbnail_override" value="{{Asset.thumbnail_url}}" placeholder="http://"/>
                            </div-->
        
                        </div>
        
                        <div class="span20 pull-left">
                            <div class="row-input">
                                <span id="modal_asset_thumb"><img src="{{Asset.thumbnail_url}}" /></span>
                            </div>
        
                        </div>
                    </div>
                    
                    {{#if Asset.is_image}}
                        <div class="span100">
                            <div class="row-input">
                                <label class="row-lbl">Full Dimensions:</label> 
                                <div class="non-input"><span id="modal_asset_width">{{Asset.width}}</span> x <span id="modal_asset_height">{{Asset.height}}</span></div>
                            </div> 
            
                            <div class="row-input">
                                <span id="modal_asset_img"><img src="{{Asset.url}}" width="{{Asset.width}}" height="{{Asset.height}}" /></span>
                            </div>
            
                        </div>
                    {{/if}}

                </div>
        	</div>
        </div>
    	<div class="asset-modal-controls pull-right">
            <span class="btn" onclick="javascript:update_asset('asset_modal_form');">Save</span>
            <span class="btn" onclick="javascript:jQuery.colorbox.close();">Cancel</span>
            <span class="btn btn-danger pull-right" style="margin-right:30px;" onclick="javascript:jQuery('#delete_asset_modal').data('asset_id', '{{asset_id}}').dialog('open');">Delete</span>
        </div>

    </form>
</script>



<!-- ========================================= CONTENT ======================================== -->
<div id="assets_tab" class="content">	
    <div id="assman_msg"></div>
    <!--span class="btn pull-right" style="margin-top:-5px;" onclick="javascript:open_browse_assets_modal();">Browse Assets</span-->
    <ul id="asset_category_filters"> 
        <li class="all first"><a href="#">All</a></li> 
    </ul>


    <div class="dropzone-wrap clearfix" id="asset_upload">
        
        <div class="clear">&nbsp;</div>
    	<ul class="clearfix" id="page_assets"></ul>
    	
        <div class="dz-link-wrap clearfix">
            <div class="dz-label">Drop files here to upload or</div>
            <div class="dz-default dz-message"><span>Select Files</span></div>
        </div>

         <div class="dz-link-wrap clearfix fback-dz">
            <div class="dz-label">Sorry your Browser Doesnt Support Drag and Drop Upload!</div>
        </div>    	

    </div>

    <div id="trash-can" class="drop-delete">
        <span>Drag Image Here to Delete</span>
    </div>
    
    
    <div id="delete_asset_modal" title="Delete/Remove Asset">
        <p>This asset might be used by other pages!</p>
        <p>You can <strong>remove</strong> the image from this page,<br/>
        or you can <strong>delete</strong> the asset.</p>
        <p class="danger">Deleting cannot be undone!</p>
    </div>

    <div id="assman_footer">
    
    	<ul>
    		<li class="assman_nav_item"><a class="assman_bug_link" href="https://github.com/craftsmancoding/assetmanager/issues/new">Report a Bug</a></li>
    		<li class="assman_nav_item"><a class="assman_wiki_link" href="https://github.com/craftsmancoding/assetmanager/wiki">Wiki</a></li>
    		<li class="assman_nav_item"><a class="assman_support_link" href="http://craftsmancoding.com/contact">Get Support</a></li>
    		
    	</ul>
       
        <div id="assman_copyright">&copy; 2014 and beyond by <a href="http://craftsmancoding.com/">Craftsman Coding</a></div>
    </div>
        

</div>