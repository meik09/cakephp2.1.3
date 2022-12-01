<?php
/*
 * 在籍者の編集画面を表示
 */

// バリデーションエラーを表示
echo $this->element("validationErrors");

// CSSを読み込み
echo $this->Html->css('students');

// JSを読み込み
echo $this->Html->script('jquery.js', array('inline' => false));
echo $this->Html->script('jquery.blockUI.js', array('inline' => false));
echo $this->Html->script('jquery.common.js', array('inline' => false));
echo $this->Html->script('students.js', array('inline' => false));
echo $this->Html->script('historyList.js', array('inline' => false));
echo $this->Html->script('seminarList.js', array('inline' => false));
echo $this->Html->script('clubList.js', array('inline' => false));
echo $this->Html->script('supportList.js', array('inline' => false));
?>
<?php echo $this->Form->create('Student', array('action' => 'edit', 'url' => array($this->data['Student']['id']), 'type' => 'POST', 'inputDefaults' => array('label' => false, 'div' => false, 'legend' => false, 'error' => false))); ?>
<?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
<div class="content_title">
	【　在籍者編集　】
	<font class="caution_msg">*印のある項目は必須なので必ず入力して下さい。</font>
</div>
<table class="student_input_tbl1">
	<tr>
		<th colspan="3" class="student_input_th1_1">個人ID：</th>
		<td colspan="3"  class="student_input_td1_3"><?php echo $this->data['Student']['personal_id']; ?></td>
	</tr>
	<tr>
		<th rowspan="3" class="student_input_th1_3">氏名</th>
		<th colspan="2" class="student_input_th1_4">カナ：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('name_kana', array('type' => 'text', 'class' => 'name')); ?></td>
	</tr>
	<tr>
		<th colspan="2" class="student_input_th1_4">*標準：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('name', array('type' => 'text', 'class' => 'name')); ?></td>
	</tr>
	<tr>
		<th colspan="2" class="student_input_th1_4">外字：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('name_ext', array('type' => 'text', 'class' => 'name')); ?></td>
	</tr>
    <tr>
        <th rowspan="2" class="student_input_th1_3">旧姓</th>
        <th colspan="2" class="student_input_th1_4">カナ：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('name_old_kana', array('type' => 'text', 'class' => 'name')); ?></td>
    </tr>
    <tr>
        <th colspan="2" class="student_input_th1_4">漢字：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('name_old', array('type' => 'text', 'class' => 'name')); ?></td>
    </tr>
	<tr>
		<th colspan="3" class="student_input_th1_1">性別：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('sex', array('type' => 'radio', 'options' => $sex, 'legend' => false, 'label' => true)); ?></td>
	</tr>
	<tr>
		<th colspan="3" class="student_input_th1_1">生年月日：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('birthday', array('type' => 'text', 'class' => 'date')); ?></td>
	</tr>
	<tr>
		<th colspan="3" class="student_input_th1_1">郵便番号：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('zip', array('type' => 'text', 'class' => 'zip')); ?></td>
	</tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">住所：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('address', array('type' => 'text', 'class' => 'address')); ?></td>
    </tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">旧郵便番号：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('zip_old', array('type' => 'text', 'class' => 'zip')); ?></td>
    </tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">旧住所：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('address_old', array('type' => 'text', 'class' => 'address')); ?></td>
    </tr>
	<tr>
		<th colspan="3" class="student_input_th1_1">電話番号：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('tel', array('type' => 'text', 'class' => 'phone')); ?></td>
	</tr>
	<tr>
		<th colspan="3" class="student_input_th1_1">携帯番号：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('mobile_phone', array('type' => 'text', 'class' => 'phone')); ?></td>
	</tr>
	<tr>
		<th rowspan="2" class="student_input_th1_3">メールアドレス</th>
		<th colspan="2" class="student_input_th1_4">PC：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('mail_address_pc', array('type' => 'text', 'class' => 'mail_address')); ?></td>
	</tr>
	<tr>
		<th colspan="2" class="student_input_th1_4">携帯：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('mail_address_mp', array('type' => 'text', 'class' => 'mail_address')); ?></td>
	</tr>
	<tr>
		<th rowspan="4" class="student_input_th1_3">保証人</th>
		<th colspan="2" class="student_input_th1_4">氏名：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('guarantor_name', array('type' => 'text', 'class' => 'name')); ?></td>
	</tr>
	<tr>
		<th colspan="2" class="student_input_th1_4">郵便番号：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('guarantor_zip', array('type' => 'text', 'class' => 'zip')); ?></td>
	</tr>
    <tr>
        <th colspan="2" class="student_input_th1_5">住所：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('guarantor_address', array('type' => 'text', 'class' => 'address')); ?></td>
    </tr>
	<tr>
		<th colspan="2" class="student_input_th1_4">電話番号：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('guarantor_tel', array('type' => 'text', 'class' => 'phone')); ?></td>
	</tr>
    <tr>
        <th rowspan="2" class="student_input_th1_3">就職情報</th>
        <th colspan="2" class="student_input_th1_4">勤務先：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('company', array('type' => 'text', 'class' => 'employment')); ?></td>
    </tr>
    <tr>
        <th colspan="2" class="student_input_th1_4">勤務先電話番号：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('company_tel', array('type' => 'text', 'class' => 'phone')); ?></td>
    </tr>
	<tr>
		<th colspan="3" class="student_input_th1_1">同窓会入会：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('payment_flag', array('type' => 'radio', 'options' => $payment, 'legend' => false, 'label' => true)); ?></td>
    </tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">同窓会費未払額(積算)：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('unsettled_price', array('type' => 'text', 'class' => 'number')); ?></td>
    </tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">大学賛助金・寄付：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('support_flag', array('type' => 'radio', 'options' => $support, 'legend' => false, 'label' => true)); ?></td>
    </tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">高校賛助金：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('support_price', array('type' => 'text', 'class' => 'number')); ?></td>
    </tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">地域支部：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('area_branch_name', array('type' => 'text', 'class' => 'name')); ?></td>
    </tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">職域支部：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('job_branch_name', array('type' => 'text', 'class' => 'name')); ?></td>
    </tr>
    <tr>
        <th colspan="3" class="student_input_th1_1">各種諸団体：</th>
        <td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('group_name', array('type' => 'text', 'class' => 'name')); ?></td>
    </tr>
	<tr>
		<th colspan="3" class="student_input_th1_1">案内状送付可否：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('sending_flag', array('type' => 'radio', 'options' => $sending, 'legend' => false, 'label' => true)); ?></td>
	</tr>
	<tr>
		<th colspan="3" class="student_input_th1_1">案内状不着：</th>
		<td colspan="3" class="student_input_td1_3">
			<?php echo $this->Form->input('deliver_ng_flag', array('type'=>'checkbox', 'value'=>$deliverKey[1])); ?>
			<label for="StudentDeliverNgFlag"><?php echo $deliverValue[1]; ?></label>
		</td>
	</tr>
	<tr>
		<th colspan="3" class="student_input_th1_7">生存情報：</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->input('alive_status', array('type' => 'radio', 'options' => $alive, 'legend' => false, 'label' => true)); ?></td>
	</tr>
	<tr>
		<th colspan="3" class="student_input_th1_7">備考：<br />(更新情報等を記入)</th>
		<td colspan="3" class="student_input_td1_3"><?php echo $this->Form->textarea('notes', array('class'=>'notes')); ?></td>
	</tr>
</table>
<div class="separator_div">&nbsp;</div>


<div class="subTitle">賛助金情報</div>
<div class="content_title">
    【　賛助金履歴　】
    <font class="caution_msg">年度、金額が入力されていない情報は登録されません。</font>
</div>
<table class="student_input_tbl1">
    <tr>
        <th class="student_input_th1_8">賛助金：</th>
        <td class="student_input_td1_3">
            <table id="supportPriceTbl">
                <thead>
                    <tr>
                        <th class="supportFundTitle">年度</th>
                        <th class="supportFundTitle">金額</th>
                        <th class="supportFundTitle">削除</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="supportPrice99" style="display: none">
                        <td>
                            <?php echo $this->Form->input('SupportPrice.99.year', array('type' => 'text', 'class' => 'number')); ?>
                            <?php echo $this->Form->input('SupportPrice.99.delete_flg', array('type' => 'hidden')); ?>
                        </td>
                        <td><?php echo $this->Form->input('SupportPrice.99.price', array('type' => 'text', 'class' => 'number')); ?></td>
                        <td style="text-align:center;"><?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteSupportRow(99)', 'tabindex' => '-1'), false); ?></td>
                    </tr>
                    <?php
                    $supportNum = 0;
                    foreach ($this->data['SupportPrice'] as $key => $rec) {
                        if ($key == 99) continue;
                        ?>
                        <tr id="supportPrice<?php echo $supportNum; ?>" <?php if ((isset($rec['delete_flg'])) &&($rec['delete_flg'] == 1)) echo 'style="display:none;"'; ?>>
                            <td>
                                <?php echo $this->Form->input('SupportPrice.' . $supportNum . '.year', array('type' => 'text', 'class' => 'number')); ?>
                                <?php echo (!empty($rec['id'])) ? $this->Form->input('SupportPrice.' . $supportNum . '.id', array('type' => 'hidden')) : ''; ?>
                                <?php echo $this->Form->input('SupportPrice.' . $supportNum . '.delete_flg', array('type' => 'hidden')); ?>
                            </td>
                            <td><?php echo $this->Form->input('SupportPrice.' . $supportNum . '.price', array('type' => 'text', 'class' => 'number')); ?></td>
                            <td style="text-align:center;"><?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteSupportRow(' . $supportNum . ')', 'tabindex' => '-1'), false); ?></td>
                        </tr>
                        <?php
                        $supportNum++;
                    }
                    ?>
                </tbody>
            </table>
            <div class="supportFunc_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addSupportRow()'), false); ?></div>
        </td>
    </tr>
</table>
<div class="separator_div">&nbsp;</div>
<div class="subTitle">大学関連情報</div>
<div class="content_title">
	【　入学履歴　】
	<font class="caution_msg">在籍状態区分情報、学部・学科・専攻名が入力されていない情報は登録されません。</font>
</div>
<span id="CollegeHistoryList">
	<?php
		$collegeHistoryNum = 0;
		foreach ($this->data['CollegeHistory'] as $key => $rec) {
			if ($key == 99)	continue;
	?>
	<span id="CollegeHistory<?php echo $collegeHistoryNum; ?>" <?php if (isset($rec['delete_flg']) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($collegeHistoryNum != 0) echo '<div class="separator_div">&nbsp;</div>'; ?>
		<table class="student_input_tbl2">
			<tr>
				<th class="student_input_th2_1">出身校：</th>
				<td class="student_input_td2_1">
                    <?php echo $this->Form->input('CollegeHistory.' . $collegeHistoryNum . '.alma_mater_name', array('type' => 'text', 'class' => 'name')); ?>
					<?php echo (!empty($rec['id'])) ? $this->Form->input('CollegeHistory.'.$collegeHistoryNum.'.id', array('type' => 'hidden')) : ''; ?>
					<?php echo $this->Form->input('CollegeHistory.'.$collegeHistoryNum.'.delete_flg', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td2_2" rowspan="8">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteCollegeHistoryTbl('.$collegeHistoryNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
            <tr>
                <th class="student_input_th2_1">大学卒業後の進路：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.' . $collegeHistoryNum . '.graduation_course', array('type' => 'text', 'class' => 'name')); ?></td>
            </tr>
			<tr>
				<th class="student_input_th2_1">在学番号：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.'.$collegeHistoryNum.'.student_no', array('type' => 'text', 'class' => 'number', 'maxlength' => '7')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">学生種別：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.'.$collegeHistoryNum.'.student_type', array('type' => 'select', 'options' => $studentCollegeType, 'empty' => true)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*在籍状態区分情報：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.'.$collegeHistoryNum.'.student_status', array('type' => 'text', 'class' => 'employment')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*学部・学科・専攻名：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.' . $collegeHistoryNum . '.faculty_name', array('type' => 'text', 'class' => 'department')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">入学年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.'.$collegeHistoryNum.'.admission_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">卒業(退学･除籍)年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.'.$collegeHistoryNum.'.graduation_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$collegeHistoryNum++;
		} 
	?>
	<span id="CollegeHistory99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl2">
			<tr>
				<th class="student_input_th2_1">出身校：</th>
				<td class="student_input_td2_1">
                    <?php echo $this->Form->input('CollegeHistory.99.alma_mater_name', array('type' => 'text', 'class' => 'name')); ?>
					<?php echo $this->Form->input('CollegeHistory.99.delete_flg', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td2_2" rowspan="8">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
            <tr>
                <th class="student_input_th2_1">大学卒業後の進路：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.99.graduation_course', array('type' => 'text', 'class' => 'name')); ?></td>
            </tr>
			<tr>
				<th class="student_input_th2_1">在学番号：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.99.student_no', array('type' => 'text', 'class' => 'number', 'maxlength' => '7')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">学生種別：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.99.student_type', array('type' => 'select', 'options' => $studentCollegeType, 'empty' => true)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*在籍状態区分情報：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.99.student_status', array('type' => 'text', 'class' => 'employment')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*学部・学科・専攻名：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.99.faculty_name', array('type' => 'text', 'class' => 'department')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">入学年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.99.admission_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">卒業(退学･除籍)年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('CollegeHistory.99.graduation_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addCollegeHistoryTbl()'), false); ?></div>
<div class="separator_div">&nbsp;</div>
<div class="content_title">
	【　ゼミ情報　】
	<font class="caution_msg">ゼミ教員名が入力されていない情報は登録されません。</font>
</div>
<span id="CollegeSeminarList">
	<?php 
		$collegeSeminarNum = 0;
		foreach ($this->data['CollegeSeminar'] as $key => $rec) {
			if ($key == 99)	continue;
	?>
	<span id="CollegeSeminar<?php echo $collegeSeminarNum; ?>" <?php if (isset($rec['delete_flg']) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($collegeSeminarNum > 0) { ?><div class="separator_div">&nbsp;</div><?php } ?>
		<table class="student_input_tbl3">
			<tr>
				<th class="student_input_th3_1">*ゼミ教員名：</th>
				<td class="student_input_td3_1">
                    <?php echo $this->Form->input('CollegeSeminar.' . $collegeSeminarNum . '.seminar_teacher_name', array('type' => 'text', 'class' => 'name')); ?>
                    <?php echo (!empty($rec['id'])) ? $this->Form->input('CollegeSeminar.' . $collegeSeminarNum . '.id', array('type' => 'hidden')) : ''; ?>
                    <?php echo $this->Form->input('CollegeSeminar.' . $collegeSeminarNum . '.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('CollegeSeminar.' . $collegeSeminarNum . '.student_type', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td3_2" rowspan="3">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteCollegeSeminarTbl('.$collegeSeminarNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">履修年度：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('CollegeSeminar.'.$collegeSeminarNum.'.completion_year', array('type' => 'text', 'class' => 'year')); ?>年度</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">ゼミ成績：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('CollegeSeminar.'.$collegeSeminarNum.'.seminar_result', array('type' => 'text', 'class' => 'result', 'maxlength' => '1')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$collegeSeminarNum++;
		}
	?>
	<span id="CollegeSeminar99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl3">
			<tr>
				<th class="student_input_th3_1">*ゼミ教員名：</th>
				<td class="student_input_td3_1">
                    <?php echo $this->Form->input('CollegeSeminar.99.seminar_teacher_name', array('type' => 'text', 'class' => 'name')); ?>
                    <?php echo $this->Form->input('CollegeSeminar.99.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('CollegeSeminar.99.student_type', array('type' => 'hidden', 'value' => Resource::STUDENT_TYPE_COLLEGE)); ?>
				</td>
				<td class="student_input_td3_2" rowspan="3">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">履修年度：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('CollegeSeminar.99.completion_year', array('type' => 'text', 'class' => 'year')); ?>年度</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">ゼミ成績：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('CollegeSeminar.99.seminar_result', array('type' => 'text', 'class' => 'result', 'maxlength' => '1')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addCollegeSeminarTbl()'), false); ?></div>
<div class="separator_div">&nbsp;</div>
<div class="content_title">
	【　サークル情報　】
	<font class="caution_msg">サークル・部活動名が入力されていない情報は登録されません。</font>
</div>
<span id="CollegeClubList">
	<?php
		$collegeClubNum = 0;
		foreach ($this->data['CollegeClub'] as $key => $rec) { 
			if ($key == 99)	continue;
	?>
	<span id="CollegeClub<?php echo $collegeClubNum; ?>" <?php if ((isset($rec['delete_flg'])) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($collegeClubNum > 0) { ?><div class="separator_div">&nbsp;</div><?php } ?>
		<table class="student_input_tbl4">
			<tr>
				<th class="student_input_th4_1">サークル分類名：</th>
				<td class="student_input_td4_1">
                    <?php echo $this->Form->input('CollegeClub.' . $collegeClubNum . '.club_type', array('type' => 'select', 'options' => $clubType, 'empty' => true)); ?>
					<?php echo (!empty($rec['id'])) ? $this->Form->input('CollegeClub.' . $collegeClubNum.'.id', array('type' => 'hidden')) : ''; ?>
                    <?php echo $this->Form->input('CollegeClub.' . $collegeClubNum . '.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('CollegeClub.' . $collegeClubNum . '.student_type', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td4_2" rowspan="5">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteCollegeClubTbl('.$collegeClubNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th4_1">*サークル・部活動名：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('CollegeClub.'.$collegeClubNum.'.club_name', array('type' => 'text', 'class' => 'name')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">役職：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('CollegeClub.'.$collegeClubNum.'.club_post', array('type' => 'select', 'options' => $post, 'empty' => true)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">入部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('CollegeClub.'.$collegeClubNum.'.in_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">退部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('CollegeClub.'.$collegeClubNum.'.out_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$collegeClubNum++;
		}
	?>
	<span id="CollegeClub99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl4">
			<tr>
				<th class="student_input_th4_1">サークル分類名：</th>
				<td class="student_input_td4_1">
					<?php echo $this->Form->input('CollegeClub.99.club_type', array('type' => 'select', 'options' => $clubType, 'empty' => true)); ?>
					<?php echo $this->Form->input('CollegeClub.99.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('CollegeClub.99.student_type', array('type' => 'hidden', 'value' => Resource::STUDENT_TYPE_COLLEGE)); ?>
				</td>
				<td class="student_input_td4_2" rowspan="5">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th4_1">*サークル・部活動名：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('CollegeClub.99.club_name', array('type' => 'text', 'class' => 'name')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">役職：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('CollegeClub.99.club_post', array('type' => 'select', 'options' => $post, 'empty' => true)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">入部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('CollegeClub.99.in_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">退部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('CollegeClub.99.out_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addCollegeClubTbl()'), false); ?></div>
<div class="separator_div">&nbsp;</div>
<div class="subTitle">高校関連情報</div>
<div class="content_title">
	【　入学履歴　】
	<font class="caution_msg">在籍状態区分情報、卒年組・高が入力されていない情報は登録されません。</font>
</div>
<span id="HighHistoryList">
	<?php
		$highHistoryNum = 0;
		foreach ($this->data['HighHistory'] as $key => $rec) {
			if ($key == 99)	continue;
	?>
	<span id="HighHistory<?php echo $highHistoryNum; ?>" <?php if (isset($rec['delete_flg']) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($highHistoryNum != 0) echo '<div class="separator_div">&nbsp;</div>'; ?>
		<table class="student_input_tbl2">
			<tr>
				<th class="student_input_th2_1">出身校：</th>
				<td class="student_input_td2_1">
                    <?php echo $this->Form->input('HighHistory.'.$highHistoryNum.'.alma_mater_name', array('type' => 'text', 'class' => 'name')); ?>
					<?php echo (!empty($rec['id'])) ? $this->Form->input('HighHistory.'.$highHistoryNum.'.id', array('type' => 'hidden')) : ''; ?>
					<?php echo $this->Form->input('HighHistory.'.$highHistoryNum.'.delete_flg', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td2_2" rowspan="8">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteHighHistoryTbl('.$highHistoryNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
            <tr>
                <th class="student_input_th2_1">進学大学：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.' . $highHistoryNum . '.graduation_course', array('type' => 'text', 'class' => 'name')); ?></td>
            </tr>
			<tr>
				<th class="student_input_th2_1">在学番号：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.'.$highHistoryNum.'.student_no', array('type' => 'text', 'class' => 'number', 'maxlength' => '7')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">学生種別：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.'.$highHistoryNum.'.student_type', array('type' => 'select', 'options' => $studentHighType)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*在籍状態区分：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.'.$highHistoryNum.'.student_status', array('type' => 'text', 'class' => 'employment')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*卒年組・高：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.' . $highHistoryNum . '.faculty_name', array('type' => 'text', 'class' => 'department')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">入学年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.'.$highHistoryNum.'.admission_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">卒業(退学･除籍)年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.'.$highHistoryNum.'.graduation_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$highHistoryNum++;
		} 
	?>
	<span id="HighHistory99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl2">
			<tr>
				<th class="student_input_th2_1">出身校：</th>
				<td class="student_input_td2_1">
                    <?php echo $this->Form->input('HighHistory.99.alma_mater_name', array('type' => 'text', 'class' => 'name')); ?>
					<?php echo $this->Form->input('HighHistory.99.delete_flg', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td2_2" rowspan="8">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
            <tr>
                <th class="student_input_th2_1">進学大学：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.99.graduation_course', array('type' => 'text', 'class' => 'name')); ?></td>
            </tr>
			<tr>
				<th class="student_input_th2_1">在学番号：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.99.student_no', array('type' => 'text', 'class' => 'number', 'maxlength' => '7')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">学生種別：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.99.student_type', array('type' => 'select', 'options' => $studentHighType)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*在籍状態区分：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.99.student_status', array('type' => 'text', 'class' => 'employment')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*卒年組・高：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.99.faculty_name', array('type' => 'text', 'class' => 'department')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">入学年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.99.admission_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">卒業(退学･除籍)年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('HighHistory.99.graduation_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addHighHistoryTbl()'), false); ?></div>
<div class="separator_div">&nbsp;</div>
<div class="content_title">
	【　担当教員情報　】
	<font class="caution_msg">担当教員名が入力されていない情報は登録されません。</font>
</div>
<span id="HighSeminarList">
	<?php 
		$highSeminarNum = 0;
		foreach ($this->data['HighSeminar'] as $key => $rec) {
			if ($key == 99)	continue;
	?>
	<span id="HighSeminar<?php echo $highSeminarNum; ?>" <?php if (isset($rec['delete_flg']) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($highSeminarNum > 0) { ?><div class="separator_div">&nbsp;</div><?php } ?>
		<table class="student_input_tbl3">
			<tr>
				<th class="student_input_th3_1">*担当教員名：</th>
				<td class="student_input_td3_1">
                    <?php echo $this->Form->input('HighSeminar.' . $highSeminarNum . '.seminar_teacher_name', array('type' => 'text', 'class' => 'name')); ?>
                    <?php echo (!empty($rec['id'])) ? $this->Form->input('HighSeminar.' . $highSeminarNum . '.id', array('type' => 'hidden')) : ''; ?>
                    <?php echo $this->Form->input('HighSeminar.' . $highSeminarNum . '.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('HighSeminar.' . $highSeminarNum . '.student_type', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td3_2" rowspan="3">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteHighSeminarTbl('.$highSeminarNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">履修年度：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('HighSeminar.'.$highSeminarNum.'.completion_year', array('type' => 'text', 'class' => 'year')); ?>年度</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">ゼミ成績：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('HighSeminar.'.$highSeminarNum.'.seminar_result', array('type' => 'text', 'class' => 'result', 'maxlength' => '1')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$highSeminarNum++;
		}
	?>
	<span id="HighSeminar99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl3">
			<tr>
				<th class="student_input_th3_1">*担当教員名：</th>
				<td class="student_input_td3_1">
                    <?php echo $this->Form->input('HighSeminar.99.seminar_teacher_name', array('type' => 'text', 'class' => 'name')); ?>
                    <?php echo $this->Form->input('HighSeminar.99.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('HighSeminar.99.student_type', array('type' => 'hidden', 'value' => Resource::STUDENT_TYPE_HIGH)); ?>
				</td>
				<td class="student_input_td3_2" rowspan="3">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">履修年度：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('HighSeminar.99.completion_year', array('type' => 'text', 'class' => 'year')); ?>年度</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">ゼミ成績：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('HighSeminar.99.seminar_result', array('type' => 'text', 'class' => 'result', 'maxlength' => '1')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addHighSeminarTbl()'), false); ?></div>
<div class="separator_div">&nbsp;</div>
<div class="content_title">
	【　クラブ・部活動情報　】
	<font class="caution_msg">クラブ・部活動名が入力されていない情報は登録されません。</font>
</div>
<span id="HighClubList">
	<?php
		$highClubNum = 0;
		foreach ($this->data['HighClub'] as $key => $rec) { 
			if ($key == 99)	continue;
	?>
	<span id="HighClub<?php echo $highClubNum; ?>" <?php if ((isset($rec['delete_flg'])) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($highClubNum > 0) { ?><div class="separator_div">&nbsp;</div><?php } ?>
		<table class="student_input_tbl4">
			<tr>
				<th class="student_input_th4_1">サークル分類名：</th>
				<td class="student_input_td4_1">
                    <?php echo $this->Form->input('HighClub.' . $highClubNum . '.club_type', array('type' => 'select', 'options' => $clubType, 'empty' => true)); ?>
					<?php echo (!empty($rec['id'])) ? $this->Form->input('HighClub.' . $highClubNum.'.id', array('type' => 'hidden')) : ''; ?>
                    <?php echo $this->Form->input('HighClub.' . $highClubNum . '.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('HighClub.' . $highClubNum . '.student_type', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td4_2" rowspan="5">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteHighClubTbl('.$highClubNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th4_1">*クラブ・部活動名：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('HighClub.'.$highClubNum.'.club_name', array('type' => 'text', 'class' => 'name')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">役職：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('HighClub.'.$highClubNum.'.club_post', array('type' => 'select', 'options' => $post, 'empty' => true)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">入部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('HighClub.'.$highClubNum.'.in_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">退部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('HighClub.'.$highClubNum.'.out_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$highClubNum++;
		}
	?>
	<span id="HighClub99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl4">
			<tr>
				<th class="student_input_th4_1">サークル分類名：</th>
				<td class="student_input_td4_1">
					<?php echo $this->Form->input('HighClub.99.club_type', array('type' => 'select', 'options' => $clubType, 'empty' => true)); ?>
					<?php echo $this->Form->input('HighClub.99.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('HighClub.99.student_type', array('type' => 'hidden', 'value' => Resource::STUDENT_TYPE_HIGH)); ?>
				</td>
				<td class="student_input_td4_2" rowspan="5">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th4_1">*クラブ・部活動名：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('HighClub.99.club_name', array('type' => 'text', 'class' => 'name')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">役職：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('HighClub.99.club_post', array('type' => 'select', 'options' => $post, 'empty' => true)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">入部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('HighClub.99.in_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">退部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('HighClub.99.out_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addHighClubTbl()'), false); ?></div>
<div class="separator_div">&nbsp;</div>
<div class="subTitle">中学関連情報</div>
<div class="content_title">
	【　入学履歴　】
	<font class="caution_msg">在籍状態区分情報、卒年組・中が入力されていない情報は登録されません。</font>
</div>
<span id="JuniorHistoryList">
	<?php
		$juniorHistoryNum = 0;
		foreach ($this->data['JuniorHistory'] as $key => $rec) {
			if ($key == 99)	continue;
	?>
	<span id="JuniorHistory<?php echo $juniorHistoryNum; ?>" <?php if (isset($rec['delete_flg']) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($juniorHistoryNum != 0) echo '<div class="separator_div">&nbsp;</div>'; ?>
		<table class="student_input_tbl2">
			<tr>
				<th class="student_input_th2_1">出身校：</th>
				<td class="student_input_td2_1">
                    <?php echo $this->Form->input('JuniorHistory.' . $juniorHistoryNum . '.alma_mater_name', array('type' => 'text', 'class' => 'name')); ?>
					<?php echo (!empty($rec['id'])) ? $this->Form->input('JuniorHistory.'.$juniorHistoryNum.'.id', array('type' => 'hidden')) : ''; ?>
					<?php echo $this->Form->input('JuniorHistory.'.$juniorHistoryNum.'.delete_flg', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td2_2" rowspan="8">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteJuniorHistoryTbl('.$juniorHistoryNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
            <tr>
                <th class="student_input_th2_1">進学高校：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.' . $juniorHistoryNum . '.graduation_course', array('type' => 'text', 'class' => 'name')); ?></td>
            </tr>
			<tr>
				<th class="student_input_th2_1">在学番号：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.'.$juniorHistoryNum.'.student_no', array('type' => 'text', 'class' => 'number', 'maxlength' => '7')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">学生種別：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.'.$juniorHistoryNum.'.student_type', array('type' => 'select', 'options' => $studentJuniorType)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*在籍状態区分：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.'.$juniorHistoryNum.'.student_status', array('type' => 'text', 'class' => 'employment')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*卒年組・中：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.' . $juniorHistoryNum . '.faculty_name', array('type' => 'text', 'class' => 'department')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">入学年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.'.$juniorHistoryNum.'.admission_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">卒業(退学･除籍)年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.'.$juniorHistoryNum.'.graduation_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$juniorHistoryNum++;
		} 
	?>
	<span id="JuniorHistory99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl2">
			<tr>
				<th class="student_input_th2_1">出身校：</th>
				<td class="student_input_td2_1">
                    <?php echo $this->Form->input('JuniorHistory.99.alma_mater_name', array('type' => 'text', 'class' => 'name')); ?>
					<?php echo $this->Form->input('JuniorHistory.99.delete_flg', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td2_2" rowspan="8">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
            <tr>
                <th class="student_input_th2_1">進学高校：</th>
                <td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.99.graduation_course', array('type' => 'text', 'class' => 'name')); ?></td>
            </tr>
			<tr>
				<th class="student_input_th2_1">在学番号：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.99.student_no', array('type' => 'text', 'class' => 'number', 'maxlength' => '7')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">学生種別：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.99.student_type', array('type' => 'select', 'options' => $studentJuniorType)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*在籍状態区分：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.99.student_status', array('type' => 'text', 'class' => 'employment')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">*卒年組・中：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.99.faculty_name', array('type' => 'text', 'class' => 'department')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">入学年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.99.admission_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th2_1">卒業(退学･除籍)年月日：</th>
				<td class="student_input_td2_1"><?php echo $this->Form->input('JuniorHistory.99.graduation_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addJuniorHistoryTbl()'), false); ?></div>
<div class="separator_div">&nbsp;</div>
<div class="content_title">
	【　担当教員情報　】
	<font class="caution_msg">担当教員名が入力されていない情報は登録されません。</font>
</div>
<span id="JuniorSeminarList">
	<?php 
		$juniorSeminarNum = 0;
		foreach ($this->data['JuniorSeminar'] as $key => $rec) {
			if ($key == 99)	continue;
	?>
	<span id="JuniorSeminar<?php echo $juniorSeminarNum; ?>" <?php if (isset($rec['delete_flg']) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($juniorSeminarNum > 0) { ?><div class="separator_div">&nbsp;</div><?php } ?>
		<table class="student_input_tbl3">
			<tr>
				<th class="student_input_th3_1">*担当教員名：</th>
				<td class="student_input_td3_1">
                    <?php echo $this->Form->input('JuniorSeminar.' . $juniorSeminarNum . '.seminar_teacher_name', array('type' => 'text', 'class' => 'name')); ?>
                    <?php echo (!empty($rec['id'])) ? $this->Form->input('JuniorSeminar.' . $juniorSeminarNum . '.id', array('type' => 'hidden')) : ''; ?>
                    <?php echo $this->Form->input('JuniorSeminar.' . $juniorSeminarNum . '.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('JuniorSeminar.' . $juniorSeminarNum . '.student_type', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td3_2" rowspan="3">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteJuniorSeminarTbl('.$juniorSeminarNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">履修年度：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('JuniorSeminar.'.$juniorSeminarNum.'.completion_year', array('type' => 'text', 'class' => 'year')); ?>年度</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">ゼミ成績：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('JuniorSeminar.'.$juniorSeminarNum.'.seminar_result', array('type' => 'text', 'class' => 'result', 'maxlength' => '1')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$juniorSeminarNum++;
		}
	?>
	<span id="JuniorSeminar99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl3">
			<tr>
				<th class="student_input_th3_1">*担当教員名：</th>
				<td class="student_input_td3_1">
                    <?php echo $this->Form->input('JuniorSeminar.99.seminar_teacher_name', array('type' => 'text', 'class' => 'name')); ?>
                    <?php echo $this->Form->input('JuniorSeminar.99.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('JuniorSeminar.99.student_type', array('type' => 'hidden', 'value' => Resource::STUDENT_TYPE_JUNIOR)); ?>
				</td>
				<td class="student_input_td3_2" rowspan="3">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">履修年度：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('JuniorSeminar.99.completion_year', array('type' => 'text', 'class' => 'year')); ?>年度</td>
			</tr>
			<tr>
				<th class="student_input_th3_1">ゼミ成績：</th>
				<td class="student_input_td3_1"><?php echo $this->Form->input('JuniorSeminar.99.seminar_result', array('type' => 'text', 'class' => 'result', 'maxlength' => '1')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addJuniorSeminarTbl()'), false); ?></div>
<div class="separator_div">&nbsp;</div>
<div class="content_title">
	【　クラブ・部活動情報　】
	<font class="caution_msg">クラブ・部活動名が入力されていない情報は登録されません。</font>
</div>
<span id="JuniorClubList">
	<?php
		$juniorClubNum = 0;
		foreach ($this->data['JuniorClub'] as $key => $rec) { 
			if ($key == 99)	continue;
	?>
	<span id="JuniorClub<?php echo $juniorClubNum; ?>" <?php if ((isset($rec['delete_flg'])) && ($rec['delete_flg'] == 1)) echo 'style="display: none;"'; ?>>
		<?php if ($juniorClubNum > 0) { ?><div class="separator_div">&nbsp;</div><?php } ?>
		<table class="student_input_tbl4">
			<tr>
				<th class="student_input_th4_1">サークル分類名：</th>
				<td class="student_input_td4_1">
                    <?php echo $this->Form->input('JuniorClub.' . $juniorClubNum . '.club_type', array('type' => 'select', 'options' => $clubType, 'empty' => true)); ?>
					<?php echo (!empty($rec['id'])) ? $this->Form->input('JuniorClub.' . $juniorClubNum.'.id', array('type' => 'hidden')) : ''; ?>
                    <?php echo $this->Form->input('JuniorClub.' . $juniorClubNum . '.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('JuniorClub.' . $juniorClubNum . '.student_type', array('type' => 'hidden')); ?>
				</td>
				<td class="student_input_td4_2" rowspan="5">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'deleteJuniorClubTbl('.$juniorClubNum.')', 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th4_1">*クラブ・部活動名：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('JuniorClub.'.$juniorClubNum.'.club_name', array('type' => 'text', 'class' => 'name')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">役職：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('JuniorClub.'.$juniorClubNum.'.club_post', array('type' => 'select', 'options' => $post, 'empty' => true)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">入部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('JuniorClub.'.$juniorClubNum.'.in_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">退部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('JuniorClub.'.$juniorClubNum.'.out_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
	<?php 
			$juniorClubNum++;
		}
	?>
	<span id="JuniorClub99" style="display: none;">
		<div class="separator_div">&nbsp;</div>
		<table class="student_input_tbl4">
			<tr>
				<th class="student_input_th4_1">サークル分類名：</th>
				<td class="student_input_td4_1">
					<?php echo $this->Form->input('JuniorClub.99.club_type', array('type' => 'select', 'options' => $clubType, 'empty' => true)); ?>
					<?php echo $this->Form->input('JuniorClub.99.delete_flg', array('type' => 'hidden')); ?>
                    <?php echo $this->Form->input('JuniorClub.99.student_type', array('type' => 'hidden', 'value' => Resource::STUDENT_TYPE_JUNIOR)); ?>
				</td>
				<td class="student_input_td4_2" rowspan="5">
					<?php echo $this->Html->link($this->Html->image('/img/students/deleteSmallBtn.png', array('alt' => '削除')), 'javascript:void(0)', array('escape' => false, 'tabindex' => '-1'), false); ?>
				</td>
			</tr>
			<tr>
				<th class="student_input_th4_1">*クラブ・部活動名：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('JuniorClub.99.club_name', array('type' => 'text', 'class' => 'name')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">役職：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('JuniorClub.99.club_post', array('type' => 'select', 'options' => $post, 'empty' => true)); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">入部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('JuniorClub.99.in_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
			<tr>
				<th class="student_input_th4_1">退部年月日：</th>
				<td class="student_input_td4_1"><?php echo $this->Form->input('JuniorClub.99.out_date', array('type' => 'text', 'class' => 'date')); ?></td>
			</tr>
		</table>
	</span>
</span>
<div class="content_add"><?php echo $this->Html->link($this->Html->image('/img/students/addListBtn.png', array('alt' => '追加')), 'javascript:void(0)', array('escape' => false, 'onClick' => 'addJuniorClubTbl()'), false); ?></div>
<hr class="separator" />
<div class="button_block">
	<?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
	<?php echo $this->Form->input('personal_id', array('type' => 'hidden')); ?>
	<?php echo $this->Form->submit('/img/students/updateBtn.png', array('name' => 'update', 'div' => false)); ?>
	<?php echo $this->Form->submit('/img/students/deleteBtn.png', array('name' => 'delete', 'onClick' => 'return onSubmit4Delete();', 'div' => false)); ?>
	<?php echo $this->Html->link($this->Html->image('/img/students/cancelBtn.png', array('alt' => 'キャンセル')), array('action' => 'index'), array('escape' => false), false); ?>
</div>
<script type="text/javascript">
<!--
    var supportNum = <?php echo $supportNum; ?>;
    var collegeHistoryNum = <?php echo $collegeHistoryNum; ?>;
    var collegeSeminarNum = <?php echo $collegeSeminarNum; ?>;
    var collegeClubNum = <?php echo $collegeClubNum; ?>;
    var highHistoryNum = <?php echo $highHistoryNum; ?>;
    var highSeminarNum = <?php echo $highSeminarNum; ?>;
    var highClubNum = <?php echo $highClubNum; ?>;
    var juniorHistoryNum = <?php echo $juniorHistoryNum; ?>;
    var juniorSeminarNum = <?php echo $juniorSeminarNum; ?>;
    var juniorClubNum = <?php echo $juniorClubNum; ?>;
-->
</script>
<?php echo $this->Form->end(); ?>
