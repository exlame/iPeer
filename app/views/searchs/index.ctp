
<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="center">
    
<form id="searchForm" method="post" action="/searchs/index">
<table width="60%" align="center" border="0" cellspacing="0" cellpadding="2">
  <tr >
    <td width="10" height="32"></td>
    <td width="100"><?php echo $html->image('magnify.png', array('alt'=>'Magnify Icon'));?> <b>Search For:</b> </td>
    <td align="left"><select name="select" id="select2">
        <option value="evaluation" >Evaluation Events</option>
        <option value="eval_result" >Evaluation Results</option>
        <option value="instructor" >Instructors</option>
    </select></td>
  </tr>
</table>
  </form>
<?php echo $ajax->observeField('select2', array(
								'update'=>'search_table',
								'url'=>"/searchs/display", 
								'frequency'=>1, 
								'loading'=>"Element.show('loading');", 
								'complete'=>"Element.hide('loading')")) ?>
<?php
?>

<a name="list"></a>

<div id='search_table'>

<?php
$params = array('controller'=>'schs', 'data'=>$data, 'paging'=>$paging, 'currentUser'=>$currentUser, array('names'=>$names));
echo $this->element('searchs/evaluation_search_panel', $params);
//echo $this->element('searchs/evaluation_search_panel', array('currentUser'=>$currentUser));

?>
</div>
	</td>
  </tr>
</table>
