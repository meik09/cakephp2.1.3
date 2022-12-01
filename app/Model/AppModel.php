<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	/**
	 * モデルのSaveメソッドをオーバーライドして、登録･更新の日付と担当者を入力する
	 * @param array $data Data to save.
	 * @param mixed $validate Either a boolean, or an array.
	 *   If a boolean, indicates whether or not to validate before saving.
	 *   If an array, allows control of validate, callbacks, and fieldList
	 * @param array $fieldList List of fields to allow to be written
	 * @return mixed On success Model::$data if its not empty or true, false on failure
	 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		// 保存データが空でない場合は処理を行う
		if (!empty($data)) {
			if (!isset($data['id'])) {
				// IDが宣言されていなければ登録者IDと登録者を登録
				$data['regist_date'] = date('Y/m/d H:i:s');
				$data['regist_id'] = @$_SESSION['Auth']['User']['login_id'];
			} else {
				// IDが宣言されていれば更新者IDと更新者を登録
				$data['update_date'] = date('Y/m/d H:i:s');
				$data['update_id'] = @$_SESSION['Auth']['User']['login_id'];
			}
		}
		// 親のsaveメソッドを呼び出し
		return parent::save($data, $validate, $fieldList);
	}
	
	/**
	 * 情報の更新時(saveAll)の成否を返す
	 * @param array $res 更新時の結果
	 * @return boolean 内容を調べた結果(True:成功 False:失敗)
	 */
	function isSuccess4SaveAll($result) {
		$ret = true;

		if ($result) {
			foreach ($result as $value) {
				if (strlen($value) == 0) {
					$ret = false;
					break;
				}
			}
		}
		return $ret;
	}
}
