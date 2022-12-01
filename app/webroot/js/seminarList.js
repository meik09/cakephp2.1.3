// 在籍者情報の新規登録時に所属ゼミの一覧に対する処理一式
var displaySeminarNum;

/**
 * 大学セミナーの入力テーブルを作成する
 */
function addCollegeSeminarTbl() {
	// セミナーの入力テーブルを取得
	seminar = $('#CollegeSeminar99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	seminar.attr('id', 'CollegeSeminar' + collegeSeminarNum);
	seminar.attr('style', 'display:');
	seminar.find('a:first').attr('onClick', 'deleteCollegeSeminarTbl(' + collegeSeminarNum + ')');

	// 各入力フィールドのname属性を変更
	seminar.find('#CollegeSeminar99SeminarTeacherName').attr('name', 'data[CollegeSeminar]['+collegeSeminarNum+'][seminar_teacher_name]');
	seminar.find('#CollegeSeminar99DeleteFlg').attr('name', 'data[CollegeSeminar]['+collegeSeminarNum+'][delete_flg]');
	seminar.find('#CollegeSeminar99StudentType').attr('name', 'data[CollegeSeminar]['+collegeSeminarNum+'][student_type]');
	seminar.find('#CollegeSeminar99CompletionYear').attr('name', 'data[CollegeSeminar]['+collegeSeminarNum+'][completion_year]');
	seminar.find('#CollegeSeminar99SeminarResult').attr('name', 'data[CollegeSeminar]['+collegeSeminarNum+'][seminar_result]');
	seminar.find('#CollegeSeminar99DeleteFlg').attr('id', 'CollegeSeminar'+collegeSeminarNum+'DeleteFlg');

	// 入力テーブルを追加
	seminar.appendTo('#CollegeSeminarList');

	// 変数を一つインクリメント
	collegeSeminarNum++;
}

/**
 * 大学セミナーの入力テーブルを削除する
 */
function deleteCollegeSeminarTbl(id) {
	// 選択されたテーブルを取得
	seminar = $('#CollegeSeminar'+id);
	
	// 非表示に設定
	seminar.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	seminar.find('#CollgeSeminar' + id + 'DeleteFlg').val(1);
}

/**
 * 高校セミナーの入力テーブルを作成する
 */
function addHighSeminarTbl() {
	// セミナーの入力テーブルを取得
	seminar = $('#HighSeminar99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	seminar.attr('id', 'HighSeminar' + highSeminarNum);
	seminar.attr('style', 'display:');
	seminar.find('a:first').attr('onClick', 'deleteHighSeminarTbl(' + highSeminarNum + ')');

	// 各入力フィールドのname属性を変更
	seminar.find('#HighSeminar99SeminarTeacherName').attr('name', 'data[HighSeminar]['+highSeminarNum+'][seminar_teacher_name]');
	seminar.find('#HighSeminar99DeleteFlg').attr('name', 'data[HighSeminar]['+highSeminarNum+'][delete_flg]');
	seminar.find('#HighSeminar99StudentType').attr('name', 'data[HighSeminar]['+highSeminarNum+'][student_type]');
	seminar.find('#HighSeminar99CompletionYear').attr('name', 'data[HighSeminar]['+highSeminarNum+'][completion_year]');
	seminar.find('#HighSeminar99SeminarResult').attr('name', 'data[HighSeminar]['+highSeminarNum+'][seminar_result]');
	seminar.find('#HighSeminar99DeleteFlg').attr('id', 'HighSeminar'+highSeminarNum+'DeleteFlg');

	// 入力テーブルを追加
	seminar.appendTo('#HighSeminarList');

	// 変数を一つインクリメント
	highSeminarNum++;
}

/**
 * 高校セミナーの入力テーブルを削除する
 */
function deleteHighSeminarTbl(id) {
	// 選択されたテーブルを取得
	seminar = $('#HighSeminar'+id);
	
	// 非表示に設定
	seminar.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	seminar.find('#HighSeminar' + id + 'DeleteFlg').val(1);
}

/**
 * 高校セミナーの入力テーブルを作成する
 */
function addJuniorSeminarTbl() {
	// セミナーの入力テーブルを取得
	seminar = $('#JuniorSeminar99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	seminar.attr('id', 'JuniorSeminar' + juniorSeminarNum);
	seminar.attr('style', 'display:');
	seminar.find('a:first').attr('onClick', 'deleteJuniorSeminarTbl(' + juniorSeminarNum + ')');

	// 各入力フィールドのname属性を変更
	seminar.find('#JuniorSeminar99SeminarTeacherName').attr('name', 'data[JuniorSeminar]['+juniorSeminarNum+'][seminar_teacher_name]');
	seminar.find('#JuniorSeminar99DeleteFlg').attr('name', 'data[JuniorSeminar]['+juniorSeminarNum+'][delete_flg]');
	seminar.find('#JuniorSeminar99StudentType').attr('name', 'data[JuniorSeminar]['+juniorSeminarNum+'][student_type]');
	seminar.find('#JuniorSeminar99CompletionYear').attr('name', 'data[JuniorSeminar]['+juniorSeminarNum+'][completion_year]');
	seminar.find('#JuniorSeminar99SeminarResult').attr('name', 'data[JuniorSeminar]['+juniorSeminarNum+'][seminar_result]');
	seminar.find('#JuniorSeminar99DeleteFlg').attr('id', 'JuniorSeminar'+juniorSeminarNum+'DeleteFlg');

	// 入力テーブルを追加
	seminar.appendTo('#JuniorSeminarList');

	// 変数を一つインクリメント
	juniorSeminarNum++;
}

/**
 * 高校セミナーの入力テーブルを削除する
 */
function deleteJuniorSeminarTbl(id) {
	// 選択されたテーブルを取得
	seminar = $('#JuniorSeminar'+id);
	
	// 非表示に設定
	seminar.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	seminar.find('#JuniorSeminar' + id + 'DeleteFlg').val(1);
}

/**
 * セミナーの選択ウィンドウを開く
 */
function openSeminarWinow(controller, action, id) {
	// IDを保持する
	displaySeminarNum = id;
	
	// ウィンドウを開く
	openWindow(controller, action);
}

/**
 * ゼミ教員検索ウィンドウからの値を表示する
 */
function setSeminarTeacher(id, name) {
	// オブジェクトに値をセット
	$('#StudentSeminar'+displaySeminarNum+'SeminarTeacherId').val(id);
	$('#StudentSeminar'+displaySeminarNum+'SeminarTeacherName').val(name);
	$('#StudentSeminar'+displaySeminarNum+'DisplayTeacherName').text(name);
}
