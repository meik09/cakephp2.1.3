<?php
/**
 * サークル情報を取り込む
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Vendor', 'AbstractImporter');

class ImportClub extends AbstractImporter {
	// 変数定義
	private $clubDao;
	private $studentDao;
	private $resourceDao;

	private $postList;
	
	private $studentId;
	
	private $registType;
	private $lineCnt = 1;

	// 定数定義
	const LOG_FILE = 'club_';
	const COLUMN_NUM = 6;

	const REGIST_NONE = 0;
	const REGIST_INSERT = 1;
	const REGIST_UPDATE = 2;
    
    private $clubTypeMap = array(
        "A" => 1,
        "B" => 2,
        "C" => 5,
        "D" => 3,
        "E" => 4
    );

	// コンストラクタ
	public function __construct() {
		// DAOクラスを生成
		$this->clubDao = ClassRegistry::init('StudentClub');
		$this->studentDao = ClassRegistry::init('Student');
		$this->resourceDao = ClassRegistry::init('Resource');
		
		// リソースの内容を取得
		$this->postList = $this->resourceDao->getResourceList(Resource::CLUB_POST);

		// 登録タイプは｢登録しない｣で登録
		$this->registType = self::REGIST_NONE;
	}

	/**
	 * サークル情報を取り込む
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
						// 値をオブジェクトにセット
						$clubModel = $this->setClubData($data);

						// 値をモデルにセット
						$this->clubDao->set($clubModel);

						// 保存開始
						$this->clubDao->begin();
						$saveFlg = true;

						// 所属サークル情報の登録
						if ($this->clubDao->validates()) {
							// 所属サークル情報を保存
							$saveFlg = $this->clubDao->save($clubModel, false);

							if (!$saveFlg) parent::writeLog('Line '.$this->lineCnt.' : 所属サークル('.$data[0].' : '.$data[2].') : 保存時にエラーが発生しました。', self::LOG_FILE);
						} else {
							// バリデーションエラーをセット
							parent::writeLog4Validation('Line '.$this->lineCnt.' : 所属サークル('.$data[0].' : '.$data[2].') : ', $this->clubDao->validationErrors, self::LOG_FILE);
							$saveFlg = false;
						}

						// トランザクション修了
						if ($saveFlg) {
							$this->clubDao->commit();
						} else {
							$this->clubDao->rollback();
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

		// インポートデータから学籍番号とサークル名を取得
		$personalId = $data[0];
		$clubName = $data[2];
		
		// 学籍番号とサークル名があればデータをチェック
		if (!empty($personalId) && !empty($clubName)) {
            // 在籍者情報を取得
            $this->studentDao->recursive = -1;
            $studentDto = $this->studentDao->find('first', array('conditions' => array('personal_id' => $personalId)));
            $this->studentId = $studentDto['Student']['id'];
            
			// 在籍者情報の件数を取得
			if (!empty($studentDto)) {
				// 学生所属サークルの件数を取得
				if ($this->clubDao->find('count', array('conditions' => array('student_id' => $this->studentId, 'student_type' => 0, 'club_name' => $clubName))) == 0) {
					// 所属サークルのデータが無いので新規登録
					$this->registType = self::REGIST_INSERT;
					return true;
				} else {
					// 所属サークルのデータが在るので更新
					$this->registType = self::REGIST_UPDATE;
					return true;
				}
				
			} else {
				// 在籍者の情報が登録されていないので登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$personalId.') : 在籍者情報が登録されていません。', self::LOG_FILE);
				return false;
			}
		} else {
			// 学籍番号かサークル名が無ければLOGに書き出す
			parent::writeLog('Line '.$this->lineCnt.' : 個人IDかサークル名の無いデータが存在します。', self::LOG_FILE);
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

		// サークル分類
		if (strlen($data[1]) > 0) {
			if (!isset($this->clubTypeMap[$data[1]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : サークル分類のデータ('.$data[1].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
		}
		
		// サークル役職
		if (strlen($data[3]) > 0) {
			if (!isset($this->postList[$data[3]])) {
				// リソースのIDと一致しなければログに書き出して登録しない
				parent::writeLog('Line '.$this->lineCnt.' : 個人ID('.$data[0].') : サークル役職のデータ('.$data[3].')が間違っています。', self::LOG_FILE);
				$result = false;
			}
		}
		
		return $result;
	}
	
	/**
	 * 所属ゼミの連想配列に値をセット
	 * 初期値のデータも一緒にセット
	 * @param array $data インポートされたデータ
	 * @return array 連想配列にセットされたデータ
	 */
	private function setClubData(&$data) {
		// 在籍者の配列に値をセット
		$model = array();
		if ($this->registType == self::REGIST_INSERT) {
			// インポートされたデータをセット
			$this->clubDao->create();
			$model['student_id'] = $this->studentId;
            $model['student_type'] = 0;
			$model['club_name'] = $data[2];
		} else if ($this->registType == self::REGIST_UPDATE) {
			// 新規ではない場合はDBから値を取得
			$rec = $this->clubDao->find('all', array('conditions' => array('student_id' => $this->studentId, 'student_type' => 0, 'club_name' => $data[2])));
			$model['id'] = $rec[0]['StudentClub']['id'];
		}
		$model['club_type'] = $this->clubTypeMap[$data[1]];
		$model['club_post'] = $data[3];
		$model['in_date'] = $data[4];
		$model['out_date'] = $data[5];
		
		return $model;
	}

}
?>
