<div id="assman_header" class="clearfix">
    <ul id="assman_nav">
        <li class="assman_nav_item">
            <strong>Manage:</strong>
        </li>

         <li class="assman_nav_item">
            <a class="<?php print ($_GET['method'] == 'assets') ? 'current' : '' ; ?>" href="<?php print self::page('assets'); ?>">Library</a>
         </li>

         <li class="assman_nav_item">
            <a class="<?php print ($_GET['method'] == 'settings') ? 'current' : '' ; ?>" href="<?php print self::page('settings'); ?>">Settings</a>
         </li>

         <li class="assman_nav_item">
            <a class="<?php print ($_GET['method'] == 'groups') ? 'current' : '' ; ?>" href="<?php print self::page('groups'); ?>">Groups</a>
         </li>

       
    </ul>
</div>

<div id="assman_msg"></div>

<div id="assman_canvas">


    
