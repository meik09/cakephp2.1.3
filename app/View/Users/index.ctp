<?php
/*
 * ユーザ一覧を表示
 */

// CSSを読み込み
echo $this->Html->css('users');
?>
<?php echo $this->Form->create('User', array('url' => '/users/index', 'type' => 'POST', 'inputDefaults' => array('label' => false, 'div' => false, 'legend' => false))); ?>
<div class="content_title">【　ユーザ一覧　】</div>
<table class="user_find_tbl1">
	<tr>
		<th class="user_find_th1">職員番号</th>
		<th class="user_find_th2">職員名</th>
		<th class="user_find_th3" rowspan="2">
			<?php echo $this->Form->submit('/img/users/findBtn.png', array('name' => 'find', 'value' => '検索', 'div' => 'false', 'tabindex' => '7')); ?>
		</th>
	</tr>
	<tr>
		<td class="user_find_td1"><?php echo $this->Form->input('vmoUser.personnel_no', array('type' => 'text', 'class' => 'find_personnel_no', 'tabindex' => '5')); ?></td>
		<td class="user_find_td2"><?php echo $this->Form->input('vmoUser.name', array('type' => 'text', 'class' => 'find_name', 'tabindex' => '6')); ?></td>
	</tr>
</table>
<div class="list_number_block"><?php echo $this->PaginatorEx->pagingCounters(); ?></div>
<table class="user_list_tbl1">
	<tr>
		<th class="user_list_th1">職員番号</th>
		<th class="user_list_th2">職員名</th>
		<th class="user_list_th3">ログインID</th>
		<th class="user_list_th4">&nbsp;</th>
	</tr>
	<?php foreach ($recs as $rec) { ?>
	<tr>
		<td class="user_list_td1"><?php echo $rec['User']['personnel_no']; ?></td>
		<td class="user_list_td2"><?php echo $rec['User']['name']; ?></td>
		<td class="user_list_td3"><?php echo $rec['User']['login_id']; ?></td>
		<td class="user_list_td4">
			<?php echo $this->Html->link($this->Html->image('/img/users/editBtn.png', array('alt'=>'編集')), array('controller' => 'Users', 'action' => 'edit', $rec['User']['id']), array('escape' => false)); ?>
		</td>
	</tr>
	<?php } ?>
</table>
<div class="paging_block"><?php echo $this->PaginatorEx->pagingLinks(); ?></div>
<hr class="separator" />
<div class="button_block">
	<?php echo $this->Html->link($this->Html->image('/img/users/addBtn.png', array('alt'=>'新規登録')), array('controller' => 'Users', 'action' => 'add'), array('escape' => false)); ?>
</div>
<?php echo $this->Form->end(); ?>
