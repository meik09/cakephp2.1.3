<?php
/*
 * ユーザ更新画面を表示
 */

// バリデーションエラーを表示
echo $this->element("validationErrors");

// CSSを読み込み
echo $this->Html->css('users');

// JSを読み込み
echo $this->Html->script('users.js', array('inline' => false));
?>
<?php echo $this->Form->create('User', array('action' => 'edit', 'url' => array($this->data['User']['id']), 'type' => 'POST', 'inputDefaults' => array('label' => false, 'div' => false, 'legend' => false, 'error' => false))); ?>
<div class="content_title">
	【　ユーザ更新　】
	<font class="caution_msg">*印のある項目は必ず入力して下さい。</font>
</div>
<table class="user_input_tbl">
	<tr>
	<th class="user_input_th1">職員番号：</th>
	<td class="user_input_td1"><?php echo $this->data['User']['personnel_no']; ?></td>
	</tr>
	<tr>
	<th class="user_input_th2">職員氏名：</th>
	<td class="user_input_td2"><?php echo $this->Form->input('name', array('type' => 'text', 'class' => 'user_name')); ?></td>
	</tr>
	<tr>
	<th class="user_input_th3">*ログインID：</th>
	<td class="user_input_td3"><?php echo $this->Form->input('login_id', array('type' => 'text', 'class' => 'user_login_id')); ?></td>
	</tr>
	<tr>
	<th class="user_input_th4">*パスワード：</th>
	<td class="user_input_td4"><?php echo $this->Form->input('password', array('type' => 'text', 'class' => 'user_password')); ?></td>
	</tr>
</table>
<hr class="separator" />
<div class="button_block">
	<?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
	<?php echo $this->Form->input('personnel_no', array('type' => 'hidden')); ?>
	<?php echo $this->Form->submit('/img/users/updateBtn.png', array('name' => 'update', 'div' => false)); ?>
	<?php echo $this->Form->submit('/img/users/deleteBtn.png', array('name' => 'delete', 'onClick' => 'return onSubmit4Delete();', 'div' => false)); ?>
	<?php echo $this->Html->link($this->Html->image('/img/users/cancelBtn.png', array('alt' => 'キャンセル')), array('action' => 'index'), array('escape' => false), false); ?>
</div>
<?php echo $this->Form->end(); ?>
