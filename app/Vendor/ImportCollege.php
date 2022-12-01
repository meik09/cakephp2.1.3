<?php
/**
 * 同窓会管理のデータを読み込む
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Model', 'Student');
App::import('Model', 'StudentHistory');
App::import('Model', 'StudentClub');
App::import('Model', 'StudentSeminar');
App::import('Model', 'StudentSupportPrice');
App::import('Vendor', 'AbstractImporter');

class ImportCollege extends AbstractImporter {
    // 変数定義
    private $lineCnt = 1;
	
    private $studentDao;
    private $historyDao;
    private $clubDao;
    private $seminarDao;
    private $supportDao;
    
    // 定数定義
    const LOG_FILE = 'College_';
	
	// コンストラクタ
	public function __construct() {
        // DAOクラスを生成
		$this->studentDao = ClassRegistry::init('Student');
		$this->historyDao = ClassRegistry::init('StudentHistory');
        $this->clubDao = ClassRegistry::init('StudentClub');
        $this->seminarDao = ClassRegistry::init('StudentSeminar');
        $this->supportDao = ClassRegistry::init('StudentSupportPrice');
    }
    
    /**
     * 同窓会のデータをインポートする
     * @param obj $file ファイルオブジェクト
     * @return boolean エラーフラグ
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
                // 一行目は飛ばす
                if ($this->lineCnt == 1) {
                    $this->lineCnt++;
                    continue;
                }
				// エンコードを変更して配列に保持
				$data = parent::trimData(str_getcsv(mb_convert_encoding($line, 'utf-8', 'sjis-win')));

                // 保存開始
                $this->studentDao->begin();

                // エラーハンドリング
                try {
                    // 在籍者情報の登録
                    $studentModel = $this->setStudentData($data);
                    $this->studentDao->set($studentModel);

                    // 在籍者情報のバリデーションチェック
                    if ($this->studentDao->validates()) {
                        // 在籍者情報を保存
                        $this->studentDao->primaryKey = 'id';
                        $saveFlg = $this->studentDao->save($studentModel, false);
                        $studentId = (empty($studentModel['id'])) ? $this->studentDao->getLastInsertId() : $studentModel['id'];

                        if (!$saveFlg) parent::writeLog('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 現姓名(' . $data[5] . ')の保存時にエラーが発生しました。', self::LOG_FILE);
                    } else {
                        // バリデーションエラーをセット
                        parent::writeLog4Validation('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 学生情報 : ', $this->studentDao->validationErrors, self::LOG_FILE);
                        $saveFlg = false;
                    }

                    // 入学履歴の登録
                    if ($saveFlg) {
                        // 6回ループする
                        for ($i = 0; $i < 6; $i++) {
                            $historyModel = $this->setHistoryData($data, $i, $studentId);
                            
                            // モデルの配列サイズが0の場合は登録しない
                            if (count($historyModel) > 0) {
                                $this->historyDao->set($historyModel);

                                // 履歴情報のバリデーションチェック
                                if ($this->historyDao->validates()) {
                                    // 履歴情報を保存
                                    $saveFlg = $this->historyDao->save($historyModel, false);

                                    if (!$saveFlg) parent::writeLog('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 入学履歴情報(' . $historyModel['faculty_name'] . ')の保存時にエラーが発生しました。', self::LOG_FILE);
                                } else {
                                    // バリデーションエラーをセット
                                    parent::writeLog4Validation('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 入学履歴 : ', $this->historyDao->validationErrors, self::LOG_FILE);
                                    $saveFlg = false;
                                }
                            }
                        }
                    }
                    
                    // ゼミ情報の登録
                    if ($saveFlg) {
                        // 3回ループする
                        for ($i = 0; $i < 3; $i++) {
                            $seminarModel = $this->setSeminarData($data, $i, $studentId);

                            // モデルの配列サイズが0の場合は登録しない
                            if (count($seminarModel) > 0) {
                                $this->seminarDao->set($seminarModel);

                                // ゼミ情報のバリデーションチェック
                                if ($this->seminarDao->validates()) {
                                    // ゼミ情報を保存
                                    $saveFlg = $this->seminarDao->save($seminarModel, false);

                                    if (!$saveFlg) parent::writeLog('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : ゼミ情報(' . $seminarModel['seminar_teacher_name'] . ')の保存時にエラーが発生しました。', self::LOG_FILE);
                                } else {
                                    // バリデーションエラーをセット
                                    parent::writeLog4Validation('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : ゼミ情報 : ', $this->seminarDao->validationErrors, self::LOG_FILE);
                                    $saveFlg = false;
                                }
                            }
                        }
                    }
                    
                    // クラブ情報の登録
                    if ($saveFlg) {
                        // 5回ループする
                        for ($i = 0; $i < 6; $i++) {
                            $clubModel = $this->setClubData($data, $i, $studentId);
                            
                            // モデルの配列サイズが0の場合は登録しない
                            if (count($clubModel) > 0) {
                                $this->clubDao->set($clubModel);

                                // クラブ情報のバリデーションチェック
                                if ($this->clubDao->validates()) {
                                    // クラブ情報を保存
                                    $saveFlg = $this->clubDao->save($clubModel, false);

                                    if (!$saveFlg) parent::writeLog('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : クラブ情報(' . $clubModel['club_name'] . ')の保存時にエラーが発生しました。', self::LOG_FILE);
                                } else {
                                    // バリデーションエラーをセット
                                    parent::writeLog4Validation('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : クラブ情報 : ', $this->clubDao->validationErrors, self::LOG_FILE);
                                    $saveFlg = false;
                                }
                            }
                        }
                    }

                    // 助成金情報の登録
                    if ($saveFlg) {
                        // 3回ループする
                        for ($i = 0; $i < 3; $i++) {
                            $supportModel = $this->setSupportData($data, $i, $studentId);
                            
                            // モデルの配列サイズが0の場合は登録しない
                            if (count($supportModel) > 0) {
                                $this->supportDao->set($supportModel);

                                // クラブ情報のバリデーションチェック
                                if ($this->supportDao->validates()) {
                                    // クラブ情報を保存
                                    $saveFlg = $this->supportDao->save($supportModel, false);

                                    if (!$saveFlg) parent::writeLog('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 助成金情報(' . $supportModel['year'] . ')の保存時にエラーが発生しました。', self::LOG_FILE);
                                } else {
                                    // バリデーションエラーをセット
                                    parent::writeLog4Validation('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 助成金情報 : ', $this->supportDao->validationErrors, self::LOG_FILE);
                                    $saveFlg = false;
                                }
                            }
                        }
                    }

                    // コミット
                    $this->studentDao->commit();
                }
                catch (Exception $ex) {
                    // ロールバック
                    parent::writeLog('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 現姓名(' . $data[5] . ')の保存時にエラーが発生しました。', self::LOG_FILE);
                    $this->studentDao->rollback();
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
     * 学生データをセットする
     * @param Array $data
     */
    private function setStudentData(&$data) {
        // 学生データ存在しているかをチェック
        $model = $this->studentDao->find('first', array(
            'conditions' => array(
                'personal_id' => $data[0]
            )
        ));
        
        // データの有無
        if ($model == null) {
            // 在籍者の配列を作成
            $model = array();

            // 値をセット
            $this->studentDao->create();
        } else {
            $model = $model['Student'];
        }

        $model['personal_id'] = $data[0];
        if (strlen($data[4]) > 0) $model['name_kana'] = $data[4];
        if (strlen($data[5]) > 0) $model['name'] = $data[5];
        if (strlen($data[6]) > 0) $model['name_old_kana'] = $data[6];
        if (strlen($data[7]) > 0) $model['name_old'] = $data[7];
        if (strlen($data[8]) > 0) $model['sex'] = $data[8];
        if (strlen($data[2]) > 0) $model['birthday'] = $data[2];
        if (empty($model['zip'])) $model['zip'] = str_replace("‐", "-", trim($data[9]));
        if (empty($model['address'])) $model['address'] = $data[10];
        if (empty($model['tel'])) $model['tel'] = str_replace("‐", "-", $data[11]);
        if (strlen($data[64]) > 0) $model['mobile_phone'] = str_replace("‐", "-", $data[64]);
        if (strlen($data[63]) > 0) $model['mail_address_pc'] = $data[63];
        if (empty($model['guarantor_zip'])) $model['guarantor_zip'] = str_replace("‐", "-", trim($data[15]));
        if (empty($model['guarantor_address'])) $model['guarantor_address'] = $data[16];
        if (empty($model['guarantor_tel'])) $model['guarantor_tel'] = str_replace("‐", "-", $data[17]);
        if (strlen($data[52]) > 0) $model['company'] = $data[52];
        if (strlen($data[53]) > 0) $model['company_tel'] = str_replace("‐", "-", $data[53]);
        if (strlen($data[51]) > 0) $model['unsettled_price'] = $data[51];
        if (strlen($data[61]) > 0) $model['support_price'] = $data[61];
        if (strlen($data[46]) > 0) $model['area_branch_name'] = $data[46];
        if (strlen($data[47]) > 0) $model['job_branch_name'] = $data[47];
        
        // 同窓会費納付は大学１が卒業の場合は納付
        if ((strpos(trim($data[28]), "卒業") !== false) || (strpos(trim($data[33]), "卒業") !== false) || (strpos(trim($data[38]), "卒業") !== false) || (strpos(trim($data[43]), "卒業") !== false)) {
            $model['payment_flag'] = 0;        
        } else if (empty($model['payment_flag'])) {
            $model['payment_flag'] = 1;
        }
                
        // 案内状送付は全員不可
        $model['sending_flag'] = 1;

        // 不明マークが○の場合は不着
        if (($data[44] == "○") || ($data[44] == "〇")) {
            $model['deliver_ng_flag'] = 1;
        } else {
            $model['deliver_ng_flag'] = 0;
        }
        
        // 死亡マークが×の場合は死亡
        if (($data[45] == "×") || ($data[45] == "○") || ($data[45] == "〇")) {
            $model['alive_status'] = 1;
        } else {
            $model['alive_status'] = 0;
        }
        
        return $model;
    }
    
    /**
     * 入学履歴のデータを作成
     * @param 登録データ $data
     * @param カウント $cnt
     * @param int $pKey 学生ID
     */
    private function setHistoryData(&$data, $cnt, $pKey) {
        // 入学履歴の配列を作成
        $model = array();
        
        // 値をセット
        $this->historyDao->create();

        // 学生の区分を判定
        if ($cnt < 4) {         // 大学
            // 基準番号
            $baseNo = 24 + ($cnt * 5);
            
            // 学部学科専攻が空の場合は登録しない
            if (strlen($data[$baseNo]) > 0) {
                $model['student_id'] = $pKey;
                $model['student_no'] = $data[3];
                $model['student_type'] = 0;
                $model['student_status'] = (strlen(trim($data[$baseNo + 4])) > 0) ? trim($data[$baseNo + 4]) : '卒業';
                $model['faculty_name'] = $data[$baseNo];
                $model['admission_date'] = str_replace(" 0:00:00", "", $data[$baseNo + 2]);
                $model['graduation_date'] = str_replace(" 0:00:00", "", $data[$baseNo + 3]);
                $model['alma_mater_name'] = $data[23];
            }
        } else if ($cnt == 4) {  // 高校
            // 卒年組・高が空の場合は登録しない
            if (strlen($data[57]) > 0) {
                $model['student_id'] = $pKey;
                $model['student_type'] = 90;
                $model['student_status'] = '卒業';
                $model['faculty_name'] = $data[57];
                $model['graduation_course'] = $data[54];
                $model['alma_mater_name'] = $data[58];                
            }
        } else if ($cnt == 5) {  // 中学
            // 卒年組・中が空の場合は登録しない
            if (strlen($data[56]) > 0) {
                $model['student_id'] = $pKey;
                $model['student_type'] = 80;
                $model['student_status'] = '卒業';
                $model['faculty_name'] = $data[56];
                $model['graduation_course'] = $data[23];
                $model['alma_mater_name'] = $data[59];                
            }
        }
        
        return $model;
    }
    
    /**
     * ゼミ情報をセット
     * @param array $data 登録データ
     * @param カウント $cnt
     * @param int $pKey 学生ID
     * @return array 登録内容
     */
    private function setSeminarData(&$data, $cnt, $pKey) {
        // ゼミ情報の配列を作成
        $model = array();
        
        // 値をセット
        $this->seminarDao->create();

        // 基準番号
        $baseNo = 12 + $cnt;

        // ゼミ名がないデータは作成しない
        if (strlen($data[$baseNo]) > 0) {
            $model['student_id'] = $pKey;
            $model['student_type'] = 0;
            $model['seminar_teacher_name'] = $data[$baseNo];            
        }
        
        return $model;
    }
    
    /**
     * クラブ情報をセット
     * @param array $data 登録データ
     * @param int $cnt カウント
     * @param int $pKey 学生ID
     * @return array 登録内容
     */
    private function setClubData(&$data, $cnt, $pKey) {
        // クラブ情報の配列を作成
        $model = array();
        
        // 値をセット
        $this->clubDao->create();

        // 学生の区分を判定
        if ($cnt < 4) {         // 大学
            // 基準番号
            $baseNo = 18 + $cnt;
            
            // クラブ名が空の場合は登録しない
            if (strlen($data[$baseNo]) > 0) {
                $model['student_id'] = $pKey;
                $model['student_type'] = 0;
                $model['club_name'] = $data[$baseNo];
            }
        } else if ($cnt == 4) { // 高校
            // クラブ名が空の場合は登録しない
            if (strlen($data[60]) > 0) {
                $model['student_id'] = $pKey;
                $model['student_type'] = 90;
                $model['club_name'] = $data[60];
            }
        }
        
        return $model;
    }
    
    /**
     * 助成金情報をセット
     * @param array $data 登録データyoros
     * @param int $cnt カウント
     * @param int $pKey 学生ID
     * @return array 登録内容
     */
    private function setSupportData(&$data, $cnt, $pKey) {
        // クラブ情報の配列を作成
        $model = array();
        
        // 基準番号
        $baseNo = 48 + $cnt;

        // 助成金の値があれば登録
        if (strlen($data[$baseNo]) > 0) {
            // 値をセット
            $this->supportDao->create();

            $model['student_id'] = $pKey;
            $model['year'] = 2012 + $cnt;
            $model['price'] = $data[$baseNo];
        }
        
        return $model;
    }
}
