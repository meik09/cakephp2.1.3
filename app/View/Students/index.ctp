<?php
/*
 * 在籍者一覧を表示
 */

// バリデーションエラーを表示
echo $this->element("validationErrors");

// CSSを読み込み
echo $this->Html->css('students');

// JSを読み込み
echo $this->Html->script('studentList.js');

// 検索テーブルのON/OFF
$collegeDisplay = ((isset($this->data["vmoStudent"]["college"])) && ($this->data["vmoStudent"]["college"] == 1)) ? "" : "none";
$collegeDisplay = ((isset($this->data["vmoStudent"]["gradSchool"])) && ($this->data["vmoStudent"]["gradSchool"] == 1)) ? "" : $collegeDisplay;
$highSchoolDisplay = ((isset($this->data["vmoStudent"]["highSchool"])) && ($this->data["vmoStudent"]["highSchool"] == 1)) ? "" : "none";
$juniorHighDisplay = ((isset($this->data["vmoStudent"]["juniorHigh"])) && ($this->data["vmoStudent"]["juniorHigh"] == 1)) ? "" : "none";
?>
<?php echo $this->Form->create('Student', array('url' => '/students/index', 'type' => 'POST', 'inputDefaults' => array('label' => false, 'div' => false, 'legend' => false, 'error' => false))); ?>
<div class="content_title">【　在籍者一覧　】</div>
<div>
    <label><?php echo $this->Form->input('vmoStudent.college', array('type' => 'checkbox', 'label' => '大学', 'id' => 'find_add_us', 'div' => false)); ?></label>
    <label><?php echo $this->Form->input('vmoStudent.gradSchool', array('type' => 'checkbox', 'label' => '大学院、法科（選科、専攻科も含む）', 'id' => 'find_add_gs', 'div' => false)); ?></label>
    <label><?php echo $this->Form->input('vmoStudent.highSchool', array('type' => 'checkbox', 'label' => '高校', 'id' => 'find_add_hs', 'div' => false)); ?></label>
    <label><?php echo $this->Form->input('vmoStudent.juniorHigh', array('type' => 'checkbox', 'label' => '中学', 'id' => 'find_add_jh', 'div' => false)); ?></label>
</div>
<table class="student_find_tbl1">
    <tr>
        <th class="student_find_th1" rowspan="2">【共通】</th>
        <th class="student_find_th2">氏名</th>
        <th class="student_find_th3">住所</th>
        <th class="student_find_th4">生年月日</th>
        <th class="student_find_th5">地域支部</th>
        <th class="student_find_th6">職域支部</th>
        <th class="student_find_th7">各種諸団体</th>
        <th class="student_find_th8">案内状送付</th>
        <th class="student_find_th9">生存情報</th>
    </tr>
    <tr>
		<td class="student_find_td2"><?php echo $this->Form->input('vmoStudent.name',     array('type' => 'text',   'class' => 'fine_name')); ?></td>
		<td class="student_find_td3"><?php echo $this->Form->input('vmoStudent.address',  array('type' => 'text',   'class' => 'find_address')); ?></td>
		<td class="student_find_td4"><?php echo $this->Form->input('vmoStudent.birthday', array('type' => 'text',   'class' => 'find_birthday')); ?></td>
		<td class="student_find_td5"><?php echo $this->Form->input('vmoStudent.areaBranch',   array('type' => 'text',   'class' => 'find_branch')); ?></td>
		<td class="student_find_td6"><?php echo $this->Form->input('vmoStudent.jobBranch',   array('type' => 'text',   'class' => 'find_branch')); ?></td>
		<td class="student_find_td7"><?php echo $this->Form->input('vmoStudent.groupName',   array('type' => 'text',   'class' => 'find_branch')); ?></td>
		<td class="student_find_td8"><?php echo $this->Form->input('vmoStudent.sending',  array('type' => 'select', 'class' => 'find_sending', 'options' => $sendingType, 'empty' => true)); ?></td>
		<td class="student_find_td9"><?php echo $this->Form->input('vmoStudent.alive',  array('type' => 'select', 'class' => 'find_sending', 'options' => $aliveType, 'empty' => true)); ?></td>
    </tr>
</table>
<table class="student_find_tbl2" style="display: <?php echo $collegeDisplay; ?>">
    <tr>
        <th class="student_find_th2_1" rowspan="2">【大学】</th>
        <th class="student_find_th2_2">在学番号</th>
        <th class="student_find_th2_3">在籍状態区分</th>
        <th class="student_find_th2_4">卒業年月日</th>
        <th class="student_find_th2_5">ゼミ教員</th>
        <th class="student_find_th2_6">サークル名</th>
    </tr>
    <tr>
        <td class="student_find_td2_2"><?php echo $this->Form->input('vmoStudent.studentNo', array('type' => 'text', 'class' => 'find_input_small')); ?></td>
        <td class="student_find_td2_3"><?php echo $this->Form->input('vmoStudent.studentStatus',  array('type' => 'text', 'class' => 'find_input_small')); ?></td>
        <td class="student_find_td2_4"><?php echo $this->Form->input('vmoStudent.graduationDate', array('type' => 'text', 'class' => 'find_input_small')); ?></td>
        <td class="student_find_td2_5"><?php echo $this->Form->input('vmoStudent.teacherName', array('type' => 'text', 'class' => 'find_input_small')); ?></td>
        <td class="student_find_td2_6"><?php echo $this->Form->input('vmoStudent.clubName', array('type' => 'text', 'class' => 'find_input_small')); ?></td>
    </tr>
</table>
<table class="student_find_tbl3" style="display: <?php echo $highSchoolDisplay; ?>">
    <tr>
        <th class="student_find_th3_1" rowspan="2">【高校】</th>
        <th class="student_find_th3_2">卒年組</th>
        <th class="student_find_th3_3">担任教員名</th>
        <th class="student_find_th3_4">クラブ・部活動</th>
    </tr>
    <tr>
        <td class="student_find_td3_2"><?php echo $this->Form->input('vmoStudent.hsFacultyName', array('type' => 'text', 'class' => 'find_input_big')); ?></td>
        <td class="student_find_td3_3"><?php echo $this->Form->input('vmoStudent.hsTeacherName', array('type' => 'text', 'class' => 'find_input_big')); ?></td>
        <td class="student_find_td3_4"><?php echo $this->Form->input('vmoStudent.hsClubName', array('type' => 'text', 'class' => 'find_input_big')); ?></td>
    </tr>
</table>
<table class="student_find_tbl4" style="display: <?php echo $juniorHighDisplay; ?>">
    <tr>
        <th class="student_find_th4_1" rowspan="2">【中学】</th>
        <th class="student_find_th4_2">卒年組</th>
        <th class="student_find_th4_3">担任教員名</th>
        <th class="student_find_th4_4">クラブ・部活動</th>
    </tr>
    <tr>
        <td class="student_find_td4_2"><?php echo $this->Form->input('vmoStudent.jhFacultyName', array('type' => 'text', 'class' => 'find_input_big')); ?></td>
        <td class="student_find_td4_3"><?php echo $this->Form->input('vmoStudent.jhTeacherName', array('type' => 'text', 'class' => 'find_input_big')); ?></td>
        <td class="student_find_td4_4"><?php echo $this->Form->input('vmoStudent.jhClubName', array('type' => 'text', 'class' => 'find_input_big')); ?></td>
    </tr>
</table>
<div class="button_block"><?php echo $this->Form->submit('/img/students/findBtn.png', array('name' => 'find', 'value' => '検索', 'div' => 'false')); ?></div>

<?php if (!empty($recs)) { ?>
<div class="list_number_block"><?php echo $this->PaginatorEx->pagingCounters(); ?></div>
<table class="student_list_tbl1">
	<tr>
		<th class="student_list_th1">在学番号</th>
		<th class="student_list_th2">氏名</th>
		<th class="student_list_th3">性別</th>
		<th class="student_list_th4">住所</th>
		<th class="student_list_th5">生年月日</th>
        <th class="student_list_th6">ゼミ教員<br>卒業時担任・高<br>卒業時担任・中</th>
		<th class="student_list_th7">サークル名</th>
        <th class="student_list_th8">地域支部<br>職域支部</th>
		<th class="student_list_th9">案内状送付</th>
		<th class="student_list_th10">&nbsp;</th>
	</tr>
	<?php foreach ($recs as $rec) { ?>
	<tr>
		<td class="student_list_td1"><?php echo $rec['StudentAllInfo']['college_student_no']; ?></td>
		<td class="student_list_td2">
            <?php
                // 氏名(標準)がなければ氏名(外字)、それでもなければ氏名(カナ)を表示
                $name = (!empty($rec['StudentAllInfo']['name'])) ? $rec['StudentAllInfo']['name'] : $rec['StudentAllInfo']['name_ext'];
                $name = (!empty($name)) ? $name : $rec['StudentAllInfo']['name_kana'];
                echo $this->StringUtils->mbStrAlign($name, 8); 
            ?>
        </td>
		<td class="student_list_td3"><?php echo (isset($sex[$rec['StudentAllInfo']['sex']])) ? $sex[$rec['StudentAllInfo']['sex']] : ""; ?></td>
		<td class="student_list_td4"><?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['address'], 12); ?></td>
		<td class="student_list_td5"><?php echo $this->StringUtils->dateFormat($rec['StudentAllInfo']['birthday']); ?></td>
		<td class="student_list_td6">
            <?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['college_semi_seminar_teacher_name'], 7); ?><br>
            <?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['high_semi_seminar_teacher_name'], 7); ?><br>
            <?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['junior_semi_seminar_teacher_name'], 7); ?>
        </td>
		<td class="student_list_td7">
            <?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['college_club_club_name'], 7); ?><br>
            <?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['high_club_club_name'], 7); ?><br>
            <?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['junior_club_club_name'], 7); ?>
        </td>
		<td class="student_list_td8">
            <?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['area_branch_name'], 7); ?><br>
            <?php echo $this->StringUtils->mbStrAlign($rec['StudentAllInfo']['job_branch_name'], 7); ?>
        </td>
		<td class="student_list_td9"><?php echo $sendingType[$rec['StudentAllInfo']['sending_flag']]; ?></td>
		<td class="student_list_td10">
			<?php echo $this->Html->link($this->Html->image('/img/students/editBtn.png', array('alt'=>'編集')), array('controller' => 'Students', 'action' => 'edit', $rec['StudentAllInfo']['id']), array('escape' => false)); ?>
		</td>
	</tr>
	<?php } ?>
</table>
<div class="paging_block"><?php echo $this->PaginatorEx->pagingLinks(); ?></div>
<?php } ?>
<hr class="separator" />
<div class="button_block">
	<?php if (!empty($recs)) { ?>
	<div class="download_block">
		<?php echo $this->Html->link($this->Html->image('/img/students/dlStudentBtn.png', array('alt'=>'在籍者 出力')), array('controller' => 'students', 'action' => 'download', StudentsController::DOWNLOAD_STUDENT), array('escape' => false)); ?>
		<?php echo $this->Html->link($this->Html->image('/img/students/dlHistoryBtn.png', array('alt'=>'入学履歴 出力')), array('controller' => 'students', 'action' => 'download', StudentsController::DOWNLOAD_HISTORY), array('escape' => false)); ?>
		<?php echo $this->Html->link($this->Html->image('/img/students/dlSeminarBtn.png', array('alt'=>'所属ゼミ 出力')), array('controller' => 'students', 'action' => 'download', StudentsController::DOWNLOAD_SEMINAR), array('escape' => false)); ?>
		<?php echo $this->Html->link($this->Html->image('/img/students/dlClubBtn.png', array('alt'=>'所属サークル 出力')), array('controller' => 'students', 'action' => 'download', StudentsController::DOWNLOAD_CLUB), array('escape' => false)); ?>
		<?php echo $this->Html->link($this->Html->image('/img/students/diSupportBtn.png', array('alt'=>'賛助金 出力')), array('controller' => 'students', 'action' => 'download', StudentsController::DOWNLOAD_SUPPORT), array('escape' => false)); ?>
		<?php echo $this->Html->link($this->Html->image('/img/students/dlAlumniBtn.png', array('alt'=>'同窓会用 出力')), array('controller' => 'students', 'action' => 'download', StudentsController::DOWNLOAD_ALUMNI), array('escape' => false)); ?>
	</div>
	<?php } ?>
	<div class="add_block">
		<?php echo $this->Html->link($this->Html->image('/img/students/addBtn.png', array('alt'=>'新規登録')), array('controller' => 'students', 'action' => 'add'), array('escape' => false)); ?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
