<?php
/**
 * ゼミ情報を取り込む
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Vendor', 'AbstractImporter');

class ImportSeminar extends AbstractImporter {
	// 変数定義
	private $seminarDao;
	private $studentDao;

	private $studentId;
	
	private $registType;
	private $lineCnt = 1;

	// 定数定義
	const LOG_FILE = 'seminar_';
	const COLUMN_NUM = 4;

	const REGIST_NONE = 0;
	const REGIST_INSERT = 1;
	const REGIST_UPDATE = 2;

	// コンストラクタ
	public function __construct() {
		// DAOクラスを生成
		$this->seminarDao = ClassRegistry::init('StudentSeminar');
		$this->studentDao = ClassRegistry::init('Student');

		// 登録タイプは｢登録しない｣で登録
		$this->registType = self::REGIST_NONE;
	}

	/**
	 * ゼミ情報を取り込む
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
                    // 値をオブジェクトにセット
                    $seminarModel = $this->setSeminarData($data);

                    // 値をモデルにセット
                    $this->seminarDao->set($seminarModel);

                    // 保存開始
                    $this->seminarDao->begin();
                    $saveFlg = true;

                    // 所属ゼミ情報の登録
                    if ($this->seminarDao->validates()) {
                        // 所属ゼミ情報を保存
                        $saveFlg = $this->seminarDao->save($seminarModel, false);

                        if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 所属ゼミ('.$data[0].' : '.$data[1].') : 保存時にエラーが発生しました。', self::LOG_FILE);
                    } else {
                        // バリデーションエラーをセット
                        parent::writeLog4Validation('Line '.$this->lineCnt.' : 所属ゼミ('.$data[0].' : '.$data[1].') : ', $this->seminarDao->validationErrors, self::LOG_FILE);
                        $saveFlg = false;
                    }

                    // トランザクション修了
                    if ($saveFlg) {
                        $this->seminarDao->commit();
                    } else {
                        $this->seminarDao->rollback();
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

		// インポートデータから個人IDとゼミ教員名を取得
		$personalId = $data[0];
		$teacherName = $data[1];
		$year = $data[2];
		
		// 学籍番号とゼミ教員IDがあればデータをチェック
		if (!empty($personalId) && !empty($teacherName) && !empty($year)) {
            // 在籍者情報を取得
            $this->studentDao->recursive = -1;
            $studentDto = $this->studentDao->find('first', array('conditions' => array('personal_id' => $personalId)));
            $this->studentId = $studentDto['Student']['id'];
            
			// 入学履歴の件数を取得
			if (!empty($studentDto)) {
				// 所属ゼミの情報は全て取り込む
                $this->registType = self::REGIST_INSERT;
                return true;
			} else {
				// 在籍者の情報が登録されていないので登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$personalId.') : 在籍者情報が登録されていません。', self::LOG_FILE);
				return false;
			}
		} else {
			// 学籍番号かゼミ教員IDが無ければLOGに書き出す
			parent::writeLog('Line '.$this->lineCnt.' : 個人ID、ゼミ教員名、履修年度のいずれかが無いデータが存在します。', self::LOG_FILE);
			return false;
		}
	}
		
	/**
	 * 所属ゼミの連想配列に値をセット
	 * 初期値のデータも一緒にセット
	 * @param array $data インポートされたデータ
	 * @return array 連想配列にセットされたデータ
	 */
	private function setSeminarData(&$data) {
		// 在籍者の配列に値をセット
		$model = array();
		if ($this->registType == self::REGIST_INSERT) {
			// インポートされたデータをセット
			$this->seminarDao->create();
			$model['student_id'] = $this->studentId;
            $model['student_type'] = 0;
			$model['seminar_teacher_name'] = $data[1];
		} else if ($this->registType == self::REGIST_UPDATE) {
			// 新規ではない場合はDBから値を取得
			$rec = $this->seminarDao->find('all', array('conditions' => array('student_id' => $this->studentId, 'student_type' => 0, 'seminar_teacher_name' => $data[1], 'completion_year' => $data[2])));
			$model['id'] = $rec[0]['StudentSeminar']['id'];
		}
		$model['completion_year'] = $data[2];
		$model['seminar_result'] = $data[3];
		
		return $model;
	}

}
?>
