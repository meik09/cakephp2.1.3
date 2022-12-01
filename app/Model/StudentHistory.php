<?php
/*
 * 学生入学履歴のモデル
 * 
 * @author Naoya.Kuga
 */
App::import('Model', 'Faculty');

class StudentHistory extends AppModel {
	public $name = 'StudentHistory';
	
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
        'student_no' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 10),
				'message' => '在学番号は10文字以内で入力してください。',
				'last' => true, // 続行しない
            )
        ),
		'faculty_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '学部学科先行を入力してください。',
				'last' => true, // 続行しない
            )
		),
        'admission_date' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 10),
				'message' => '入学年月日は10文字以内で入力してください。',
				'last' => true, // 続行しない
            )
        ),
        'graduation_date' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 10),
				'message' => '卒業（退学・除籍）年月日は10文字以内で入力してください。',
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
	 * @return array 入学履歴情報の初期値 
	 */
	public function getDefault() {
		// デフォルト値を入力した配列を作成
		$rec = array();
		$rec[0]['student_no'] = '';
		$rec[0]['student_type'] = '';
		$rec[0]['student_status'] = '';
		$rec[0]['faculty_name'] = '';
		$rec[0]['admission_date'] = '';
		$rec[0]['graduation_date'] = '';
		$rec[0]['graduation_course'] = '';
		$rec[0]['alma_mater_name'] = '';
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
                // $keyが｢99｣とdelete_flgが｢1｣と学部名と学生状態区分が空の内容は無視する
                if (($key != 99) && ($rec['delete_flg'] != 1) && (!empty($rec['faculty_name'])) && (!empty($rec['student_status']))) {
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
	 * @return array 保存する情報を返す
	 */
	public function getSaveModel4Edit($id, $data1, $data2, $data3) {
        // 各情報分処理
		$model = array();
        for ($i = 1; $i <= 3; $i++) {
            $name = "data" . $i;
            
            // 入力内容から保存するモデルを作成
            foreach ($$name as $key => $rec) {
                // $keyが｢99｣とdelete_flgが｢1｣と学部名と学生状態区分が空の内容は無視する
                if (($key != 99) && ($rec['delete_flg'] != 1) && (!empty($rec['faculty_name'])) && (!empty($rec['student_status']))) {
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
     * 同窓会CSVに出力するレコードを取得
     * @param int $studentId 学生ID
     * @param array $conditions 条件一覧
     * @return array 出力するレコード
     */
    public function getAlumniDownloadList($studentId, $conditions) {
        // 学生種別の検索条件
        $studentTypes = array();
        if (isset($conditions['college_student_type'])) array_push($studentTypes, $conditions['college_student_type']);
        if (isset($conditions['grad_student_type'])) $studentTypes = array_merge($studentTypes, $conditions['grad_student_type']);
        if (isset($conditions['high_student_type'])) array_push($studentTypes, $conditions['high_student_type']);
        if (isset($conditions['junior_student_type'])) array_push($studentTypes, $conditions['junior_student_type']);
        
        // 検索条件を生成
        $where = array('student_id' => $studentId);
        if (!empty($studentTypes)) $where['student_type'] = $studentTypes;
        
        // レコードを取得
        return $this->find('all', array(
            'conditions' => $where,
            'order' => array('graduation_date' => 'asc')
        ));
    }

    /**
     * 卒業のデータが存在するかをチェック
     * @param type $id
     * @return boolean true:存在する false:存在しない
     */
    public function isExistGraduation($id) {
        // レコード件数を取得
        $count = $this->find('count', array(
            'conditions' => array(
                'student_id' => $id
                , 'student_status like' => '%卒業%'
            )
        ));
        
        return ($count > 0) ? true : false;
    }

	/**
	 * 入力された学部･学科･専攻のデータがマスタに存在するかをチェック
	 * @param array $data 学部･学科･専攻のID
	 * @return boolean true:存在している false:存在していない
	 */
	public function isExistFaculty($data) {
		// DAOをインスタンス化
		$facultyDao = ClassRegistry::init('Faculty');
		$params['conditions'] = $data;
		
		// 取得件数が1件以上あればtrueを返す
		if ($facultyDao->find('count', $params) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 検索終了後のコールバックメソッド
	 * @param array $results 検索結果の配列
	 * @param boolean $primary プライマリキーの値
	 */
	public function afterFind($results, $primary = false) {
		// 日付の部分の「-」⇒「/」に変更
		for ($i = 0; $i < count($results); $i++) {
			if (isset($results[$i]['CollegeHistory']['admission_date'])) $results[$i]['CollegeHistory']['admission_date'] = str_replace ('-', '/', $results[$i]['CollegeHistory']['admission_date']);
			if (isset($results[$i]['CollegeHistory']['graduation_date'])) $results[$i]['CollegeHistory']['graduation_date'] = str_replace ('-', '/', $results[$i]['CollegeHistory']['graduation_date']);
			if (isset($results[$i]['CollegeHistory']['regist_date'])) $results[$i]['CollegeHistory']['regist_date'] = str_replace ('-', '/', $results[$i]['CollegeHistory']['regist_date']);
			if (isset($results[$i]['CollegeHistory']['update_date'])) $results[$i]['CollegeHistory']['update_date'] = str_replace ('-', '/', $results[$i]['CollegeHistory']['update_date']);

            if (isset($results[$i]['HighHistory']['admission_date'])) $results[$i]['HighHistory']['admission_date'] = str_replace ('-', '/', $results[$i]['HighHistory']['admission_date']);
			if (isset($results[$i]['HighHistory']['graduation_date'])) $results[$i]['HighHistory']['graduation_date'] = str_replace ('-', '/', $results[$i]['HighHistory']['graduation_date']);
			if (isset($results[$i]['HighHistory']['regist_date'])) $results[$i]['HighHistory']['regist_date'] = str_replace ('-', '/', $results[$i]['HighHistory']['regist_date']);
			if (isset($results[$i]['HighHistory']['update_date'])) $results[$i]['HighHistory']['update_date'] = str_replace ('-', '/', $results[$i]['HighHistory']['update_date']);

            if (isset($results[$i]['JuniorHistory']['admission_date'])) $results[$i]['JuniorHistory']['admission_date'] = str_replace ('-', '/', $results[$i]['JuniorHistory']['admission_date']);
			if (isset($results[$i]['JuniorHistory']['graduation_date'])) $results[$i]['JuniorHistory']['graduation_date'] = str_replace ('-', '/', $results[$i]['JuniorHistory']['graduation_date']);
			if (isset($results[$i]['JuniorHistory']['regist_date'])) $results[$i]['JuniorHistory']['regist_date'] = str_replace ('-', '/', $results[$i]['JuniorHistory']['regist_date']);
			if (isset($results[$i]['JuniorHistory']['update_date'])) $results[$i]['JuniorHistory']['update_date'] = str_replace ('-', '/', $results[$i]['JuniorHistory']['update_date']);
        }

		// 値を返す
		return $results;
	}

}
?>
