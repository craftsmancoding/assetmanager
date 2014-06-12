<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Edit Asset <?php print $data['slug']; ?></h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Here you can  Edit Asset.</p></div>

<div class="moxycart_canvas_inner">


<?php
\Formbuilder\Form::setTpl('description','<p class="description-txt">[+description+]</p>');
print \Formbuilder\Form::open($data['baseurl'])
    ->hidden('field_id',$data['field_id'])
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.','class'=>'input input-half'))
    ->text('label', $data['label'], array('label'=>'Label','description'=>'Human readable name for this field.','class'=>'input input-half'))
    ->dropdown('type', array('text'=>'Text','textarea'=>'Textarea','checkbox'=>'Checkbox','dropdown'=>'Dropdown','multicheck'=>'Multi-Check'), $data['type'], array('label'=>'Field Type', 'description'=>'Choose what type of field this is.'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.','class'=>'input input-half'))
    ->textarea('config', $data['config'], array('label'=>'Configuration','description'=>'Some fields require special customization via a JSON array.'))
    ->html('<br>')
    ->text('group', $data['group'], array('label'=>'Group','description'=>'Fields with the same group value will appear together.','class'=>'input input-half'))
    ->submit('','Save',array('class'=>'btn moxycart-btn'))
    ->close();
?>
<div>
    <a href="<?php print static::url('field','index'); ?>" class="btn btn-cancel">Cancel</a>
</div>

</div>
<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>