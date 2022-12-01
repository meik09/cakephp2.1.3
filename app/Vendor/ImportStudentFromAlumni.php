<?php
/**
 * 在籍者の基本情報と入学情報を取り込む
 * (情報の提供先が同窓会の物を取り込む)
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Vendor', 'AbstractImporter');

class ImportStudentFromAlumni extends AbstractImporter {
	// 変数定義
	private $studentDao;
	private $historyDao;
	
	private $studentTypeList;
	private $studentStatusList;
	private $sexList;
	private $graduationJudgeList;
	private $almaMaterList;
	
	private $registTypeStudent;
	private $registTypeHistory;
	private $lineCnt = 1;
	
	// 定数定義
	const LOG_FILE = 'student_alumni_';
	const COLUMN_NUM = 35;

	const REGIST_NONE = 0;
	const REGIST_STUDENT_INSERT = 1;
	const REGIST_STUDENT_UPDATE = 2;
	const REGIST_HISTORY_INSERT = 3;
	const REGIST_HISTORY_UPDATE = 4;
	
	// コンストラクタ
	public function __construct() {
		// DAOクラスを生成
		$this->studentDao = ClassRegistry::init('Student');
		$this->historyDao = ClassRegistry::init('StudentHistory');
		
		// リソースの値を保持
		$resourceDao = ClassRegistry::init('Resource');
		$this->studentTypeList = $resourceDao->getResourceList(Resource::STUDENT_TYPE);
		$this->studentStatusList = $resourceDao->getResourceList(Resource::STUDENT_STATUS);
		$this->sexList = $resourceDao->getResourceList(Resource::SEX);
		$this->graduationJudgeList = $resourceDao->getResourceList(Resource::GRADUATION_JUDGE);
		
		// 出身校の値を保持
		$almaMaterDao = ClassRegistry::init('AlmaMater');
		$this->almaMaterList = $almaMaterDao->getHashList();
		
		// 登録タイプは｢登録しない｣で登録
		$this->registTypeStudent = self::REGIST_NONE;
		$this->registTypeHistory = self::REGIST_NONE;
	}
	
	/**
	 * 在籍者の情報を取り込む 
	 */
	public function import($files) {
		// テンポラリのファイル名を取得
		$tmpName = $files['tmp_name'];
		
		// ファイルをオープン
		if (($fp = fopen($tmpName, 'r')) !== FALSE) {
			// 実行時間を無制限にする
			set_time_limit(0);
			
			// 1行単位に処理を行う
			while (($line = fgets($fp, 10000))) {
				// エンコードを変更して配列に保持
				$data = parent::trimData(str_getcsv(mb_convert_encoding($line, 'utf-8', 'sjis-win')));

				// インポートデータとDBのデータを比較
				if ($this->isRegist($data)) {
					
					// 入力データのチェック
					if ($this->checkData($data)) {
						// 保存開始
						$this->studentDao->begin();
						$saveFlg = true;

						// 在籍者情報の登録
						if ($this->registTypeStudent != self::REGIST_NONE) {
							// 値をモデルにセット
							$studentModel = $this->setStudentData($data);
							$this->studentDao->set($studentModel);
							
							// 在籍者情報のバリデーションチェック
							if ($this->studentDao->validates()) {
								// 在籍者情報を保存
								$this->studentDao->primaryKey = 'id';
								$saveFlg = $this->studentDao->save($studentModel, false);

								if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : 在籍者情報('.$data[4].')の保存時にエラーが発生しました。', self::LOG_FILE);
							} else {
								// バリデーションエラーをセット
								parent::writeLog4Validation('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : ', $this->studentDao->validationErrors, self::LOG_FILE);
								$saveFlg = false;
							}
						}

						// 入学履歴の登録
						if ($saveFlg && $this->registTypeHistory != self::REGIST_NONE) {
							// 値をモデルにセット
							$historyModel = $this->setHistoryData($data);
							$this->historyDao->set($historyModel);

							// 履歴情報のバリデーションチェック
							if ($this->historyDao->validates()) {
								// 履歴情報を保存
								$saveFlg = $this->historyDao->save($historyModel, false);

								if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : 入学履歴情報('.$data[4].')の保存時にエラーが発生しました。', self::LOG_FILE);
							} else {
								// バリデーションエラーをセット
								parent::writeLog4Validation('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : ', $this->historyDao->validationErrors, self::LOG_FILE);
								$saveFlg = false;
							}
						}
						
						// トランザクション修了
						if ($saveFlg) {
							$this->studentDao->commit();
						} else {
							$this->studentDao->rollback();
						}
					}
				}
				$this->lineCnt++;
			}
			
			// ファイルをクローズ
			fclose($fp);
		} else {
			// ファイルのオープンに失敗
			parent::writeLog('インポートファイルのオープンに失敗しました。', self::LOG_FILE);
		}
		
		return !$this->errFlg;
	}
	
	/**
	 * インポートデータとDBのデータを比較して登録するかをチェック
	 * @param array $data インポートされたデータ
	 * @return boolean true:登録する false:登録しない
	 */
	private function isRegist(&$data) {
		// カラム数が一致していなかったら処理しない
		if (count($data) != self::COLUMN_NUM) {
			parent::writeLog('Line '.$this->lineCnt.' : 項目数が一致していません。', self::LOG_FILE);
			return false;
		}
		
		// インポートデータから個人IDを取得
		$personalId = $data[4];
		
		// 個人IDがあればDBより値を取得
		if (!empty($personalId)) {
			// 在籍者の件数を取得
			if ($this->studentDao->find('count', array('conditions' => array('personal_id' => $personalId))) > 0) {
				// 入学履歴のレコードを全取得
				$historyDto = $this->historyDao->find('all', array('conditions' => array('personal_id' => $personalId), 'order' => array('admission_date' => 'desc')));
				
				// 入学履歴のデータで｢退学・除籍・卒業(修了)等｣が存在した場合は在籍者情報を更新しない
				$this->registTypeStudent = self::REGIST_STUDENT_UPDATE;
				foreach ($historyDto as $dto) {
					if (($dto['StudentHistory']['student_status'] == Resource::STUDENT_STATUS_CHANGE)
						or ($dto['StudentHistory']['student_status'] == Resource::STUDENT_STATUS_RESIGN)
						or ($dto['StudentHistory']['student_status'] == Resource::STUDENT_STATUS_REMOVAL)
						or ($dto['StudentHistory']['student_status'] == Resource::STUDENT_STATUS_GRADUATION)) {
						$this->registTypeStudent = self::REGIST_NONE;
						break;
					}
				}
				
				// 入学履歴は常に新規登録する
				// 在学番号が空白のデータが存在するため重複なのか履歴なのかの判断がつかないため
				$this->registTypeHistory = self::REGIST_HISTORY_INSERT;

				// 入学履歴は常に新規登録なのでtrueのみ
				return true;
			} else {
				// DBに値が無ければ全項目を登録する
				$this->registTypeStudent = self::REGIST_STUDENT_INSERT;
				$this->registTypeHistory = self::REGIST_HISTORY_INSERT;
				return true;
			}
		} else {
			// 個人IDが無ければLOGに書き出す
			parent::writeLog('Line '.$this->lineCnt.' : 個人IDが無いデータが存在します。', self::LOG_FILE);
			return false;
		}
	}
	
	/**
	 * 入力されているデータが正しいかチェックする
	 * @param array $data インポートされたデータ
	 * @return boolean true:正 false:不正
	 */
	private function checkData(&$data) {
		// 返り値
		$result = true;
		
		// 学生分類区分
		if (strlen($data[2]) > 0) {
			if (!isset($this->studentTypeList[$data[2]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : 学生分類区分のデータ('.$data[2].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
		}
		// 学籍状態区分情報
		if (strlen($data[0]) > 0) {
			if (!isset($this->studentStatusList[$data[0]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : 学籍状態区分情報のデータ('.$data[0].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
		}
		// 性別
		if (strlen($data[9]) > 0) {
			if (!isset($this->sexList[$data[9]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : 性別のデータ('.$data[9].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
		}
		// 卒業判定区分
		if (strlen($data[24]) > 0) {
			if (!isset($this->graduationJudgeList[$data[24]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : 卒業判定区分のデータ('.$data[24].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
		}
		// 出身校
		if (strlen($data[33]) > 0) {
			if (!isset($this->almaMaterList[$data[33]])) {
				// 出身校のIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[4].') : 出身校のデータ('.$data[33].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
		}
		
		return $result;
	}
	
	/**
	 * 在籍者の連想配列に値をセット
	 * 初期値のデータも一緒にセット
	 * @param array $data インポートされたデータ
	 * @return array 連想配列にセットされたデータ
	 */
	private function setStudentData(&$data) {
		// 在籍者の配列に値をセット
		$model = array();
		
		// 登録モードに応じた配列を返す
		if ($this->registTypeStudent == self::REGIST_STUDENT_INSERT) {
			// 値をセット
			$this->studentDao->create();
			$model['personal_id'] = $data[4];
			$model['sex'] = $data[9];
			$model['birthday'] = $data[18];
			$model['payment_flag'] = 0;
			$model['sending_flag'] = 0;
			$model['deliver_ng_flag'] = 0;
			$model['alive_status'] = 0;
		} else if ($this->registTypeStudent == self::REGIST_STUDENT_UPDATE) {
			// 新規ではない場合はDBから値を取得
			$this->studentDao->recursive = -1;
			$rec = $this->studentDao->findByPersonalId($data[4]);
			
			$model['id'] = $rec['Student']['id'];
		}
		$model['name_kana'] = $data[6];
		$model['name'] = $data[7];
		$model['name_ext'] = $data[8];
		$model['zip'] = $data[10];
		$model['address1'] = $data[11];
		$model['address2'] = $data[12];
		$model['address3'] = $data[13];
		$model['tel'] = $data[14];
		$model['mobile_phone'] = $data[15];
		$model['mail_address_pc'] = $data[16];
		$model['mail_address_mp'] = $data[17];
		$model['guarantor_name'] = $data[26];
		$model['guarantor_zip'] = $data[27];
		$model['guarantor_address1'] = $data[28];
		$model['guarantor_address2'] = $data[29];
		$model['guarantor_address3'] = $data[30];
		$model['guarantor_tel'] = $data[31];
		$model['employment_info'] = $data[34];
		
		return $model;
	}
	
	/**
	 * 入学履歴の連想配列に値をセット
	 * 初期値のデータも一緒にセット
	 * @param array $data インポートされたデータ
	 * @return array 連想配列にセットされたデータ
	 */
	private function setHistoryData(&$data) {
		// 入学履歴の配列に値をセット
		$model = array();
		
		// 新規登録しかないのでデータを作成
		// 値をセット
		$this->historyDao->create();
		$model['personal_id'] = $data[4];
		$model['student_no'] = $data[5];
		$model['faculty_id'] = parent::getFacultyId($data[19], $data[20], $data[21]);
		$model['student_type'] = $data[2];
		$model['student_status'] = $data[0];
		$model['admission_date'] = $data[22];
		$model['graduation_date'] = $data[23];
		$model['graduation_judge'] = $data[24];
		$model['alma_mater_id'] = $data[33];
		
		return $model;
	}
}
?>
