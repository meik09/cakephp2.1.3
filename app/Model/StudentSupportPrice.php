<?php
/*
 * 学生賛助金履歴のモデル
 * 
 * @author Naoya.Kuga
 */
class StudentSupportPrice extends AppModel {
    public $name = 'StudentSupportPrice';
	
	var $validate = array(
		'id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'IDを入力してください。',
				'last' => true, // 続行しない
			)
		),
		'student_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '学生IDを入力してください。',
				'last' => true, // 続行しない
			)
		),
		'year' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => '賛助金履歴の年度は数値で入力してください。',
				'last' => true, // 続行しない
			)
		),
		'price' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => '賛助金履歴の金額は数値で入力してください。',
				'last' => true, // 続行しない
			)
		),
	);

    /**
	 * 新規登録時の初期値を返す
	 * @return array 賛助金履歴の初期値 
	 */
	public function getDefault() {
		// デフォルト値を入力した配列を作成
		$rec = array();
		$rec[0]['year'] = '';
		$rec[0]['price'] = '';
		$rec[0]['delete_flg'] = '';
		
		return $rec;
	}
    
    /**
	 * 新規登録時の保存モデルを返す
	 * @param int $id 学生ID
	 * @param array $data 入力された内容(大学)
	 * @return array 保存する情報を返す
	 */
	public function getSaveModels4Add($id, $data) {
        // 情報を保存
		$model = array();
        foreach ($data as $key => $rec) {
            // $keyが｢99｣とdelete_flgが｢1｣とyearとpriceが空の内容は無視する
            if (($key != 99) && ($rec['delete_flg'] != 1) && (!empty($rec['year'])) && (!empty($rec['price']))) {
                $rec['student_id'] = $id;
                $rec['regist_id'] = @$_SESSION['Auth']['User']['login_id'];
                $rec['regist_date'] = date('Y/m/d H:i:s');
                $rec['update_id'] = @$_SESSION['Auth']['User']['login_id'];
                $rec['update_date'] = date('Y/m/d H:i:s');
                $model[] = $rec;
            }
        }
		
		return $model;
	}
    
    /**
	 * 更新時の保存モデルを返す 
	 * @param int $id 個人ID
	 * @params array $data 入力された内容
	 * @return array 保存する情報を返す
	 */
	public function getSaveModel4Edit($id, $data) {
        // 入力内容から保存するモデルを作成
        $model = array();
        foreach ($data as $key => $rec) {
            // $keyが｢99｣とdelete_flgが｢1｣とyearとpriceが空の内容は無視する
            if (($key != 99) && ($rec['delete_flg'] != 1) && (!empty($rec['year'])) && (!empty($rec['price']))) {
                // IDが空の場合は更新者IDと更新日に値を入れる
                if (isset($rec['id'])) {
                    $rec['update_id'] = @$_SESSION['Auth']['User']['login_id'];
                    $rec['update_date'] = date('Y/m/d H:i:s');
                } else {
                    $rec['student_id'] = $id;
                    $rec['regist_id'] = @$_SESSION['Auth']['User']['login_id'];
                    $rec['regist_date'] = date('Y/m/d H:i:s');
                    $rec['update_id'] = @$_SESSION['Auth']['User']['login_id'];
                    $rec['update_date'] = date('Y/m/d H:i:s');
                }
                $model[] = $rec;
            }
        }
		
		return $model;
	}
	
	/**
	 * 編集時に削除された情報のIDを配列で返す
	 * @params array $data1 入力された内容
	 * @return array 削除するレコードIDの配列
	 */
	public function getIds4Delete($data) {
        // 入力された内容から削除するレコードのIDを返す
        $deleteIds = array();
        foreach ($data as $key => $rec) {
            // delete_flgが｢1｣でIDが空の場合にはIDを保持する
            if (($rec['delete_flg'] == 1) && (isset($rec['id']))) $deleteIds[] = $rec['id'];
        }
		
		return $deleteIds;
	}

    /**
	 * 検索終了後のコールバックメソッド
	 * @param array $results 検索結果の配列
	 * @param boolean $primary プライマリキーの値
	 */
	public function afterFind($results, $primary = false) {
		// 日付の部分の「-」⇒「/」に変更
		for ($i = 0; $i < count($results); $i++) {
			if (isset($results[$i]['SupportPrice']['regist_date'])) $results[$i]['SupportPrice']['regist_date'] = str_replace ('-', '/', $results[$i]['SupportPrice']['regist_date']);
			if (isset($results[$i]['SupportPrice']['update_date'])) $results[$i]['SupportPrice']['update_date'] = str_replace ('-', '/', $results[$i]['SupportPrice']['update_date']);
		}

		// 値を返す
		return $results;
	}
}
