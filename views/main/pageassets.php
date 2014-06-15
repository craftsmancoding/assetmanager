<script id="page_asset_tpl" type="text/x-handlebars-template">
<li class="li_product_image" id="product-asset-{{asset_id}}">
	<div class="img-info-wrap">  
        <img src="{{thumbnail_url}}?rand=<?php print uniqid(); ?>" alt="{{alt}}" width="" />
	    <input type="hidden" name="Assets[asset_id][]" value="{{asset_id}}" onclick="javascript:jQuery('#asset_edit_form').data('asset_id', '{{asset_id}}').dialog('open');" style="cursor:pointer;"/>
        <input type="hidden" id="asset_asset_id_{{asset_id}}" name="Assets[asset_id][]" class="asset_asset_id" value="{{asset_id}}" />
	</div>
</li>
</script>

<div id="assets_tab" class="content">	
    <div class="dropzone-wrap" id="asset_upload">

    	<ul class="clearfix" id="page_assets">
            <?php 
            foreach ($data['page_assets'] as $a): ?>
                <li class="li_page_image" id="page-asset-<?php print $a->get('asset_id'); ?>">
                	<div class="img-info-wrap">
                		  <img src="<?php print $a->Asset->get('thumbnail_url'); ?>?rand=<?php print uniqid(); ?>" alt="<?php print $a->Asset->get('alt'); ?>" width="" onclick="javascript:jQuery('#asset_edit_form').data('asset_id', <?php print $a->get('asset_id'); ?>).dialog('open');" style="cursor:pointer;"/>
                	    <input type="hidden" id="asset_asset_id_<?php print $a->get('asset_id'); ?>" class="asset_asset_id" name="Assets[asset_id][]" value="<?php print $a->get('asset_id'); ?>" />
                	    <!-- Button trigger modal -->
                		
                		<!-- Modal-->
                	</div>
                </li>            
            
            <?php endforeach; ?>
        </ul>


    	<div class="dz-default dz-message"><span>Drop files here to upload</span></div>

         <div id="trash-can" class="drop-delete">
            <span>Drag Image Here to Delete</span>
        </div>

    </div>
    
	<?php /* ======== ASSET MODAL DIALOG BOX ======*/ ?>
	<div id="asset_edit_form" title="Edit Asset">
        <div id="asset_being_edited"></div>
        <label for="modal_asset_title">Title</label>
        <input type="text" id="modal_asset_title" value="" />
        <label for="modal_asset_alt">Alt</label>
        <input type="text" id="modal_asset_alt" value="" />
        <label for="modal_asset_is_active">Is Active?</label>
        <input type="checkbox" id="modal_asset_is_active" value="1" /> Is Active?
        <p>Dimensions: <span id="modal_asset_width"></span> x <span id="modal_asset_height"></span></p>
        <span id="modal_asset_img"></span>
	</div>		


	<div class="modal fade" id="update-image">
        <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Update Image</h4>

                <?php
                // This spinner image shows while the image is being loaded from ajax.
                ?>
                <div class="loader-ajax">
                    <img src="<?php print $this->config['assets_url']; ?>images/gif-load.gif" alt="">
                </div>
                
              </div>

              <div class="update-container"></div>
             
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!--/.modal -->

</div>