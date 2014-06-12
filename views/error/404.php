<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">404 Page Not Found</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Sorry, the page you requested could not be found.</p></div>

<div class="moxycart_canvas_inner">



<?php print $this->getMsg(); ?>

<?php if (isset($data['msg'])): ?>
    <div class="danger">
        <?php print $data['msg']; ?>
    </div>
<?php endif; ?>

</div>
<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>