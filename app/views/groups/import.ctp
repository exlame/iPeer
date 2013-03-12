<div id='groupsimport'>
<h2><?php __('Instructions') ?></h2>
<ul>
    <li><?php __('Please make sure the column matches the username column in student import file.')?></li>
    <li><?php __('Please make sure to remove the header in CSV file.')?></li>
    <li><?php __('All fields are mandatory, except Group#.')?></li>
    <li><?php __('All group names must be unique within the class.')?></li>
    <li><?php __('The student identifiers that can be used are usernames or student numbers.')?></li>
    <li><?php __('If the Group# column is missing, the system will generate a Group#.')?></li>
</ul>

<h3><?php __('Formatting:')?></h3>
    <pre style='background-color: white; border:1px solid black; padding:5px; margin:5px'>
    <?php __('Student Identifier, Group Name, <i>Group#(optional)</i>')?>
    </pre>

<h3><?php __('Examples:')?></h3>
    <pre style='background-color: white; border:1px solid black; padding:5px; margin:5px'>
        29978037, <?php __('Team A')?>, 1<br>
        29978063, <?php __('Team A')?>, 1<br>
        29978043, <?php __('Team B')?>, 2<br>
        29978051, <?php __('Team B')?>, 2
    </pre>
    
<h2><?php __('Import')?></h2>

<?php
echo $this->Form->create(null, array('type' => 'file', 'url' => 'import/'.$courseId));
echo $this->Form->input('file', array('type' => 'file', 'name' => 'file'));
echo $this->Form->input('identifiers', array(
    'type' => 'radio',
    'options' => array('student_no' => 'Student No.', 'username' => 'Username'),
    'legend' => __('Student Identifier', true),
    'default' => 'username'
));
?><div class="help-text"><?php echo _t('The student identifier used in the CSV file.')?></div><?php
echo $this->Form->input('Course',
    array('multiple'=>false, 'default' => $courseId));
echo $this->Form->input('update_groups',
    array('type'=>'checkbox'));;
?><div class="help-text"><?php echo _t('Update group members for existing groups.')?></div><?php
echo $this->Form->submit(__('Import', true));
echo $this->Form->end();
?>
</div>