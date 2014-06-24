<div class="assman_canvas_inner">
    <h2 class="assman_cmp_heading">Moxycart Settings</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>MODX Settings are hierarchical: they can be overridden by Content Settings.</p></div>

<div class="assman_canvas_inner">

    
    <?php
    $settings = array(

    );
    ?>

    <table class="classy">
        <thead>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($settings as $s): ?>
                <tr>
                    <td><?php print $s; ?></td>
                    <td><?php print $this->modx->getOption($s); ?></td>
                </tr>
            <?php endforeach; ?>
            
        </tbody>
    </table>
    <br>
    <a class="btn assman-btn" href="<?php print MODX_MANAGER_URL; ?>?a=70">See all Settings</a>

</div>