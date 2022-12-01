<?php
/**
 * 出身校検索の検索条件仮想モデル
 * @author N.Kuga
 */
class VmoAlmaMater extends AppModel {
	public $name = 'VmoAlmaMater';
	public $useTable = false;	// テーブルを使用しない
	public $_schema = array(
		'school_name' => array('type' => 'string')
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
		$data = $this->data['VmoAlmaMater'];
		
		// 検索条件を生成
		$condition = array();
		if (strlen($data['school_name'])) $condition['name like'] = '%' . $data['school_name'] . '%';
		
		return $condition;
	}
}
?>
