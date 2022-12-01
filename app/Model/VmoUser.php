<?php
/**
 * ユーザ一覧の検索条件仮想モデル
 * @author N.Kuga
 */
class VmoUser extends AppModel {
	public $name = 'VmoUser';
	public $useTable = false;	// テーブルを使用しない
	public $_schema = array(
		'personnel_no' => array('type' => 'string')
		, 'name' => array('type' => 'string')
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
		$data = $this->data['VmoUser'];
		
		// 検索条件を生成
		$condition = array();
		if (strlen($data['personnel_no'])) $condition['personnel_no'] = $data['personnel_no'];
		if (strlen($data['name'])) $condition['name like'] = '%' . $data['name'] . '%';
		
		return $condition;
	}
}
?>
