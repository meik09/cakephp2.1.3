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

class ImportNewCollege extends AbstractImporter {
    // 変数定義
    private $lineCnt = 1;
	
    private $studentDao;
    private $historyDao;
    
    // 定数定義
    const LOG_FILE = 'College2_';
	
	// コンストラクタ
	public function __construct() {
        // DAOクラスを生成
		$this->studentDao = ClassRegistry::init('Student');
		$this->historyDao = ClassRegistry::init('StudentHistory');
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

                        if (!$saveFlg) parent::writeLog('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 現姓名(' . $data[3] . ')の保存時にエラーが発生しました。', self::LOG_FILE);
                    } else {
                        // バリデーションエラーをセット
                        parent::writeLog4Validation('Line ' . $this->lineCnt . ' : メンテキー(' . $data[0] . ') : 学生情報 : ', $this->studentDao->validationErrors, self::LOG_FILE);
                        $saveFlg = false;
                    }

                    // 入学履歴の登録
                    if ($saveFlg) {
                        $historyModel = $this->setHistoryData($data, $studentId);

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
            
            // 卒業レコードの有無
            $graduationFlg = false;

            // 値をセット
            $this->studentDao->create();
        } else {
            $model = $model['Student'];
            $graduationFlg = $this->historyDao->isExistGraduation($model['id']);
        }

        $model['personal_id'] = $data[0];
        if (strlen($data[3]) > 0) $model['name_kana'] = $data[3];
        if (strlen($data[4]) > 0) $model['name'] = $data[4];
        if (strlen($data[5]) > 0) $model['sex'] = $data[5];
        if (strlen($data[1]) > 0) $model['birthday'] = $data[1];
        if (empty($model['zip'])) $model['zip'] = str_replace("‐", "-", trim($data[6]));
        if (empty($model['address'])) $model['address'] = $data[7];
        if (empty($model['tel'])) $model['tel'] = str_replace("‐", "-", $data[8]);
        if (strlen($data[18]) > 0) $model['mail_address_pc'] = $data[18];
        if (empty($model['guarantor_zip'])) $model['guarantor_zip'] = str_replace("‐", "-", trim($data[9]));
        if (empty($model['guarantor_address'])) $model['guarantor_address'] = $data[10];
        if (empty($model['guarantor_tel'])) $model['guarantor_tel'] = str_replace("‐", "-", $data[11]);
        if (strlen($data[17]) > 0) $model['company'] = $data[17];
        if (empty($model['deliver_ng_flag'])) $model['deliver_ng_flag'] = 0;
        if (empty($model['alive_status'])) $model['alive_status'] = 0;
        
        // 同窓会費納付は大学１が卒業の場合は納付
        if ((strpos(trim($data[16]), "卒業") !== false) || ($graduationFlg === true)) {
            $model['payment_flag'] = 0;        
        } else if (empty($model['payment_flag'])) {
            $model['payment_flag'] = 1;
        }
                
        // 案内状送付は中高の卒業生は可、大学の卒業生は可
        if ((strpos(trim($data[16]), "卒業") !== false) || ($graduationFlg === true)) {
            $model['sending_flag'] = 0;
        } else if (empty($model['sending_flag'])) {
            $model['sending_flag'] = 1;
        }
        
        return $model;
    }
    
    /**
     * 入学履歴のデータを作成
     * @param 登録データ $data
     * @param int $pKey 学生ID
     */
    private function setHistoryData(&$data, $pKey) {
        // 入学履歴の配列を作成
        $model = array();
        
        // 値をセット
        $this->historyDao->create();

        // 学部学科専攻が空の場合は登録しない
        if (strlen($data[13]) > 0) {
            $model['student_id'] = $pKey;
            $model['student_no'] = $data[2];
            $model['student_type'] = 0;
            $model['student_status'] = (strlen(trim($data[16])) > 0) ? trim($data[16]) : '卒業';
            $model['faculty_name'] = $data[13];
            $model['admission_date'] = $data[14];
            $model['graduation_date'] = $data[15];
            $model['alma_mater_name'] = $data[12];
        }
        
        return $model;
    }
}
