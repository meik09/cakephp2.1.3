<?php
/*
 * 学生所属ゼミのモデル
 * 
 * @author Naoya.Kuga
 */
class StudentSeminar extends AppModel {
	public $name = 'StudentSeminar';
	
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
		'student_type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '学生種別を入力してください。',
				'last' => true, // 続行しない
			)
		),
		'seminar_teacher_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'ゼミ教員を入力してください。',
				'last' => true, // 続行しない
			)
		),
		'completion_year' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => '履修年度は数値で入力してください。',
				'allowEmpty' => true, // 空欄許可
				'last' => true, // 続行しない
			)
		),
        'seminar_result' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 1),
				'message' => 'ゼミ成績は1文字以内で入力してください。',
				'last' => true, // 続行しない
            )
        ),
		'regist_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '登録者IDを入力してください。',
				'last' => true, // 続行しない
			)
		),
		'regist_date' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '登録日を入力してください。',
				'last' => true, // 続行しない
			)
		)
	);
	
	/**
	 * 新規登録時の初期値を返す
	 * @return array 所属ゼミ情報の初期値 
	 */
	public function getDefault() {
		// デフォルト値を入力した配列を作成
		$rec = array();
		$rec[0]['seminar_teacher_name'] = '';
		$rec[0]['completion_year'] = '';
		$rec[0]['seminar_result'] = '';
		$rec[0]['delete_flg'] = '';
		
		return $rec;
	}

	/**
	 * 新規登録時の保存モデルを返す
	 * @param int $id 学生ID
	 * @param array $data1 入力された内容(大学)
	 * @param array $data2 入力された内容(高校)
	 * @param array $data3 入力された内容(中学)
	 * @return array 保存する情報を返す
	 */
	public function getSaveModels4Add($id, $data1, $data2, $data3) {
        // 各情報分処理
		$model = array();
        for ($i = 1; $i <= 3; $i++) {
            $name = "data" . $i;
            
            // 情報を保存
            foreach ($$name as $key => $rec) {
                // $keyが｢99｣とdelete_flgが｢1｣とseminar_teacher_idが空の内容は無視する
                if (($key != 99) && ($rec['delete_flg'] != 1) && (!empty($rec['seminar_teacher_name']))) {
                    $rec['student_id'] = $id;
                    $rec['regist_id'] = @$_SESSION['Auth']['User']['login_id'];
                    $rec['regist_date'] = date('Y/m/d H:i:s');
                    $rec['update_id'] = @$_SESSION['Auth']['User']['login_id'];
                    $rec['update_date'] = date('Y/m/d H:i:s');
                    $model[] = $rec;
                }
            }
        }

		return $model;
	}
	
	/**
	 * 更新時の保存モデルを返す 
	 * @param int $id 学生ID
	 * @params array $data1 入力された内容(大学)
	 * @params array $data2 入力された内容(高校)
	 * @params array $data3 入力された内容(中学)
	 * @param array $recs 入力された内容
	 * @return array 保存する情報を返す
	 */
	public function getSaveModel4Edit($id, $data1, $data2, $data3) {
        // 各情報分処理する
        $model = array();
        for ($i = 1; $i <= 3; $i++) {
            $name = "data" . $i;
            
            // 入力内容から保存するモデルを作成
            foreach ($$name as $key => $rec) {
                // $keyが｢99｣とdelete_flgが｢1｣とseminar_teacher_idが空の内容は無視する
                if (($key != 99) && ($rec['delete_flg'] != 1) && (!empty($rec['seminar_teacher_name']))) {
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
        }
		
		return $model;
	}
	
	/**
	 * 編集時に削除された情報のIDを配列で返す
	 * @params array $data1 入力された内容(大学)
	 * @params array $data2 入力された内容(高校)
	 * @params array $data3 入力された内容(中学)
	 * @return array 削除するレコードIDの配列
	 */
	public function getIds4Delete($data1, $data2, $data3) {
        // 各情報分処理
        $deleteIds = array();
        for ($i = 1; $i <= 3; $i++) {
            $name = "data" . $i;
            
       		// 入力された内容から削除するレコードのIDを返す
            foreach ($$name as $key => $rec) {
                // delete_flgが｢1｣でIDが空の場合にはIDを保持する
                if (($rec['delete_flg'] == 1) && (isset($rec['id']))) $deleteIds[] = $rec['id'];
            }
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
			if (isset($results[$i]['CollegeSeminar']['regist_date'])) $results[$i]['CollegeSeminar']['regist_date'] = str_replace ('-', '/', $results[$i]['CollegeSeminar']['regist_date']);
			if (isset($results[$i]['CollegeSeminar']['update_date'])) $results[$i]['CollegeSeminar']['update_date'] = str_replace ('-', '/', $results[$i]['CollegeSeminar']['update_date']);
			
            if (isset($results[$i]['HighSeminar']['regist_date'])) $results[$i]['HighSeminar']['regist_date'] = str_replace ('-', '/', $results[$i]['HighSeminar']['regist_date']);
			if (isset($results[$i]['HighSeminar']['update_date'])) $results[$i]['HighSeminar']['update_date'] = str_replace ('-', '/', $results[$i]['HighSeminar']['update_date']);

            if (isset($results[$i]['JuniorSeminar']['regist_date'])) $results[$i]['JuniorSeminar']['regist_date'] = str_replace ('-', '/', $results[$i]['JuniorSeminar']['regist_date']);
			if (isset($results[$i]['JuniorSeminar']['update_date'])) $results[$i]['JuniorSeminar']['update_date'] = str_replace ('-', '/', $results[$i]['JuniorSeminar']['update_date']);
		}

		// 値を返す
		return $results;
	}

}

?>
