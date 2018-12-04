<div class="assman_canvas_inner">
	<h2 class="assman_cmp_heading" id="assman_pagetitle"><?php print $this->modx->lexicon('assman.verify.pagetitle'); ?></h2>
</div>
<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
    <p><?php print $this->modx->lexicon('assman.verify.subtitle'); ?></p>
</div>

<div class="assman_canvas_inner">

    <div class="clearfix">

        <form action="" method="post">
            <input type="submit" class="btn" name="verify" value="<?php print $this->modx->lexicon('assman.menu.verify'); ?>" />
        </form>

        <?php if (array_key_exists('errors', $data) && empty($data['errors'])): ?>

            <p>Congratulations!  Your asset library matches with what's in the database!</p>

        <?php elseif (!empty($data['errors'])): ?>

            <pre>
            <?php foreach ($data['errors'] as $e): ?>
                <?php print $e['message'] . "\n"; ?>
            <?php endforeach; ?>
            </pre>

        <?php endif; ?>

    </div><!--/clearfix-->
</div><!--/assman_canvas_inner-->