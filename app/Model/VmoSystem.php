<?php
/**
 * システム管理画面の仮想モデル
 * @author N.Kuga
 */
class VmoSystem extends AppModel {
	public $name = 'VmoSystem';
	public $useTable = false;	// テーブルを使用しない
	public $_schema = array(
		'upload_type' => array('type' => 'integer')
		, 'upload_file_name' => array('type' => 'string')
	);

	// --バリデート定義--
	public $validate = Array(
		// 特になし
	);
}
