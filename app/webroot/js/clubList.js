// 在籍者情報の新規登録時に所属サークルの一覧に対する処理一式
var displayClubNum;

/**
 * 大学サークルの入力テーブルを作成する
 */
function addCollegeClubTbl() {
	// セミナーの入力テーブルを取得
	club = $('#CollegeClub99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	club.attr('id', 'CollegeClub' + collegeClubNum);
	club.attr('style', 'display:');
	club.find('a:first').attr('onClick', 'deleteCollegeClubTbl(' + collegeClubNum + ')');

	// 各入力フィールドのname属性を変更
	club.find('#CollegeClub99ClubType').attr('name', 'data[CollegeClub]['+collegeClubNum+'][club_type]');
	club.find('#CollegeClub99ClubName').attr('name', 'data[CollegeClub]['+collegeClubNum+'][club_name]');
	club.find('#CollegeClub99ClubPost').attr('name', 'data[CollegeClub]['+collegeClubNum+'][club_post]');
	club.find('#CollegeClub99InDate').attr('name', 'data[CollegeClub]['+collegeClubNum+'][in_date]');
	club.find('#CollegeClub99OutDate').attr('name', 'data[CollegeClub]['+collegeClubNum+'][out_date]');
        club.find('#CollegeClub99StudentType').attr('name', 'data[CollegeClub]['+collegeClubNum+'][student_type]');
	club.find('#CollegeClub99DeleteFlg').attr('name', 'data[CollegeClub]['+collegeClubNum+'][delete_flg]');
	club.find('#CollegeClub99DeleteFlg').attr('id', 'CollegeClub'+collegeClubNum+'DeleteFlg');

	// 入力テーブルを追加
	club.appendTo('#CollegeClubList');

	// 変数を一つインクリメント
	collegeClubNum++;
}

/**
 * 大学サークルの入力テーブルを削除する
 */
function deleteCollegeClubTbl(id) {
	// 選択されたテーブルを取得
	club = $('#CollegeClub'+id);
	
	// 非表示に設定
	club.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	club.find('#CollegeClub' + id + 'DeleteFlg').val(1);
}

/**
 * 高校サークルの入力テーブルを作成する
 */
function addHighClubTbl() {
	// セミナーの入力テーブルを取得
	club = $('#HighClub99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	club.attr('id', 'HighClub' + highClubNum);
	club.attr('style', 'display:');
	club.find('a:first').attr('onClick', 'deleteHighClubTbl(' + highClubNum + ')');

	// 各入力フィールドのname属性を変更
	club.find('#HighClub99ClubType').attr('name', 'data[HighClub]['+highClubNum+'][club_type]');
	club.find('#HighClub99ClubName').attr('name', 'data[HighClub]['+highClubNum+'][club_name]');
	club.find('#HighClub99ClubPost').attr('name', 'data[HighClub]['+highClubNum+'][club_post]');
	club.find('#HighClub99InDate').attr('name', 'data[HighClub]['+highClubNum+'][in_date]');
	club.find('#HighClub99OutDate').attr('name', 'data[HighClub]['+highClubNum+'][out_date]');
        club.find('#HighClub99StudentType').attr('name', 'data[HighClub]['+highClubNum+'][student_type]');
	club.find('#HighClub99DeleteFlg').attr('name', 'data[HighClub]['+highClubNum+'][delete_flg]');
	club.find('#HighClub99DeleteFlg').attr('id', 'HighClub'+highClubNum+'DeleteFlg');

	// 入力テーブルを追加
	club.appendTo('#HighClubList');

	// 変数を一つインクリメント
	highClubNum++;
}

/**
 * 高校サークルの入力テーブルを削除する
 */
function deleteHighClubTbl(id) {
	// 選択されたテーブルを取得
	club = $('#HighClub'+id);
	
	// 非表示に設定
	club.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	club.find('#HighClub' + id + 'DeleteFlg').val(1);
}

/**
 * 中学サークルの入力テーブルを作成する
 */
function addJuniorClubTbl() {
	// セミナーの入力テーブルを取得
	club = $('#JuniorClub99').clone(true);

	// 入力テーブルの各オブジェクトの属性を変更
	club.attr('id', 'JuniorClub' + juniorClubNum);
	club.attr('style', 'display:');
	club.find('a:first').attr('onClick', 'deleteJuniorClubTbl(' + juniorClubNum + ')');

	// 各入力フィールドのname属性を変更
	club.find('#JuniorClub99ClubType').attr('name', 'data[JuniorClub]['+juniorClubNum+'][club_type]');
	club.find('#JuniorClub99ClubName').attr('name', 'data[JuniorClub]['+juniorClubNum+'][club_name]');
	club.find('#JuniorClub99ClubPost').attr('name', 'data[JuniorClub]['+juniorClubNum+'][club_post]');
	club.find('#JuniorClub99InDate').attr('name', 'data[JuniorClub]['+juniorClubNum+'][in_date]');
	club.find('#JuniorClub99OutDate').attr('name', 'data[JuniorClub]['+juniorClubNum+'][out_date]');
        club.find('#JuniorClub99StudentType').attr('name', 'data[JuniorClub]['+juniorClubNum+'][student_type]');
	club.find('#JuniorClub99DeleteFlg').attr('name', 'data[JuniorClub]['+juniorClubNum+'][delete_flg]');
	club.find('#JuniorClub99DeleteFlg').attr('id', 'JuniorClub'+juniorClubNum+'DeleteFlg');

	// 入力テーブルを追加
	club.appendTo('#JuniorClubList');

	// 変数を一つインクリメント
	juniorClubNum++;
}

/**
 * 中学サークルの入力テーブルを削除する
 */
function deleteJuniorClubTbl(id) {
	// 選択されたテーブルを取得
	club = $('#JuniorClub'+id);
	
	// 非表示に設定
	club.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	club.find('#JuniorClub' + id + 'DeleteFlg').val(1);
}
