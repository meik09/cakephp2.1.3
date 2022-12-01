/**
 * ユーザ削除時の確認
 */
function onSubmit4Delete() {
	var submitFlg = false;
	if (confirm('表示されているユーザを削除してもよろしいでしょうか？')) submitFlg = true;
	return submitFlg;
}
