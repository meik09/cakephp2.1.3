<?php
/*
 * システム管理を表示
 */

// CSSを読み込み
echo $this->Html->css('systems');
?>
<?php echo $this->Form->create('VmoSystem', array('url' => '/temporaries/index', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'legend' => false))); ?>
<div class="content_title">【　基本データインポート　】</div>
<table class="system_import_tbl1">
	<tr>
		<th class="system_import_th1">アップロード対象：</th>
		<td class="system_import_td1"><?php echo $this->Form->input('upload_type', array('type' => 'select', 'options' => $uploadType, 'class' => 'upload_file_name_slt')); ?></td>
	</tr>
	<tr>
		<th class="system_import_th1">ファイル：</th>
		<td class="system_import_td1"><?php echo $this->Form->inpu('uploadFileName', array('type' => 'file', 'class' => 'upload_file_fld', 'size' => '65')); ?></td>
	</tr>
	<?php if (isset($errFile)) { ?>
	<tr>
		<th class="system_import_th1">エラーファイル：</th>
		<td class="system_import_td1">
			<?php echo $this->Html->link($errFile, '/files/'.$errFile); ?>
			&nbsp;←&nbsp;ダウンロードをされる場合には、リンク上で右クリックをして｢対象をファイルに保存...｣を選択して下さい。
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td class="system_import_td2" colspan="2">
			<?php echo $this->Form->submit('/img/systems/uploadBtn.png', array('name' => 'upload', 'value' => 'アップロード', 'div' => false)); ?>
		</td>
	</tr>
</table>
<hr class="separator" />
<?php echo $this->Form->end(); ?>
