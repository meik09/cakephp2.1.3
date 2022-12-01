<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('common');
		echo $this->Html->css('login');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>

<body>
<div class="login_content">
<center>
	<?php echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('User', Array('url' => '/users/login')); ?>
	<table class="login_tbl">
		<tr>
			<td class="login_td1" colspan="2">在籍履歴管理システム</td>
		</tr>
		<tr>
			<th class="login_th1">ログインID</th>
			<td class="login_td2"><?php echo $this->Form->input('login_id', Array('type' => 'text', 'class' => 'login_id', 'label' => false)); ?></td>
		</tr>
		<tr>
			<th class="login_th1">パスワード</th>
			<td class="login_td2"><?php echo $this->Form->input('password', Array('type' => 'password', 'class' => 'password', 'label' => false)); ?></td>
		</tr>
	</table>
	<div class="button_block"><?php echo $this->Form->end('loginBtn.png'); ?></div>
</center>
</div> 
<?php echo $this->element('sql_dump'); ?>
</body>
</html>
