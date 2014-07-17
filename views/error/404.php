<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<div class="assman_canvas_inner">
    <h2 class="assman_cmp_heading">404 Page Not Found</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Sorry, the page you requested could not be found.</p></div>

<div class="assman_canvas_inner">


<?php if (isset($data['msg'])): ?>
    <div class="danger">
        <?php print $data['msg']; ?>
    </div>
<?php endif; ?>

</div>
<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>