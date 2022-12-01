<?php
/**
 * 在籍者一覧の検索条件仮想モデル
 * @author N.Kuga
 */
class VmoStudent extends AppModel {
	public $name = 'VmoStudent';
	public $useTable = false;	// テーブルを使用しない
	public $_schema = array(
        'college' => array('type' => 'integer')
        , 'gradSchool' => array('type' => 'integer')
        , 'highSchool' => array('type' => 'integer')
        , 'juniorHigh' => array('type' => 'integer')
		, 'name' => array('type' => 'string')
		, 'address' => array('type' => 'string')
		, 'birthday' => array('type' => 'string')
		, 'areaBranch' => array('type' => 'string')
		, 'jobBranch' => array('type' => 'string')
		, 'groupName' => array('type' => 'string')
		, 'sending' => array('type' => 'integer')
		, 'alive' => array('type' => 'integer')
		, 'studentNo' => array('type' => 'string')
        , 'studentStatus' => array('type' => 'integer')
		, 'graduationDate' => array('type' => 'string')
		, 'teacherName' => array('type' => 'string')
		, 'clubName' => array('type' => 'string')
		, 'hsFacultyName' => array('type' => 'string')
		, 'hsTeacherName' => array('type' => 'string')
		, 'hsClubName' => array('type' => 'string')
		, 'jhFacultyName' => array('type' => 'string')
		, 'jhTeacherName' => array('type' => 'string')
		, 'jhClubName' => array('type' => 'string')
	);

	// 定数宣言
	private $graduate = '卒業';
	private $completion = '修了';
	private $end = '終了';

	// --バリデート定義--
	public $validate = Array(
		'birthday' => array(
			'custom' => array(
				'rule' => array('custom', '/^[0-9\/]+$/'),
				'message' => '生年月日は半角数字と半角/(スラッシュ)で入力してください。',
				'allowEmpty' => true, // 空欄許可
				'last' => true, // 続行しない
			)
		),
        'graduationDate' => array(
			'custom' => array(
				'rule' => array('custom', '/^[0-9\/]+$/'),
				'message' => '卒業年月日は半角数字と半角/(スラッシュ)で入力してください。',
				'allowEmpty' => true, // 空欄許可
				'last' => true, // 続行しない
			)
		)
	);
	
	/**
	 * 在籍者一覧の検索条件を取得する 
	 */
	public function getCondition4List(&$data) {
		// 検索条件を生成
		$condition = array();
		if (strlen($data['address']) > 0) $condition['address like'] = '%'.$data['address'].'%';
		if (strlen($data['birthday']) > 0) $condition['birthday like'] = $this->getFormatedDate($data['birthday']).'%';
		if (strlen($data['areaBranch']) > 0) $condition['area_branch_name like '] = '%'.$data['areaBranch'].'%';
		if (strlen($data['jobBranch']) > 0) $condition['job_branch_name like'] = '%'.$data['jobBranch'].'%';
		if (strlen($data['groupName']) > 0) $condition['group_name like'] = '%'.$data['groupName'].'%';
		if (strlen($data['sending']) > 0) $condition['sending_flag'] = $data['sending'];
		if (strlen($data['alive']) > 0) $condition['alive_status'] = $data['alive'];
		if (strlen($data['name']) > 0) {
			$nameCondition['or'] = array(
				'name_kana like'  => '%'.$data['name'].'%'
				, 'name like'     => '%'.$data['name'].'%'
				, 'name_ext like' => '%'.$data['name'].'%'
				, 'name_old_kana like' => '%'.$data['name'].'%'
				, 'name_old like' => '%'.$data['name'].'%'
			);
			$condition[] = $nameCondition;
		}
        // 大学条件を追加
        $findNo = array();
        $findStatus = array();
        $findDate = array();
        if ($data['college'] == 1) {
            $condition['college_student_type'] = Resource::STUDENT_TYPE_COLLEGE;
            if (strlen($data['studentNo']) > 0) $findNo['college_student_no like'] = $data['studentNo'].'%';
            if (strlen($data['studentStatus']) > 0) $findStatus['college_student_status like'] = '%'.$data['studentStatus'].'%';
            if (strlen($data['graduationDate']) > 0) $findDate['college_graduation_date like'] = $this->getFormatedDate($data['graduationDate']).'%';
            if (strlen($data['teacherName']) > 0) $condition['college_semi_seminar_teacher_name like'] = '%'.$data['teacherName'].'%';
            if (strlen($data['clubName']) > 0) $condition['college_club_club_name like'] = '%'.$data['clubName'].'%';
        }
        // 大学院条件を追加
        if ($data['gradSchool'] == 1) {
            $condition['grad_student_type'] = array(Resource::STUDENT_TYPE_GRADUATE, Resource::STUDENT_TYPE_MASTER, Resource::STUDENT_TYPE_DOCTOR, Resource::STUDENT_TYPE_SPECIAL, Resource::STUDENT_TYPE_LAW_SCHOOL);
            if (strlen($data['studentNo']) > 0) $findNo['grad_student_no like'] = $data['studentNo'].'%';
            if (strlen($data['studentStatus']) > 0) {
				if (strpos($data['studentStatus'], $this->graduate) === false) {
					$findStatus['grad_student_status like'] = '%'.$data['studentStatus'].'%';
				} else {
					$findStatus[] = array('or' => array(
						'grad_student_status like' => '%'.$data['studentStatus'].'%',
						'or' => array(
							'grad_student_status like' => '%'.$this->completion.'%',
							'or' => array(
								'grad_student_status like' => '%'.$this->end.'%'
							)
						)
					));
				}
			}
            if (strlen($data['graduationDate']) > 0) $findDate['grad_graduation_date like'] = $this->getFormatedDate($data['graduationDate']).'%';
        }
        // 大学・大学院共通条件を追加
        if (!empty($findNo)) $condition[] = array('or' => $findNo);
        if (!empty($findStatus)) $condition[] = array('or' => $findStatus);
        if (!empty($findDate))$condition[] = array('or' => $findDate);
        // 高校条件を追加
        if ($data['highSchool'] == 1) $condition['high_student_type'] = Resource::STUDENT_TYPE_HIGH;
		if (strlen($data['hsFacultyName']) > 0) $condition['high_faculty_name like'] = '%'.$data['hsFacultyName'].'%';
		if (strlen($data['hsTeacherName']) > 0) $condition['high_semi_seminar_teacher_name like'] = '%'.$data['hsTeacherName'].'%';
		if (strlen($data['hsClubName']) > 0) $condition['high_club_club_name like'] = '%'.$data['hsClubName'].'%';
        // 中学条件を追加
        if ($data['juniorHigh'] == 1) $condition['junior_student_type'] = Resource::STUDENT_TYPE_JUNIOR;
		if (strlen($data['jhFacultyName']) > 0) $condition['junior_faculty_name like'] = '%'.$data['jhFacultyName'].'%';
		if (strlen($data['jhTeacherName']) > 0) $condition['junior_semi_seminar_teacher_name like'] = '%'.$data['jhTeacherName'].'%';
		if (strlen($data['jhClubName']) > 0) $condition['junior_club_club_name like'] = '%'.$data['jhClubName'].'%';
        
		return $condition;
	}
	
	/**
	 * 検索条件の日付で月と日が一桁の場合には｢0｣で埋める
	 * @param type $day 
	 */
	private function getFormatedDate($day) {
		// 日付を分割する
		$splitDay = explode('/', $day);
		
		// 返り値を作成
		if ((isset($splitDay[1])) && (strlen($splitDay[1]) == 1)) $splitDay[1] = '0'.$splitDay[1];
		if ((isset($splitDay[2])) && (strlen($splitDay[2]) == 1)) $splitDay[2] = '0'.$splitDay[2];
		
		return implode('/', $splitDay);
	}
}
?>
