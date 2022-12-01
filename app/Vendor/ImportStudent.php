<?php
/**
 * 在籍者の基本情報と入学情報を取り込む
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Vendor', 'AbstractImporter');

class ImportStudent extends AbstractImporter {
	// 変数定義
	private $studentDao;
	private $historyDao;
	
	private $studentTypeList;
	private $sexList;
	
	private $registTypeStudent;
	private $registTypeHistory;
	private $lineCnt = 1;
    
    private $updateStudentId = 0;
	
	// 定数定義
	const LOG_FILE = 'student_';
    const SAME_DATA_LOG_FILE = 'same_student_';
	const COLUMN_NUM = 28;

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
		$this->sexList = $resourceDao->getResourceList(Resource::SEX);
				
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

                // 保存用在籍者IDを初期化
                $this->updateStudentId = 0;
                
				// インポートデータとDBのデータを比較
				if ($this->isRegist($data)) {
					// 入力データのチェック
					if ($this->checkData($data)) {
						// 保存開始
						$this->studentDao->begin();
						$saveFlg = true;

						// 在籍者情報の登録
						$studentModel = $this->setStudentData($data);
						$this->studentDao->set($studentModel);
						
						// 在籍者情報のバリデーションチェック
						if ($this->studentDao->validates()) {
							// 在籍者情報を保存
							$this->studentDao->primaryKey = 'id';
							$saveFlg = $this->studentDao->save($studentModel, false);

							if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 在籍者情報('.$data[2].')の保存時にエラーが発生しました。', self::LOG_FILE);
                            
                            // 新規の場合は保存されたデータのIDを保存
                            if ($this->registTypeStudent == self::REGIST_STUDENT_INSERT) $this->updateStudentId = $this->studentDao->getLastInsertID();
						} else {
							// バリデーションエラーをセット
							parent::writeLog4Validation('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : ', $this->studentDao->validationErrors, self::LOG_FILE);
							$saveFlg = false;
						}

						// 入学履歴の登録
						if ($saveFlg) {
							// 値をモデルにセット
							$historyModel = $this->setHistoryData($data);
							$this->historyDao->set($historyModel);

							// 履歴情報のバリデーションチェック
							if ($this->historyDao->validates()) {
								// 履歴情報を保存
								$saveFlg = $this->historyDao->save($historyModel, false);

								if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 入学履歴情報('.$data[24].')の保存時にエラーが発生しました。', self::LOG_FILE);
							} else {
								// バリデーションエラーをセット
								parent::writeLog4Validation('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : ', $this->historyDao->validationErrors, self::LOG_FILE);
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
		
		// インポートデータから個人IDと氏名(カナ)、誕生日、学部学科専攻名を取得
		$personalId = $data[0];
        $nameKana = $data[1];
        $birthday = $data[5];
        $facultyName = $data[24];
		
        // 氏名(カナ)を姓名に分ける
        $seimeiList = array_unique(array_merge(explode("　", $nameKana), explode(" ", $nameKana)));
        
		// インポートデータの必須項目
		if (!empty($personalId) && !empty($nameKana) && !empty($birthday) && !empty($facultyName)) {
			// 姓名の値を作成
            $nameKanaList = array($seimeiList[0] . " " . $seimeiList[1], $seimeiList[0] . "　" . $seimeiList[1], $seimeiList[0] . $seimeiList[1], );
            
            // 在籍者情報を取得
            $this->studentDao->recursive = -1;
            $studentDto = $this->studentDao->find('first', array('conditions' => array('name_kana' => $nameKanaList, 'birthday' => $birthday)));
            
            // 在籍者情報の件数を取得
			if (!empty($studentDto)) {
                // 在籍者の更新モードをセットして在籍者IDを保持
                $this->registTypeStudent = self::REGIST_STUDENT_UPDATE;
                $this->updateStudentId = $studentDto['Student']['id'];

				// 同じ学部学科専攻の入学履歴のデータが存在するかをチェック
				if ($this->historyDao->find('count', array('conditions' => array('student_id' => $this->updateStudentId, 'faculty_name' => $facultyName))) > 0) {					
					// 存在する場合はレコードを更新
					$this->registTypeHistory = self::REGIST_HISTORY_UPDATE;
				} else {
					// 存在しない場合は新規に作成
					$this->registTypeHistory = self::REGIST_HISTORY_INSERT;
				}
                
                // 自分以外に個人IDが同じデータが無いかを確認
                $samePersonalIdStudent = $this->studentDao->find('first', array('conditions' => array('personal_id' => $personalId)));
                if (($this->studentDao->find('count', array('conditions' => array('personal_id' => $personalId))) > 0) && ($this->updateStudentId != $samePersonalIdStudent['Student']['id'])) {
                    parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$personalId.')が既に登録されています。(ID : ' . $samePersonalIdStudent['Student']['id'] . ')', self::LOG_FILE);
                    return false;
                }
                
                return true;
			} else {
                // 学籍情報を新規登録する場合に個人IDが重複していたらエラーとする
                if ($this->studentDao->find('count', array('conditions' => array('personal_id' => $personalId))) > 0) {
                    parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$personalId.')が既に登録されています。', self::LOG_FILE);
                    return false;
                }
                
                // 類似情報を取得
                if ($this->studentDao->find('count', array('conditions' => array('name_kana like' => '%' . $seimeiList[1], 'birthday' => $birthday))) > 0) {
                    // 類似情報をログに書き出して登録は行う
                    parent::writeSameStudentInfo('Line '.$this->lineCnt.' : 個人ID('.$personalId.') : 類似したデータが存在します。', self::SAME_DATA_LOG_FILE);
                }

                // DBに値が無ければ全項目を登録する
                $this->registTypeStudent = self::REGIST_STUDENT_INSERT;
                $this->registTypeHistory = self::REGIST_HISTORY_INSERT;
                    
                return true;
			}
		} else {
			// 個人IDと氏名(カナ)、誕生日、学部学科専攻名のいずれかが無ければLOGに書き出す
			parent::writeLog('Line '.$this->lineCnt.' : 個人IDと氏名(カナ)、誕生日、学部学科専攻名のいずれかが無いデータが存在します。', self::LOG_FILE);
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
		if (strlen($data[19]) > 0) {
			if (!isset($this->studentTypeList[$data[19]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 学生分類区分のデータ('.$data[19].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
		}
		// 性別
		if (strlen($data[4]) > 0) {
			if (!isset($this->sexList[$data[4]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 性別のデータ('.$data[4].')が間違っています。', self::LOG_FILE);
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
            $model['support_flag'] = Resource::SUPPORT_PAYMENT_UNPAID;
		} else if ($this->registTypeStudent == self::REGIST_STUDENT_UPDATE) {
			// 新規ではない場合はDBから値を取得
			$this->studentDao->recursive = -1;
			$rec = $this->studentDao->findById($this->updateStudentId);
			
			$model['id'] = $rec['Student']['id'];
		}
		$model['personal_id'] = $data[0];
		$model['name_kana'] = $data[1];
		$model['name'] = $data[2];
		$model['name_ext'] = $data[3];
        $model['sex'] = $data[4];
        $model['birthday'] = $data[5];
		$model['zip'] = $data[6];
		$model['address'] = $data[7];
		$model['tel'] = $data[8];
		$model['mobile_phone'] = $data[9];
		$model['mail_address_pc'] = $data[10];
		$model['mail_address_mp'] = $data[11];
		$model['guarantor_name'] = $data[12];
		$model['guarantor_zip'] = $data[13];
		$model['guarantor_address'] = $data[14];
		$model['guarantor_tel'] = $data[15];
		$model['company'] = $data[16];
		$model['payment_flag'] = $data[17];
        $model['sending_flag'] = $data[18];
        $model['deliver_ng_flag'] = $data[19];
        $model['alive_status'] = $data[20];
		
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
		
		// 登録モードに応じた配列を返す
		if ($this->registTypeHistory == self::REGIST_HISTORY_INSERT) {
			// 値をセット
			$this->historyDao->create();
			$model['student_id'] = $this->updateStudentId;
			$model['faculty_name'] = $data[24];
		} else if ($this->registTypeHistory == self::REGIST_HISTORY_UPDATE) {
			// 新規でない場合はDBから値を取得
			$models = $this->historyDao->find('all', array('conditions' => array('student_id' => $this->updateStudentId, 'faculty_name' => $data[24])));
			
			// 値をセット
			$model['id'] = $models[0]['StudentHistory']['id'];
		}
		$model['student_no'] = $data[21];
		$model['student_type'] = $data[22];
		$model['student_status'] = $data[23];
		$model['admission_date'] = $data[25];
		$model['graduation_date'] = $data[26];
		$model['alma_mater_name'] = $data[27];
		
		return $model;
	}
}
?>
