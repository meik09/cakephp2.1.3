<?php
/**
 * ゼミ教員検索の検索条件仮想モデル
 * @author N.Kuga
 */
class VmoSeminarTeacher extends AppModel {
	public $name = 'VmoSeminarTeacher';
	public $useTable = false;	// テーブルを使用しない
	public $_schema = array(
		'teacher_name' => array('type' => 'string')
	);

	// --バリデート定義--
	public $validate = Array(
		// 特になし
	);
	
	/**
	 * ユーザ一覧の検索条件を取得する 
	 */
	public function getCondition4List() {
		// 値を取得
		$data = $this->data['VmoSeminarTeacher'];
		
		// 検索条件を生成
		$condition = array();
		if (strlen($data['teacher_name'])) $condition['or'] = array(
			array('name_kana like' => '%' . $data['teacher_name'] . '%')
			, array('name like' => '%' . $data['teacher_name'] . '%')
		);
		
		return $condition;
	}
}
?>
