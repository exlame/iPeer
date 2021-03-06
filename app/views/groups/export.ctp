<?php echo $html->script('groups');?>
<div class="content-container">
    <form name="frm" id="frm" method="POST" action="<?php echo $html->url('export/'.$courseId) ?>">
      <table class="standardtable">
        <tr>
          <th colspan="2" align="center">Export As</th>
        </tr>
        <tr>
          <td width="30%">Export Filename:</td><td width="40%"><input type="text" name="file_name" value="<?php if(isset($file_name)) echo $file_name;?>" />.csv</td>
        </tr>
        <tr>
          <th colspan="2" align="center">Export Group Fields</th>
        </tr>
        <tr><td colspan="2" style="color:darkred; font-size:smaller"> (Please select at least one of the fields)</td></tr>
        <tr>
          <td width="60%">Include Group Number(s):</td><td><input type="checkbox" name="include_group_numbers" checked /></td>
        </tr>
        <tr>
          <td width="60%">Include Group Name(s):</td><td><input type="checkbox" name="include_group_names" checked /></td>
        </tr>
        <?php if (User::hasPermission('functions/viewusername')) { ?>
            <tr>
              <td width="60%">Include Username(s):</td><td><input type="checkbox" name="include_usernames" checked /></td>
            </tr>
        <?php } ?>
        <tr>
          <td width="60%">Include Student Id #:</td><td><input type="checkbox" name="include_student_id" checked /></td>
        </tr>
        <tr>
          <td width="60%">Include Student Name(s):</td><td><input type="checkbox" name="include_student_name" checked /></td>
        </tr>
        <?php /*if (User::hasPermission('functions/viewemailaddress')) { ?>
            <tr>
              <td>Include Student Email(s):</td><td><input type="checkbox" name="include_student_email" /></td>
            </tr>
        <?php }*/ ?>
        </table>
        <table class="standardtable">
        <tr>
          <th>Group Selection</th>
        </tr>
        <tr>
          <td>
<?php
echo $this->element("groups/group_list_chooser",
    array('all' => $unassignedGroups, 'assigned'=>'',
    'allName' =>  __('Available Groups', true), 'selectedName' => __('Participating Groups', true),
    'itemName' => 'Group', 'listStrings' => array("Group #", "group_num"," - ","group_name")));
?>
          </td>
        </tr>
        <tr>
          <td>
<?php echo $this->Form->submit(ucfirst($this->action).__(' Group', true), array('div' => false,
    'onClick' => "processSubmit(document.getElementById('selected_groups'));")) ?>
          </td>
        </tr>
      </table>
    </form>
</div>
