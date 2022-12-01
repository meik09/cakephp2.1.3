<?php

/**
 * ゼミ履歴のデータを読み込む
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Model', 'Student');
App::import('Model', 'StudentSeminar');
App::import('Vendor', 'AbstractImporter');

class ImportSeminarHistory extends AbstractImporter {

	// 変数定義
	private $lineCnt = 1;
	private $studentDao;
	private $seminarDao;

	// 定数定義
	const LOG_FILE = 'seminarhistory_';

	// コンストラクタ
	public function __construct() {
		// DAOクラスを生成
		$this->studentDao = ClassRegistry::init('Student');
		$this->seminarDao = ClassRegistry::init('StudentSeminar');
	}

	/**
	 * 中高のデータをインポートする
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
				$saveFlg = true;

				// エラーハンドリング
				try {
					// 在籍者情報の検索
					$studentId = 0;
					$studentModel = $this->getStudentData($data);
					$studentId = (empty($studentModel['id'])) ? $this->studentDao->getLastInsertId() : $studentModel['id'];

					// ゼミ情報の登録
					$seminarModel = $this->setSeminarData($data, $studentId);
					$this->seminarDao->set($seminarModel);

					// ゼミ情報のバリデーションチェック
					if ($this->seminarDao->validates()) {
						// ゼミ情報を保存
						$saveFlg = $this->seminarDao->save($seminarModel, false);
						if (!$saveFlg) {
							throw new Exception("DB保存時にエラーが発生しました");
						}
						// コミット
						$this->studentDao->commit();
					} else {
						// バリデーションエラーをセット
						parent::writeLog4Validation('Line ' . $this->lineCnt . ':メンテキー(' . $data[0] . ')_', $this->seminarDao->validationErrors, self::LOG_FILE);
						// ロールバック
						$this->studentDao->rollback();
					}
				} catch (Exception $ex) {
					// ロールバック
					parent::writeLog('Line ' . $this->lineCnt . ':メンテキー(' . $data[0] . ')_' . $ex->getMessage(), self::LOG_FILE);
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
	private function getStudentData(&$data) {
		// 学生データ存在しているかをチェック
		$model = $this->studentDao->find('first', array(
			'conditions' => array(
				'personal_id' => $data[0]
			)
		));

		// データの有無
		if ($model == null) {
			throw new Exception("学生の個人IDが見つかりません");
		} else {
			$model = $model['Student'];
		}

		return $model;
	}

	/**
	 * ゼミ情報をセット
	 * @param array $data 登録データ
	 * @param int $pKey 学生ID
	 * @return array 登録内容
	 */
	private function setSeminarData(&$data, $pKey) {
		// ゼミ情報の配列を作成
		$model = array();

		// 値をセット
		$this->seminarDao->create();

		// ゼミ情報をセット
		$model['student_id'] = $pKey;
		$model['student_type'] = 0;
		$model['seminar_teacher_name'] = $data[2];
		$model['seminar_result'] = $data[3];
		$model['completion_year'] = $data[4];

		return $model;
	}

}
