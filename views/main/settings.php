<div class="assman_canvas_inner">
    <h2 class="assman_cmp_heading">Asset Manager Settings</h2>
</div>

<div class="assman_canvas_inner">

    <?php print (isset($data['msg'])) ? $data['msg'] : ''; ?>
    
    <form action="" method="post" id="assman_settings">

        <h3>Thumbnail Dimensions</h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>These settings affect the size of thumbnails displayed on the "Assets" tab.</p>
        <?php
        print \Formbuilder\Form::text('thumbnail_width', $data['thumbnail_width'], array('label' => 'Thumbnail Width'));
        ?>    
        <?php
        print \Formbuilder\Form::text('thumbnail_height', $data['thumbnail_height'], array('label' => 'Thumbnail Height'));
        ?>    
        </div>
    
        <h3>Resource Types</h3>

        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>The Asset Manager can be attached to any valid MODX Resource Class, e.g. <code>modWebLink</code>, <code>modSymLink</code>, <code>modStaticResource</code>, and <code>modDocument</code> (the default).  List these as a JSON array.</p>
        <?php
        print \Formbuilder\Form::text('class_keys', $data['class_keys'], array('label' => 'Class Keys','size'=>'200'));
        ?> 
        </div>
        
        <h3>Storage</h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Several settings relate to where the Asset Manager stores its files and how they are accessed.</p>
        <?php
        print \Formbuilder\Form::text('library_path', $data['library_path'], array('label' => 'Library Path','description'=>'Relative to the <code>MODX_ASSETS_URL</code>'));
        ?> 
        <?php
        print \Formbuilder\Form::checkbox('url_override', $data['url_override'], array('label' => 'URL Override'));
        ?>
        <p>Normally, asset URLS are calculated as <code>MODX_ASSET_URL</code> + <code>assman.library_path</code> + Asset stub.  If URL override is enabled, the URLS are calculated as <code>assman.site_url</code> + <code>assman.library_path</code> + Asset stub.</p>
        
        <?php
        print \Formbuilder\Form::text('site_url', $data['site_url'], array('label' => 'Override URL','description'=>'Used to calculate URLs When the URL Override is checked.'));
        ?>         
        
        
        </div>

        <h3>Content Types</h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>By default, the Asset Manager will detect the MIME types of uploaded files and create a <code>modContentType</code> object for the detected content type.  This behavior saves a lot of time, but you may disable if you need to customize the specifics of your upload types.</p>
        <?php
        print \Formbuilder\Form::checkbox('autocreate_content_type', $data['autocreate_content_type'], array('label' => 'Auto-create'));
        ?>    
        
        <br/>
        <input class="btn assman-btn" type="submit" value="Update Settings" />

    </form>

    <hr/>

    <a class="btn" href="<?php print MODX_MANAGER_URL; ?>?a=system/settings">See all Settings</a>

</div>