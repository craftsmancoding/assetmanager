<?php
$a = (int) $_GET['a'];
print $this->getMsg();
?>
<div class="assman_canvas_inner">
    <h2 class="assman_cmp_heading" id="assman_pagetitle">Manage Assets</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Here you can Manage your Products Assets.</p></div>

<div class="assman_canvas_inner">

<div class="clearfix">

    <span class="button btn assman-btn pull-left" onclick="javascript:paint('fieldcreate');">Add Asset</span>

        <div class="pull-right">   
            <form action="<?php print static::page('assets'); ?>" method="post">
                <input type="text" name="searchterm" placeholder="Search..." value="<?php print $data['searchterm']; ?>"/>    
                <input type="submit" class="button btn assman-btn" value="Search"/>
                <a href="<?php print static::page('assets'); ?>" class="btn">Show All</a>
            </form>
            
        </div>
   </div>     

<?php if ($data['results']): ?>
<table class="classy">
    <thead>
        <tr>
            <th>
                Thumbnail
            </th>
            <th>
                Title
            </th>
            <th>
                Alt
            </th>
            <th>
                Size
            </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $r) :?>
    <tr>
        <td><?php print $r->get('thumbnail_url'); ?></td>
        <td><?php print $r->get('title'); ?></td>
        <td><?php print $r->get('alt'); ?></td>
        <td><?php print $r->get('size'); ?>
        </td>
        <td>
            <span class="button btn" onclick="javascript:paint('assetedit',{asset_id:<?php print $r->get('asset_id'); ?>});">Edit</span>
            <span class="button btn" onclick="javascript:mapi('asset','delete',{asset_id:<?php print $r->get('asset_id'); ?>},'assets');">Delete</span>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <div class="danger">You don't have any assets uploaded yet.</div>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('assman.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
?>
</div>