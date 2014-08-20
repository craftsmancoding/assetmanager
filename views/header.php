<div class="x-panel-bwrap">
    <div class="x-panel-body x-panel-body-noheader x-panel-body-noborder" >
        <div class=" x-panel container x-panel-noborder">
            <div class="x-panel-bwrap">
                
                <div id="assman_header" class="clearfix">
                    <ul id="assman_nav">
                         <li class="assman_nav_item">
                            <a class="<?php print ($_GET['method'] == 'index') ? 'current' : '' ; ?>" href="<?php print self::page('index'); ?>"><?php print $this->modx->lexicon('assman.menu.manage'); ?></a>
                         </li>

                         <li class="assman_nav_item">
                            <a class="<?php print ($_GET['method'] == 'assets') ? 'current' : '' ; ?>" href="<?php print self::page('assets'); ?>"><?php print $this->modx->lexicon('assman.menu.library'); ?></a>
                         </li>

                         <li class="assman_nav_item">
                            <a class="<?php print ($_GET['method'] == 'verify') ? 'current' : '' ; ?>" href="<?php print self::page('verify'); ?>"><?php print $this->modx->lexicon('assman.menu.verify'); ?></a>
                         </li>

                         <li class="assman_nav_item">
                            <a class="<?php print ($_GET['method'] == 'settings') ? 'current' : '' ; ?>" href="<?php print self::page('settings'); ?>"><?php print $this->modx->lexicon('assman.menu.settings'); ?></a>
                         </li>

                         <li class="assman_nav_item">
                            <a class="<?php print ($_GET['method'] == 'groups') ? 'current' : '' ; ?>" href="<?php print self::page('groups'); ?>"><?php print $this->modx->lexicon('assman.menu.groups'); ?></a>
                         </li>

                       
                    </ul>
                </div>

                <div id="assman_msg"></div>

                <div id="assman_canvas">


    
