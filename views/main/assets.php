<script type="text/javascript">
    var assman = <?php print json_encode($this->config); ?>;
    Ext.onReady(function() {
        define_delete_asset_dialog();
    });
</script>
<div class="assman_canvas_inner">
    <h2 class="assman_cmp_heading" id="assman_pagetitle"><?php print $data['pagetitle']; ?></h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p><?php print $data['subtitle']; ?></p></div>

<div class="assman_canvas_inner">

<div class="clearfix">

    <!--span class="button btn assman-btn pull-left" onclick="javascript:paint('fieldcreate');">Add Asset</span-->

        <div class="pull-right">   
            <form action="<?php print static::page('assets'); ?>" method="post">
                <input type="text" name="searchterm" placeholder="<?php print $this->modx->lexicon('assman.btn.search'); ?>..." value="<?php print $data['searchterm']; ?>"/>    
                <input type="submit" class="button btn assman-btn" value="<?php print $this->modx->lexicon('assman.btn.search'); ?>"/>
                <a href="<?php print static::page('assets'); ?>" class="btn"><?php print $this->modx->lexicon('assman.btn.showall'); ?></a>
            </form>
            
        </div>
   </div>     

<?php if ($data['results']): ?>
<table class="classy">
    <thead>
        <tr>
            <th>
                <?php print $this->modx->lexicon('assman.lbl.thumbnail'); ?>
            </th>
            <th>
                <?php print $this->modx->lexicon('assman.lbl.title'); ?>
            </th>
            <th>
                <?php print $this->modx->lexicon('assman.lbl.alt'); ?>
            </th>
            <th>
                <?php print $this->modx->lexicon('assman.lbl.size'); ?>
            </th>
            <th><?php print $this->modx->lexicon('assman.lbl.action'); ?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $r) :?>
    <tr id="page-asset-<?php print $r->get('asset_id'); ?>">
        <td><?php 
            //print $r->get('path'); 
            print $this->modx->runSnippet('Asset', array('asset_id'=>$r->get('asset_id'),'width'=>$this->modx->getOption('assman.thumbnail_width'),'tpl'=>'<img src="[[+url]]" width="'.$this->modx->getOption('assman.thumbnail_width').'" height="[[+asset_id.height]]" />'));
            ?>
        </td>
        <td><?php print $r->get('title'); ?></td>
        <td><?php print $r->get('alt'); ?></td>
        <td><?php print $r->get('size'); ?>
        </td>
        <td>
            <span class="button btn" onclick="javascript:open_asset_modal('<?php print $r->get('asset_id'); ?>');"><?php print $this->modx->lexicon('assman.btn.edit'); ?></span>
            <span class="button btn" onclick="javascript:jQuery('#delete_asset_modal').data('asset_id', '<?php print $r->get('asset_id'); ?>').dialog('open');"><?php print $this->modx->lexicon('assman.btn.delete'); ?></span>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <div class="danger"><?php print $this->modx->lexicon('assman.no_results'); ?></div>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('assman.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
?>
</div>



    <div id="delete_asset_modal" title="Delete Asset">
        <p>Deleting the asset will remove it permanently from your site.</p>
        <p class="danger">Deleting cannot be undone!</p>
    </div>
