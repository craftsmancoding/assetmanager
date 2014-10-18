<div class="assman_canvas_inner">
    <h2 class="assman_cmp_heading"><?php print $data['pagetitle']; ?></h2>
</div>

<div class="assman_canvas_inner">

    <?php print (isset($data['msg'])) ? $data['msg'] : ''; ?>
    
    <form action="" method="post" id="assman_settings">

        

         <h3>Upload Max Size (php.ini and modx System Setting)</h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
        <p>post_max_size(php.ini) : <strong><?php  print ini_get('post_max_size'); ?></strong></p> 
         <p>upload_max_size(php.ini) : <strong><?php  print ini_get('upload_max_filesize'); ?></strong></p> 
          <p>upload_maxsize (modx system setting) : <strong><?php  print ($this->modx->getOption('upload_maxsize')/1024)/1024; ?>M</strong></p>   
        </div>
        <br/>

        <h3><?php print $this->modx->lexicon('assman.settings.thumbnail.title'); ?></h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $this->modx->lexicon('assman.settings.thumbnail.desc'); ?></p>
        <?php
        print \Formbuilder\Form::text('thumbnail_width', $data['thumbnail_width'], array('label' => $this->modx->lexicon('assman.lbl.thumbwidth')));
        ?>    
        <?php
        print \Formbuilder\Form::text('thumbnail_height', $data['thumbnail_height'], array('label' => $this->modx->lexicon('assman.lbl.thumbheight')));
        ?>    
        </div>
        <br/>
        
        <h3><?php print $this->modx->lexicon('assman.settings.resourcetype.title'); ?></h3>

        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $this->modx->lexicon('assman.settings.resourcetype.desc'); ?></p>
        <?php
        print \Formbuilder\Form::text('class_keys', $data['class_keys'], array('label' => $this->modx->lexicon('assman.lbl.classkeys'),'size'=>'200'));
        ?> 
        </div>
        <br/>
        
        <h3><?php print $this->modx->lexicon('assman.settings.storage.title'); ?></h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $this->modx->lexicon('assman.settings.storage.path.desc'); ?></p>
        <?php
        print \Formbuilder\Form::text('library_path', $data['library_path'], array('label' => $this->modx->lexicon('assman.lbl.librarypath'),'description'=>$this->modx->lexicon('assman.settings.storage.path.note')));
        ?> 
        <?php
        print \Formbuilder\Form::checkbox('url_override', $data['url_override'], array('label' => $this->modx->lexicon('setting_assman.url_override')));
        ?>
        <p><?php print $this->modx->lexicon('assman.settings.storage.override.desc'); ?></p>
        
        <?php
        print \Formbuilder\Form::text('site_url', $data['site_url'], array('label' => $this->modx->lexicon('assman.lbl.override'),'description'=>$this->modx->lexicon('assman.settings.storage.override.note')));
        ?>         
        
        </div>
        <br/>
        
        <h3><?php print $this->modx->lexicon('assman.settings.contenttype.title'); ?></h3>
        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $this->modx->lexicon('assman.settings.contenttype.desc'); ?></p>
        <?php
        print \Formbuilder\Form::checkbox('autocreate_content_type', $this->modx->lexicon('assman.lbl.autocreate'), array('label' => $this->modx->lexicon('assman.lbl.autocreate')));
        ?>    
        </div>
        <br/>
        <input class="btn assman-btn btn-primary" type="submit" value="<?php print $this->modx->lexicon('assman.btn.updatesettings'); ?>" />

    </form>

    <hr/>

    <a class="btn" href="<?php print MODX_MANAGER_URL; ?>?a=system/settings"><?php print $this->modx->lexicon('assman.btn.seesettings'); ?></a>

</div>