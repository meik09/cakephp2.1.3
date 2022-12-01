<?php
/*
 * リソース情報のモデル
 *
 * @author Naoya.Kuga
 */
class Resource extends AppModel {

    public $name = 'Resource';

	// 定数定義
	// リソースグループコード
	const SEX = 1;
	const FEE_PAYMENT = 2;
    const SUPPORT_PAYMENT = 3;
	const INVITATION_SENDING = 4;
	const INVITATION_DELIVERY = 5;
	const ALIVE_INFO = 6;
	const STUDENT_TYPE = 7;
	const CLUB_TYPE = 8;
	const CLUB_POST = 9;
	const UPLOAD_TYPE = 10;
    const BASE_DATA = 11;

    // 性別
	const SEX_MALE = '1';
	const SEX_FEMALE = '2';

	// 同窓会費納付
	const FEE_PAYMENT_PAID = '1';
	const FEE_PAYMENT_UNPAID = '0';

    // 大学賛助金・寄付
    const SUPPORT_PAYMENT_PAID = '1';
    const SUPPORT_PAYMENT_UNPAID = '0';

	// 案内状送付可否
	const INVITATION_SENDING_OK = '0';
	const INVITATION_SENDING_NG = '1';

	// 案内状不着
	const INVITATION_DELIVERY_OK = '0';
	const INVITATION_DELIVERY_NG = '1';

	// 生存情報
	const ALIVE_INFO_OK = '0';
	const ALIVE_INFO_NG = '1';

    // 学生種別
    const STUDENT_TYPE_JUNIOR = '80';
    const STUDENT_TYPE_HIGH = '90';
	const STUDENT_TYPE_COLLEGE = '0';
	const STUDENT_TYPE_GRADUATE = '1';
	const STUDENT_TYPE_MASTER = '2';
	const STUDENT_TYPE_DOCTOR = '3';
	const STUDENT_TYPE_SPECIAL = '6';
	const STUDENT_TYPE_LAW_SCHOOL = 'A';

	// サークル分類
	const CLUB_TYPE_ATHLETIC = 1;
	const CLUB_TYPE_CULTURE = 2;
	const CLUB_TYPE_ATHLETIC_FANS = 3;
	const CLUB_TYPE_CULTURE_FANS = 4;
	const CLUB_TYPE_OTHER = 5;

	// サークル役職
	const CLUB_POST_CAPTAIN = 'A';
	const CLUB_POST_SUB_CAPTAIN = 'B';
	const CLUB_POST_SECRETARY = 'C';
	const CLUB_POST_SUB_SECRETARY = 'D';
	const CLUB_POST_ACCOUNTS = 'E';
	const CLUB_POST_SPECIALIST = 'F';
	const CLUB_POST_CHAIRMAN = 'G';
	const CLUB_POST_SUB_CHAIRMAN = 'H';
	const CLUB_POST_NEGOTIATOR = 'I';
	const CLUB_POST_AFFAIR = 'J';
	const CLUB_POST_OTHER = 'K';
	const CLUB_POST_MANAGER = 'M';
	const CLUB_POST_DIRECTION = 'U';
	const CLUB_POST_CHIEF_DIRECTOR = 'V';
	const CLUB_POST_SUB_DIRECTOR = 'W';
	const CLUB_POST_SUSPEND = 'X';
	const CLUB_POST_NOTHING = 'Z';

	// アップロード対象
	const UPLOAD_TYPE_STUDENT = 1;
	const UPLOAD_TYPE_SEMINAR = 2;
	const UPLOAD_TYPE_CLUB = 3;
    const UPLOAD_TYPE_JH_STUDENT = 4;

    // ベースアップロードデータ
    const BASE_DATA_ALUMNI = 1;
    const BASE_DATA_ADD_JINIOR_HIGH = 2;
    const BASE_DATA_COLLEGE = 3;
    const BASE_DATA_NEW_COLLEGE = 4;
    const BASE_DATA_ADD_SEMINAR = 5;
    const BASE_DATA_CLUB = 6;

    var $validate = array(
		'id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'IDを入力してください。',
				'last' => true, // 続行しない
			)
		),
		'group_code' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'リソースグループコードを入力してください。',
				'last' => true, // 続行しない
			)
		),
		'group_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'リソースグループ名を入力してください。',
				'last' => true, // 続行しない
			)
		),
		'resource_code' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'リソースコードを入力してください。',
				'last' => true, // 続行しない
			)
		),
		'resource_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'リソース名を入力してください。',
				'last' => true, // 続行しない
			)
		),
		'sort' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '並び順を入力してください。',
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
	 * パラメタのグループのアイテムを返す
	 * @param string $groupCd リソースグループコード
	 * @return array リソースの配列
	 */
	public function getResourceList($groupCd) {
		// 各種条件を生成
		$params = array();
		$params['conditions'] = array('group_code' => $groupCd);
		$params['order'] = array('sort' => 'asc');
		$params['fields'] = array('resource_code', 'resource_name');
		
		// 値を取得
		return $this->find('list', $params);
	}
    
    /**
     * 学生種別を大学、高校、中学別に取得
     * @param int $type 80:中学 90:高校 0:大学
     * @return array 学生種別
     */
    public function getStudentType($type) {
        // 検索条件を生成
        $condition = array('group_code' => self::STUDENT_TYPE);
        switch ($type) {
            case self::STUDENT_TYPE_COLLEGE:
                $condition['NOT'] = array('resource_code' => array(self::STUDENT_TYPE_HIGH, self::STUDENT_TYPE_JUNIOR));
                break;
            case self::STUDENT_TYPE_HIGH:
                $condition['resource_code'] = self::STUDENT_TYPE_HIGH;
                break;
            case self::STUDENT_TYPE_JUNIOR:
                $condition['resource_code'] = self::STUDENT_TYPE_JUNIOR;
                break;
        }
        
		// 各種条件を生成
		$params = array();
		$params['conditions'] = $condition;
		$params['order'] = array('sort' => 'asc');
		$params['fields'] = array('resource_code', 'resource_name');
		
		// 値を取得
		return $this->find('list', $params);        
    }
}
?>
