<div class="assman_canvas_inner">
    <h2 class="assman_cmp_heading"><?php print $data['pagetitle']; ?></h2>
</div>

<div class="assman_canvas_inner">

    <?php print (isset($data['msg'])) ? $data['msg'] : ''; ?>
    
    <form action="" method="post" id="assman_settings">


        <h3><?php print $data['settings.thumbnail.title']; ?></h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $data['settings.thumbnail.desc']; ?></p>
        <?php
        print \Formbuilder\Form::text('thumbnail_width', $data['thumbnail_width'], array('label' => $data['lbl.thumbwidth']));
        ?>    
        <?php
        print \Formbuilder\Form::text('thumbnail_height', $data['thumbnail_height'], array('label' => $data['lbl.thumbheight']));
        ?>    
        </div>
    
        <h3><?php print $data['settings.resourcetype.title']; ?></h3>

        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $data['settings.resourcetype.desc']; ?></p>
        <?php
        print \Formbuilder\Form::text('class_keys', $data['class_keys'], array('label' => $data['lbl.classkeys'],'size'=>'200'));
        ?> 
        </div>
        
        <h3><?php print $data['settings.storage.title']; ?></h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $data['settings.storage.path.desc']; ?></p>
        <?php
        print \Formbuilder\Form::text('library_path', $data['library_path'], array('label' => $data['lbl.librarypath'],'description'=>$data['settings.storage.path.note']));
        ?> 
        <?php
        print \Formbuilder\Form::checkbox('url_override', $data['url_override'], array('label' => $data['lbl.override']));
        ?>
        <p><?php print $data['settings.storage.override.desc']; ?></p>
        
        <?php
        print \Formbuilder\Form::text('site_url', $data['site_url'], array('label' => $data['lbl.override'],'description'=>$data['settings.storage.override.note']));
        ?>         
        
        
        </div>

        <h3><?php print $data['settings.contenttype.title']; ?></h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $data['settings.contenttype.desc']; ?></p>
        <?php
        print \Formbuilder\Form::checkbox('autocreate_content_type', $data['autocreate_content_type'], array('label' => $data['lbl.autocreate']));
        ?>    
        </div>
        <br/>
        <input class="btn assman-btn" type="submit" value="<?php print $data['btn.updatesettings']; ?>" />

    </form>

    <hr/>

    <a class="btn" href="<?php print MODX_MANAGER_URL; ?>?a=system/settings"><?php print $data['btn.seesettings']; ?></a>

</div>