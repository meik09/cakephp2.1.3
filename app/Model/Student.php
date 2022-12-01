<?php
/*
 * 学生のモデル
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
		
class Student extends AppModel {
	public $name = 'Student';

	var $validate = array(
        'personal_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '個人IDを入力してください。',
				'last' => true, // 続行しない
			),
            'maxLength' => array(
                'rule' => array('maxLength', 10),
				'message' => '個人IDは10文字以内で入力してください。',
				'last' => true, // 続行しない
            )
		),
        'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '氏名（標準）を入力してください。',
				'last' => true, // 続行しない
			)
		),
        'birthday' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 10),
				'message' => '生年月日は10文字以内で入力してください。',
				'last' => true, // 続行しない
            )
        ),
        'zip' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 8),
				'message' => '郵便番号は8文字以内で入力してください。',
				'last' => true, // 続行しない
            )
        ),
        'zip_old' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 8),
				'message' => '旧郵便番号は8文字以内で入力してください。',
				'last' => true, // 続行しない
            )
        ),
        'guarantor_zip' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 8),
				'message' => '保証人の郵便番号は8文字以内で入力してください。',
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
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
		'CollegeSeminar' => array(
			'className' => 'StudentSeminar',
			'dependent' => false,
			'conditions' => array(
                'NOT' => array('student_type' => array(Resource::STUDENT_TYPE_JUNIOR, resource::STUDENT_TYPE_HIGH))
            ),
			'order' => array(
				'completion_year' => 'desc'
				, 'id' => 'desc'
			)
		),
		'CollegeClub' => array(
			'className' => 'StudentClub',
			'dependent' => false,
			'conditions' => array(
                'NOT' => array('student_type' => array(Resource::STUDENT_TYPE_JUNIOR, resource::STUDENT_TYPE_HIGH))
            ),
			'order' => array(
				'in_date' => 'desc'
				, 'id' => 'desc'
			)
		),
		'CollegeHistory' => array(
			'className' => 'StudentHistory',
			'dependent' => false,
			'conditions' => array(
                'NOT' => array('student_type' => array(Resource::STUDENT_TYPE_JUNIOR, resource::STUDENT_TYPE_HIGH))
            ),
			'order' => array(
				'admission_date' => 'desc'
				, 'id' => 'desc'
			)
		),
		'HighSeminar' => array(
			'className' => 'StudentSeminar',
			'dependent' => false,
			'conditions' => array(
                'student_type' => resource::STUDENT_TYPE_HIGH
            ),
			'order' => array(
				'completion_year' => 'desc'
				, 'id' => 'desc'
			)
		),
		'HighClub' => array(
			'className' => 'StudentClub',
			'dependent' => false,
			'conditions' => array(
                'student_type' => resource::STUDENT_TYPE_HIGH
            ),
			'order' => array(
				'in_date' => 'desc'
				, 'id' => 'desc'
			)
		),
		'HighHistory' => array(
			'className' => 'StudentHistory',
			'dependent' => false,
			'conditions' => array(
                'student_type' => resource::STUDENT_TYPE_HIGH
            ),
			'order' => array(
				'admission_date' => 'desc'
				, 'id' => 'desc'
			)
		),
        'JuniorSeminar' => array(
			'className' => 'StudentSeminar',
			'dependent' => false,
			'conditions' => array(
                'student_type' => resource::STUDENT_TYPE_JUNIOR
            ),
			'order' => array(
				'completion_year' => 'desc'
				, 'id' => 'desc'
			)
		),
		'JuniorClub' => array(
			'className' => 'StudentClub',
			'dependent' => false,
			'conditions' => array(
                'student_type' => resource::STUDENT_TYPE_JUNIOR
            ),
			'order' => array(
				'in_date' => 'desc'
				, 'id' => 'desc'
			)
		),
		'JuniorHistory' => array(
			'className' => 'StudentHistory',
			'dependent' => false,
			'conditions' => array(
                'student_type' => resource::STUDENT_TYPE_JUNIOR
            ),
			'order' => array(
				'admission_date' => 'desc'
				, 'id' => 'desc'
			)
		),
		'SupportPrice' => array(
			'className' => 'StudentSupportPrice',
			'dependent' => false,
			'conditions' => '',
			'order' => array(
				'year' => 'desc'
				, 'id' => 'desc'
			)
		),
	);
	
	/**
	 * 新規登録時の初期値を返す
	 * @return array 在籍者情報の初期値 
	 */
	public function getDefault() {
		// デフォルト値を入力した配列を作成
		$rec = array();
		$rec['Student']['sex'] = Resource::SEX_MALE;
		$rec['Student']['payment_flag'] = Resource::FEE_PAYMENT_PAID;
        $rec['Student']['support_flag'] = Resource::SUPPORT_PAYMENT_PAID;
		$rec['Student']['sending_flag'] = Resource::INVITATION_SENDING_OK;
		$rec['Student']['deliver_ng_flag'] = Resource::INVITATION_DELIVERY_OK;
		$rec['Student']['alive_status'] = Resource::ALIVE_INFO_OK;
		
		// 所属ゼミ、所属サークル、入学履歴も合わせて取得
        $supportDao = ClassRegistry::init('StudentSupportPrice');
		$historyDao = ClassRegistry::init('StudentHistory');
		$seminarDao = ClassRegistry::init('StudentSeminar');
		$clubDao = ClassRegistry::init('StudentClub');
        $rec['SupportPrice'] = $supportDao->getDefault();
		$rec['CollegeHistory'] = $historyDao->getDefault();
		$rec['CollegeSeminar'] = $seminarDao->getDefault();
		$rec['CollegeClub'] = $clubDao->getDefault();
		$rec['HighHistory'] = $historyDao->getDefault();
		$rec['HighSeminar'] = $seminarDao->getDefault();
		$rec['HighClub'] = $clubDao->getDefault();
		$rec['JuniorHistory'] = $historyDao->getDefault();
		$rec['JuniorSeminar'] = $seminarDao->getDefault();
		$rec['JuniorClub'] = $clubDao->getDefault();
		
		return $rec;
	}
	
	/**
	 * 検索終了後のコールバックメソッド
	 * @param array $results 検索結果の配列
	 * @param boolean $primary プライマリキーの値
	 */
	public function afterFind($results, $primary = false) {
		// 日付の部分の「-」⇒「/」に変更
		for ($i = 0; $i < count($results); $i++) {
			if (isset($results[$i]['Student']['birthday'])) $results[$i]['Student']['birthday'] = str_replace ('-', '/', $results[$i]['Student']['birthday']);
			if (isset($results[$i]['Student']['regist_date'])) $results[$i]['Student']['regist_date'] = str_replace ('-', '/', $results[$i]['Student']['regist_date']);
			if (isset($results[$i]['Student']['update_date'])) $results[$i]['Student']['update_date'] = str_replace ('-', '/', $results[$i]['Student']['update_date']);
		}

		// 値を返す
		return $results;
	}

}

?>
