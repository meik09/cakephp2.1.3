<?php
/**
 * 文字列を操作して表示内容を返す共通メソッドを提供する
 *
 * @package app.views.helpers
 * @author	N.Kuga
 * @create	2012/08/03
 **/
class StringUtilsHelper extends Helper {

	/**
	 * 日付のフォーマットを変更して返す
	 * 例：2012-08-03 ⇒ 2012/08/03
	 * @param string $date 変換する日付
	 * @return string 変換された日付
	 */
	public function dateFormat($date) {
		return str_replace('-', '/', $date);
	}

	/**
	 * 文字列の文字数を調整して返す
	 * 文字数はmb_strlenを使用して取得している
	 * 指定された文字数には｢…｣も含まれる
	 * 
	 * @param string $str 文字数を調整する文字列
	 * @param int $len 調整する文字列長
	 * @return string 文字列長を調整した長さ
	 */
	public function mbStrAlign($str, $len) {
		// 元の文字数を取る
		$strLen = mb_strlen($str);

		// 指定文字列より長い場合は調整する
		if ($strLen > $len) $str = mb_substr($str, 0, ($len - 1)) . '…';

		return $str;
	}
}
