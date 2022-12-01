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
		echo $this->Html->css('subwindow');
		
		echo $this->Html->script('seminarTeachers.js', array('inline' => true));

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<base target="_self" />
</head>

<body>
<div id="container">
	<div id="header">
		<!-- タイトル -->
		<div class="title">ゼミ教員検索</div>
	</div>
	<br />
	<!-- ここに各機能のViewが表示される -->
	<div id="content">
		<?php echo $this->Form->create('SeminarTeacher', array('url' => '/seminarTeachers/index', 'type' => 'POST', 'inputDefaults' => array('label' => false, 'div' => false, 'legend' => false))); ?>
		<table class="seminor_find_tbl">
			<tr>
				<th class="seminor_find_th1">教員名：</th>
				<td class="seminor_find_td1"><?php echo $this->Form->input('vmoSeminarTeacher.teacher_name', array('type' => 'text', 'class' => 'find_seminor_name')); ?></td>
				<th class="seminor_find_td2"><?php echo $this->Form->submit('/img/students/findBtn.png', array('name' => 'find', 'value' => '検索', 'div' => 'false')); ?></th>
			</tr>
		</table>
		<?php if (count($recs) > 0) { ?>
		<div class="list_number_block"><?php echo $this->PaginatorEx->pagingCounters(); ?></div>
		<table class="seminor_list_tbl">
			<tr>
				<th class="seminor_list_th1">学校ID</th>
				<th class="seminor_list_th2">学校名</th>
			</tr>
			<?php foreach ($recs as $rec) { ?>
			<?php		$jsFunc = "closeWindow('".$rec['SeminarTeacher']['seminar_teacher_id']."', '".$rec['SeminarTeacher']['name']."')"?>
			<tr>
				<td class="seminor_list_td1"><?php echo $this->Html->link($rec['SeminarTeacher']['name_kana'], 'javascript:void(0)', array('escape' => false, 'onClick' => $jsFunc), false); ?></td>
				<td class="seminor_list_td2"><?php echo $this->Html->link($rec['SeminarTeacher']['name'], 'javascript:void(0)', array('escape' => false, 'onClick' => $jsFunc), false); ?></td>
			</tr>
			<?php } ?>
		</table>
		<div class="paging_block"><?php echo $this->PaginatorEx->pagingLinks(); ?></div>
		<?php } ?>
		<hr class="separator" />
		<div class="button_block">
			<?php echo $this->Html->link($this->Html->image('/img/students/cancelBtn.png', array('alt' => 'キャンセル')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'closeWindow(\'\', \'\')'), false); ?>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
<?php echo $this->element('sql_dump'); ?>
</body>
</html>
