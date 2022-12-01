<?php
/**
 * 中学校・高等学校の卒業生データを読み込む
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Vendor', 'AbstractImporter');

class ImportJuniorHighStudent extends AbstractImporter {
    // 変数定義
	private $studentDao;
	private $historyDao;
    private $seminarDao;
    private $clubDao;
	
	private $studentTypeList;
	private $sexList;
    private $clubPostList;
	
	private $registTypeStudent;
	private $registTypeHistory;
	private $lineCnt = 1;
    
    private $updateStudentId = 0;
	
	// 定数定義
	const LOG_FILE = 'junior_high_student_';
    const SAME_DATA_LOG_FILE = 'junior_high_same_student_';
	const COLUMN_NUM = 27;

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
        $this->seminarDao = ClassRegistry::init('StudentSeminar');
        $this->clubDao = ClassRegistry::init('StudentClub');
		
		// リソースの値を保持
		$resourceDao = ClassRegistry::init('Resource');
		$this->studentTypeList = $resourceDao->getResourceList(Resource::STUDENT_TYPE);
		$this->sexList = $resourceDao->getResourceList(Resource::SEX);
        $this->clubPostList = $resourceDao->getResourceList(Resource::CLUB_POST);
				
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

							if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 在籍者情報('.$data[1].')の保存時にエラーが発生しました。', self::LOG_FILE);
                            
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

								if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 入学履歴情報('.$data[15].')の保存時にエラーが発生しました。', self::LOG_FILE);
							} else {
								// バリデーションエラーをセット
								parent::writeLog4Validation('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : ', $this->historyDao->validationErrors, self::LOG_FILE);
								$saveFlg = false;
							}
						}
                        
                        // 卒業時担任の登録
                        if ($saveFlg) {
                            // 値をモデルにセット
                            $seminarModel = $this->setSeminarData($data);
                            $this->seminarDao->set($seminarModel);
                            
                            // 卒業時担任のバリデーションチェック
                            if ($this->seminarDao->validates()) {
                                // 卒業時担任を保存
                                $saveFlg = $this->seminarDao->save($seminarModel, false);
                                
                                if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 卒業時担任('.$data[20].')の保存時にエラーが発生しました。', self::LOG_FILE);
                            } else {
								// バリデーションエラーをセット
								parent::writeLog4Validation('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : ', $this->seminarDao->validationErrors, self::LOG_FILE);
								$saveFlg = false;
                            }
                        }
                        
                        // 部活動のデータを削除
                        if ($saveFlg) {
                            $saveFlg = $this->clubDao->deleteAll(array('student_id' => $this->updateStudentId, 'student_type' => $data[13]), false);
                            
                            if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 部活動の削除時にエラーが発生しました。', self::LOG_FILE);
                        }
                        
                        // 部活動の登録（最大３回ループ）
                        for ($i = 0; $i < 3; $i++) {
                            $colNum = ($i * 2) + 21;
                            if (($saveFlg) && (strlen($data[$colNum]) > 0)) {
                                // 値をモデルにセット
                                $clubModel = $this->setClubData($data, $colNum);
                                $this->clubDao->set($clubModel);
                                
                                // 部活動のバリデーションチェック
                                if ($this->clubDao->validates()) {
                                    // 部活動を保存
                                    $saveFlg = $this->clubDao->save($clubModel,false);
                                    
                                    if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 部活動名('.$data[$colNum].')の保存時にエラーが発生しました。', self::LOG_FILE);
                                } else {
                                    // バリデーションエラーをセット
                                    parent::writeLog4Validation('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : ', $this->clubDao->validationErrors, self::LOG_FILE);
                                    $saveFlg = false;
                                }
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
		
		// インポートデータから個人IDと氏名(カナ)、誕生日、卒年組を取得
		$personalId = $data[0];
        $nameKana = $data[1];
        $birthday = $data[5];
        $facultyName = $data[15];
		
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
			// 個人IDと氏名(カナ)、誕生日、卒年組のいずれかが無ければLOGに書き出す
			parent::writeLog('Line '.$this->lineCnt.' : 個人IDと氏名(カナ)、誕生日、卒年組のいずれかが無いデータが存在します。', self::LOG_FILE);
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
		
		// 学生種別
		if (strlen($data[13]) > 0) {
			if (!isset($this->studentTypeList[$data[13]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 学生種別のデータ('.$data[13].')が間違っています。', self::LOG_FILE);
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
        
        // 部活動役職１、２、３
        if (strlen($data[22]) > 0) {
			if (!isset($this->clubPostList[$data[22]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 部活動役職１のデータ('.$data[22].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
        }
        if (strlen($data[24]) > 0) {
			if (!isset($this->clubPostList[$data[24]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 部活動役職２のデータ('.$data[24].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
        }
        if (strlen($data[26]) > 0) {
			if (!isset($this->clubPostList[$data[26]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : 部活動役職３のデータ('.$data[26].')が間違っています。', self::LOG_FILE);
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
            $model['payment_flag'] = Resource::FEE_PAYMENT_PAID;
            $model['support_flag'] = Resource::SUPPORT_PAYMENT_UNPAID;
            $model['sending_flag'] = Resource::INVITATION_SENDING_OK;
            $model['deliver_ng_flag'] = Resource::INVITATION_DELIVERY_OK;
            $model['alive_status'] = Resource::ALIVE_INFO_OK;
		} else if ($this->registTypeStudent == self::REGIST_STUDENT_UPDATE) {
			// 新規ではない場合はDBから値を取得
			$this->studentDao->recursive = -1;
			$rec = $this->studentDao->findById($this->updateStudentId);
			
			$model['id'] = $rec['Student']['id'];
		}
		$model['personal_id'] = $data[0];
		$model['name_kana'] = $data[1];
		$model['name'] = (strlen($data[2]) == 0) ? $data[3] : $data[2];
		$model['name_ext'] = $data[3];
        $model['sex'] = $data[4];
        $model['birthday'] = $data[5];
		$model['zip'] = $data[6];
		$model['address'] = $data[7];
		$model['tel'] = $data[8];
		$model['guarantor_zip'] = $data[9];
		$model['guarantor_address'] = $data[10];
		$model['guarantor_tel'] = $data[11];
		
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
			$model['faculty_name'] = $data[15];
		} else if ($this->registTypeHistory == self::REGIST_HISTORY_UPDATE) {
			// 新規でない場合はDBから値を取得
			$models = $this->historyDao->find('all', array('conditions' => array('student_id' => $this->updateStudentId, 'faculty_name' => $data[15])));
			
			// 値をセット
			$model['id'] = $models[0]['StudentHistory']['id'];
		}
		$model['student_no'] = $data[12];
		$model['student_type'] = $data[13];
		$model['student_status'] = $data[14];
		$model['admission_date'] = $data[16];
		$model['graduation_date'] = $data[17];
		$model['graduation_course'] = $data[18];
		$model['alma_mater_name'] = $data[19];
		
		return $model;
	}
    
    /**
     * 卒業時担任の連想配列に値をセット
     * @param array $data インポートされたデータ
     * @return array 連想配列にセットされたデータ
     */
    private function setSeminarData(&$data) {
        // 卒業時の配列に値をセット
        $model = array();
        
        // 学生種別のデータを取得
        $seminarList = $this->seminarDao->find('all', array('conditions' => array('student_id' => $this->updateStudentId, 'student_type' => $data[13])));
        if (count(($seminarList)) == 0) {
            // 値をセット
            $this->seminarDao->create();
            $model['student_id'] = $this->updateStudentId;
            $model['student_type'] = $data[13];
        } else {
            // 値をセット
            $model['id'] = $seminarList[0]['StudentSeminar']['id'];
        }
        
        $model['seminar_teacher_name'] = $data[20];
        
        return $model;
    }
    
    /**
     * 部活動の連想配列に値をセット
     * @param array $data インポートされたデータ
     * @param int $colNum レコード番号
     * @return array 連想配列にセットされたデータ
     */
    private function setClubData(&$data, $colNum) {
        // 部活動の配列に値をセット
        $model = array();
        
        // 部活動のデータをセット
        $this->clubDao->create();
        $model['student_id'] = $this->updateStudentId;
        $model['student_type'] = $data[13];
        $model['club_name'] = $data[$colNum];
        $model['club_post'] = $data[$colNum + 1];
        
        return $model;
    }
}
