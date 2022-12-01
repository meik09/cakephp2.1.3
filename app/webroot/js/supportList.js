/**
 * 賛助金履歴の入力テーブルを作成する
 */
function addSupportRow() {
	// セミナーの入力テーブルを取得
	support = $('#supportPriceTbl > tbody > tr:first').clone(true);

	// 行の属性を変更
	support.attr('style', 'display:');
        support.attr('id', 'supportPrice' + supportNum);
        support.find('a:first').attr('onClick', 'deleteSupportRow(' + supportNum + ')');

	// 各入力フィールドのname属性を変更
	support.find('#SupportPrice99Year').attr('name', 'data[SupportPrice][' + supportNum + '][year]');
	support.find('#SupportPrice99Price').attr('name', 'data[SupportPrice][' + supportNum + '][price]');
        support.find('#SupportPrice99DeleteFlg').attr('name', 'data[SupportPrice][' + supportNum + '][delete_flg]');
        support.find('#SupportPrice99DeleteFlg').attr('id', 'SupportPrice'+collegeClubNum+'DeleteFlg');

	// 入力テーブルを追加
	support.appendTo('#supportPriceTbl > tbody');

	// 変数を一つインクリメント
	supportNum++;
}

/**
 * セミナーの入力テーブルを削除する
 */
function deleteSupportRow(id) {
	// 選択されたテーブルを取得
	support = $('#supportPrice'+id);
	
	// 非表示に設定
	support.attr('style', 'display: none');
	
	// delete_flgに｢1｣を代入
	support.find('#SupportPrice' + id + 'DeleteFlg').val(1);
}
