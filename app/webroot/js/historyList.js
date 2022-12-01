// 在籍者情報の新規登録時に入学履歴の一覧に対する処理一式
var displayHistoryNum;
var selectNum;

/**
 * 大学入学履歴の入力テーブルを作成する
 */
function addCollegeHistoryTbl() {
	// セミナーの入力テーブルを取得
	historyBlock = $('#CollegeHistory99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	historyBlock.attr('id', 'CollegeHistory' + collegeHistoryNum);
	historyBlock.attr('style', 'display:');
	historyBlock.find('a:first').attr('onClick', 'deleteCollegeHistoryTbl(' + collegeHistoryNum + ')');

	// 各入力フィールドのname属性を変更
	historyBlock.find('#CollegeHistory99AlmaMaterName').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][alma_mater_name]');
	historyBlock.find('#CollegeHistory99GraduationCourse').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][graduation_course]');
	historyBlock.find('#CollegeHistory99DeleteFlg').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][delete_flg]');
	historyBlock.find('#CollegeHistory99StudentNo').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][student_no]');
	historyBlock.find('#CollegeHistory99StudentType').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][student_type]');
	historyBlock.find('#CollegeHistory99StudentStatus').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][student_status]');
	historyBlock.find('#CollegeHistory99FacultyName').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][faculty_name]');
	historyBlock.find('#CollegeHistory99AdmissionDate').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][admission_date]');
	historyBlock.find('#CollegeHistory99GraduationDate').attr('name', 'data[CollegeHistory]['+collegeHistoryNum+'][graduation_date]');
	historyBlock.find('#CollegeHistory99DeleteFlg').attr('id', 'CollegeHistory'+collegeHistoryNum+'DeleteFlg');

	// 入力テーブルを追加
	historyBlock.appendTo('#CollegeHistoryList');

	// 変数を一つインクリメント
	collegeHistoryNum++;
}

/**
 * 大学入学履歴の入力テーブルを削除する
 */
function deleteCollegeHistoryTbl(id) {
	// 選択されたテーブルを取得
	historyBlock = $('#CollegeHistory'+id);
	
	// 非表示に設定
	historyBlock.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	historyBlock.find('#CollegeHistory' + id + 'DeleteFlg').val(1);
}

/**
 * 高校入学履歴の入力テーブルを作成する
 */
function addHighHistoryTbl() {
	// セミナーの入力テーブルを取得
	historyBlock = $('#HighHistory99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	historyBlock.attr('id', 'HighHistory' + highHistoryNum);
	historyBlock.attr('style', 'display:');
	historyBlock.find('a:first').attr('onClick', 'deleteHighHistoryTbl(' + highHistoryNum + ')');

	// 各入力フィールドのname属性を変更
	historyBlock.find('#HighHistory99AlmaMaterName').attr('name', 'data[HighHistory]['+highHistoryNum+'][alma_mater_name]');
	historyBlock.find('#HighHistory99GraduationCourse').attr('name', 'data[HighHistory]['+highHistoryNum+'][graduation_course]');
	historyBlock.find('#HighHistory99DeleteFlg').attr('name', 'data[HighHistory]['+highHistoryNum+'][delete_flg]');
	historyBlock.find('#HighHistory99StudentNo').attr('name', 'data[HighHistory]['+highHistoryNum+'][student_no]');
	historyBlock.find('#HighHistory99StudentType').attr('name', 'data[HighHistory]['+highHistoryNum+'][student_type]');
	historyBlock.find('#HighHistory99StudentStatus').attr('name', 'data[HighHistory]['+highHistoryNum+'][student_status]');
	historyBlock.find('#HighHistory99FacultyName').attr('name', 'data[HighHistory]['+highHistoryNum+'][faculty_name]');
	historyBlock.find('#HighHistory99AdmissionDate').attr('name', 'data[HighHistory]['+highHistoryNum+'][admission_date]');
	historyBlock.find('#HighHistory99GraduationDate').attr('name', 'data[HighHistory]['+highHistoryNum+'][graduation_date]');
	historyBlock.find('#HighHistory99DeleteFlg').attr('id', 'HighHistory'+highHistoryNum+'DeleteFlg');

	// 入力テーブルを追加
	historyBlock.appendTo('#HighHistoryList');

	// 変数を一つインクリメント
	highHistoryNum++;
}

/**
 * 高校入学履歴の入力テーブルを削除する
 */
function deleteHighHistoryTbl(id) {
	// 選択されたテーブルを取得
	historyBlock = $('#HighHistory'+id);
	
	// 非表示に設定
	historyBlock.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	historyBlock.find('#HighHistory' + id + 'DeleteFlg').val(1);
}

/**
 * 中学入学履歴の入力テーブルを作成する
 */
function addJuniorHistoryTbl() {
	// セミナーの入力テーブルを取得
	historyBlock = $('#JuniorHistory99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	historyBlock.attr('id', 'JuniorHistory' + juniorHistoryNum);
	historyBlock.attr('style', 'display:');
	historyBlock.find('a:first').attr('onClick', 'deleteJuniorHistoryTbl(' + juniorHistoryNum + ')');

	// 各入力フィールドのname属性を変更
	historyBlock.find('#JuniorHistory99AlmaMaterName').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][alma_mater_name]');
	historyBlock.find('#JuniorHistory99GraduationCourse').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][graduation_course]');
	historyBlock.find('#JuniorHistory99DeleteFlg').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][delete_flg]');
	historyBlock.find('#JuniorHistory99StudentNo').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][student_no]');
	historyBlock.find('#JuniorHistory99StudentType').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][student_type]');
	historyBlock.find('#JuniorHistory99StudentStatus').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][student_status]');
	historyBlock.find('#JuniorHistory99FacultyName').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][faculty_name]');
	historyBlock.find('#JuniorHistory99AdmissionDate').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][admission_date]');
	historyBlock.find('#JuniorHistory99GraduationDate').attr('name', 'data[JuniorHistory]['+juniorHistoryNum+'][graduation_date]');
	historyBlock.find('#JuniorHistory99DeleteFlg').attr('id', 'JuniorHistory'+juniorHistoryNum+'DeleteFlg');

	// 入力テーブルを追加
	historyBlock.appendTo('#JuniorHistoryList');

	// 変数を一つインクリメント
	juniorHistoryNum++;
}

/**
 * 中学入学履歴の入力テーブルを削除する
 */
function deleteJuniorHistoryTbl(id) {
	// 選択されたテーブルを取得
	historyBlock = $('#JuniorHistory'+id);
	
	// 非表示に設定
	historyBlock.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	historyBlock.find('#JuniorHistory' + id + 'DeleteFlg').val(1);
}

/**
 * 出身校の選択ウィンドウを開く
 */
function openAlmaMaterWinow(controller, action, id) {
	// IDを保持する
	displayHistoryNum = id;
	
	// ウィンドウを開く
	openWindow(controller, action);
}

/**
 * 出身校検索ウィンドウからの値を表示する
 */
function setAlmaMater(id, name) {
	// オブジェクトに値をセット
	$('#StudentHistory'+displayHistoryNum+'AlmaMaterId').val(id);
	$('#StudentHistory'+displayHistoryNum+'AlmaMaterName').val(name);
	$('#StudentHistory'+displayHistoryNum+'DisplayAlmaMaterName').text(name);
}

/**
 * 学科のコンボボックスの内容を取得する
 */
function getSubjectList(id) {
	// 学科情報を初期化
	$('#StudentHistory'+id+'SubjectId').empty();
	$('#StudentHistory'+id+'SubjectId').append($('<option>').attr({value:''}).text('ロード中...'));
	$('#StudentHistory'+id+'SubjectId').after($('#StudentHistory'+id+'SubjectId').clone()).remove();
	
	// 専攻情報を初期化
	$('#StudentHistory'+id+'SpecialityId').empty();
	$('#StudentHistory'+id+'SpecialityId').append($('<option>').attr({value:''}).text(''));
	$('#StudentHistory'+id+'SpecialityId').after($('#StudentHistory'+id+'SpecialityId').clone()).remove();
	
	// 学部の情報を取得
	facultyId = $('#StudentHistory'+id+'FacultyId').val();
	
	// IDを保持
	selectNum = id;
	
	$.getJSON(appUrl+'/Students/getAjax/subject/' + facultyId + '/',
		function (faculties) {
			$('#StudentHistory'+selectNum+'SubjectId').empty();
			$('#StudentHistory'+selectNum+'SubjectId').append($('<option>').attr({value:''}).text(''));
			
			for (id in faculties) {
				$('#StudentHistory'+selectNum+'SubjectId').append($('<option></option>').attr({value:faculties[id].i}).text(faculties[id].n));
			}
			// IEで動作をさせるとコンボボックスをクリックして選択肢を表示した際に中身が変わらないので、一度コンボボックスを削除して作り直している
			$('#StudentHistory'+selectNum+'SubjectId').after($('#StudentHistory'+selectNum+'SubjectId').clone()).remove();
		}
	);
}
/**
 * 専攻のコンボボックスの内容を取得する
 */
function getMajorList(id) {
	// 学科情報を初期化
	$('#StudentHistory'+id+'SpecialityId').empty();
	$('#StudentHistory'+id+'SpecialityId').append($('<option>').attr({value:''}).text('ロード中...'));
	$('#StudentHistory'+id+'SpecialityId').after($('#StudentHistory'+id+'SpecialityId').clone()).remove();
	
	// 学部・学科の情報を取得
	facultyId = $('#StudentHistory'+id+'FacultyId').val();
	subjectId = $('#StudentHistory'+id+'SubjectId').val();

	// IDを保持
	selectNum = id;

	$.getJSON(appUrl+'/Students/getAjax/speciality/' + facultyId + '/' + subjectId + '/',
		function (subjects) {
			$('#StudentHistory'+selectNum+'SpecialityId').empty();
			
			for (id in subjects) {
				$('#StudentHistory'+selectNum+'SpecialityId').append($('<option>').attr({value:subjects[id].i}).text(subjects[id].n));
			}
			// IEで動作をさせるとコンボボックスをクリックして選択肢を表示した際に中身が変わらないので、一度コンボボックスを削除して作り直している
			$('#StudentHistory'+selectNum+'SpecialityId').after($('#StudentHistory'+selectNum+'SpecialityId').clone()).remove();
		}
	);
}
