<!-- elements::ajax_user_list end -->
<div id="ajax_update">
<?php $pagination->loadingId = 'loading';?>
<?php if($pagination->set($paging)):?>
<?php endif;?>
<table width="95%" border="0" cellspacing="2" cellpadding="4">
<tr class="tableheader">
	<?php if($rdAuth->role == 'A' || $rdAuth->role == 'I'):?>
	<th width="15%">Actions</th>
	<?php endif;?>
	<th width="33%"><?php echo $pagination->sortLink('Name',array('name','desc'))?></th>
	<th width="5%">In Use</th>
	<th width="5%"><?php echo $pagination->sortLink('Public',array('availability','desc'))?></th>
	<th width="5%"><?php echo $pagination->sortLink('LOM',array('lom_max','desc'))?></th>
	<th width="5%"><?php echo $pagination->sortLink('Criteria',array('criteria','desc'))?></th>
	<th width="5%"><?php echo $pagination->sortLink('Total Marks',array('total_marks','desc'))?></th>
	<th width="27%"><?php echo $pagination->sortLink('Created By',array('created','desc'))?></th>
	<!--th width="14%"><?php echo $pagination->sortLink('Last Updated By',array('modified','desc'))?></th-->
  </tr>
<?php $i = '0';?>
<?php
foreach ($data as $row): $rubric = $row['Rubric']; ?>
<tr class="tablecell">
  <td align="center">
  <a href="<?php echo $this->webroot.$this->themeWeb.'rubrics/view/'.$rubric['id']?>"><?php echo $html->image('icons/view.gif',array('border'=>'0','alt'=>'View'))?></a>
  <?php if($rdAuth->role == 'A' || $rdAuth->role == 'I'):?>
  	<?php if ($rdAuth->id == $rubric['creator_id'] or $rdAuth->role=='A' ): ?>
      <a href="<?php echo $this->webroot.$this->themeWeb.'rubrics/edit/'.$rubric['id']?>"><?php echo $html->image('icons/edit.gif',array('border'=>'0','alt'=>'Edit'))?></a>
    <?php else: ?>
      <?php echo $html->image('icons/editdisabled.gif',array('border'=>'0','alt'=>'Edit'))?>
    <?php endif;?>
    <?php if ($rdAuth->id == $rubric['creator_id'] or $rdAuth->role=='A'): ?>
      <a href="<?php echo $this->webroot.$this->themeWeb.'rubrics/delete/'.$rubric['id']?>" onclick="return confirm('All associating events and evaluation data will be deleted as well.\n Are you sure you want to delete rubric &ldquo;<?php echo $rubric['name']?>&rdquo;?')"><?php echo $html->image('icons/delete.gif',array('border'=>'0','alt'=>'Delete'))?></a>
    <?php else: ?>
       <?php echo $html->image('icons/deletedisabled.gif',array('border'=>'0','alt'=>'Delete'))?>
    <?php endif;?>
    <a href="<?php echo $this->webroot.$this->themeWeb.'rubrics/copy/'.$rubric['id']?>"><?php echo $html->image('icons/copy.gif',array('border'=>'0','alt'=>'Copy'))?></a>

  <?php endif;?>
  </td>
  <td align="left">
  <?php echo $html->link($rubric['name'], '/rubrics/view/'.$rubric['id']) ?>
  </td>
  <td align="center">
	<?php
		if($sysContainer->checkEvaluationToolInUse('2', $rubric['id']))
			echo $html->image('icons/green_check.gif',array('border'=>'0','alt'=>'green_check'));
		else
			echo $html->image('icons/red_x.gif',array('border'=>'0','alt'=>'red_x'));

	 ?>
  </td>
  <td align="center">
	<?php
	if( $rubric['availability'] == "public" )
		echo $html->image('icons/green_check.gif',array('border'=>'0','alt'=>'green_check'));
	else
		echo $html->image('icons/red_x.gif',array('border'=>'0','alt'=>'red_x'));
	?>
  </td>
  <td align="center">
	<?php echo $rubric['lom_max'] ?>
  </td>
  <td align="center">
	<?php echo $rubric['criteria'] ?>
  </td>
  <td align="center">
	<?php echo $rubric['total_marks'] ?>
  </td>
  <td align="center">
    <?php
    $params = array('controller'=>'rubrics', 'userId'=>$rubric['creator_id']);
    echo $this->renderElement('users/user_info', $params);
    ?><br/>
    <?php echo $this->controller->Output->formatDate(date('Y-m-d H:i:s', strtotime($rubric['created']))) ?>
  </td>
  <!--td align="center">
    <?php
    $params = array('controller'=>'rubrics', 'userId'=>$rubric['updater_id']);
    echo $this->renderElement('users/user_info', $params);
    ?><br/>
    <?php
        if (!empty($rubric['modified'])) echo $this->controller->Output->formatDate(date('Y-m-d H:i:s', strtotime($rubric['modified'])));
    ?>
  </td-->
</tr>
<?php $i++;?>
<?php endforeach; ?>
<?php if ($i == 0) :?>
<tr class="tablecell" align="center">
    <td colspan="9">Record Not Found</td>
</tr>
<?php endif;?>
</table>
<table width="95%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
  <tr>
	<td align="left"><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'corner_bot_left'))?></td>
	<td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'corner_bot_right'))?></td>
  </tr>
</table>
<?php $pagination->loadingId = 'loading2';?>
<div id="page-numbers">
  <table width="95%"  border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td width="33%" align="left"><?php echo $pagination->result('Results: ')?></td>
      <td width="33%"></td>
      <td width="33%" align="right">
<?php if($pagination->set($paging)):?>
        <?php echo $pagination->prev('Prev')?> <?php echo $pagination->numbers()?> <?php echo $pagination->next('Next')?>
<?php endif;?>
      </td>
    </tr>
  </table>
  </div>
</div>
<!-- elements::ajax_user_list end -->
