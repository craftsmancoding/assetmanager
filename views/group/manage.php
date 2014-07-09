<script id="asset_groups_tpl" type="text/x-handlebars-template">
<form method="post" action="">
    <span class="btn" onclick="javascript:add_group();">Add Group</span>
    <ul id="asset_groups">
    {{#each Groups}}    
        <li class="row-input"><input type="text" name="groups[]" class="row-field" value="{{this}}"/> <span class="btn" onclick="javascript:remove_me.call(this,event,'li');">x</span></li>
    {{/each}}
    </ul>

    <input type="submit" class="btn assman-btn" value="Save"/>
    <a class="btn" href="{{controller_url}}">Cancel</a>
</form>
</script>

<script>
jQuery(document).ready(function(){
    console.debug('[page_init]');
    inited = 1; // flag it as having been initialized
    assman['tpls'] = {};
    assman.tpls.asset_groups = Handlebars.compile(jQuery('#asset_groups_tpl').html());
    jQuery('#manage_groups').html( assman.tpls.asset_groups(assman) );
    

    jQuery( "#asset_groups" ).sortable();
    jQuery( "#asset_groups" ).disableSelection();

});

function add_group() {
    jQuery('#asset_groups').append('<li class="row-input"><input type="text" name="groups[]" class="row-field" value=""/> <span class="btn" onclick="javascript:remove_me.call(this,event,\'li\');">x</span></li>');
}
</script>

<div class="assman_canvas_inner">
    <h2 class="assman_cmp_heading">Asset Groups</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Use groups to help organize your assets within a page.  Each asset may belong to only one group for page.  If an asset is used on multiple pages, it may belong to a different group on each page where it is used.</p></div>

<div class="assman_canvas_inner" id="manage_groups">

</div>