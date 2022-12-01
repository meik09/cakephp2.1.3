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

		echo $this->Html->script('jquery.js', array('inline' => true));
		echo $this->Html->script('common.js', array('inline' => true));

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
<div id="container">
	<div id="header">
		<!-- ロゴ & ログアウト -->
		<div class="logo">
			<?php echo $this->Html->link($this->Html->image('logoutBtn.png', array('alt'=>'ログアウト')), array('controller' => 'Users', 'action' => 'logout'), array('escape' => false, 'tabindex' => '1')); ?>
		</div>
		<!-- メニュー -->
		<div class="menu">
			<?php echo $this->Html->link('[在籍者情報]',   array('controller' => 'Students', 'action' => 'index', 1), array('class' => 'menu_an', 'escape' => false, 'tabindex' => '2')); ?>&nbsp;
			<?php echo $this->Html->link('[ユーザ情報]',   array('controller' => 'Users',    'action' => 'index', 1), array('class' => 'menu_an', 'escape' => false, 'tabindex' => '3')); ?>&nbsp;
			<?php echo $this->Html->link('[システム管理]', array('controller' => 'Systems',  'action' => 'index', 1), array('class' => 'menu_an', 'escape' => false, 'tabindex' => '4')); ?>
		</div>
		<?php echo $this->Session->flash(); ?>
	</div>
	<br />
	<div id="content">
		<!-- ここに各機能のViewが表示される -->
		<?php echo $this->fetch('content'); ?>
	</div>
	<div id="footer">
		<?php // 何もなし ?>
	</div>
</div>
<?php echo $this->element('sql_dump'); ?>
</body>
</html>
