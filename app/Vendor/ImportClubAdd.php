<?php

/**
 * 学生のサークル情報を読み込む
 *
 * @author Yoshie.Aoki
 */
App::import('Model', 'Resource');
App::import('Model', 'Student');
App::import('Model', 'StudentClub');
App::import('Vendor', 'AbstractImporter');

class ImportClubAdd extends AbstractImporter {

	// 変数定義
	private $lineCnt = 1;
	private $studentDao;
	private $clubDao;
	private $resourcename = null;

	// 定数定義
	const LOG_FILE = 'club_';

	// コンストラクタ
	public function __construct() {
		// DAOクラスを生成
		$this->studentDao = ClassRegistry::init('Student');
		$this->clubDao = ClassRegistry::init('StudentClub');
		$this->resourceDao = ClassRegistry::init('Resource');
	}

	/**
	 * サークルのデータをインポートする
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
					// 在籍者情報の登録
					$studentId = 0;
					$studentModel = $this->getStudentData($data);
					$studentId = (empty($studentModel['id'])) ? $this->studentDao->getLastInsertId() : $studentModel['id'];

					// クラブ情報の登録
					$clubModel = $this->setClubData($data, $studentId);

					// DBにセットしに行く
					$this->clubDao->set($clubModel);

					// クラブ情報のバリデーションチェック
					if ($this->clubDao->validates()) {
						// クラブ情報を保存
						$saveFlg = $this->clubDao->save($clubModel, false);
						if (!$saveFlg) {
							throw new Exception("DB保存時にエラーが発生しました。");
						}
						// コミット
						$this->studentDao->commit();
					} else {
						// バリデーションエラーをセット
						parent::writeLog4Validation('Line ' . $this->lineCnt . ':メンテキー(' . $data[0] . ')_', $this->clubDao->validationErrors, self::LOG_FILE);
						// ロールバック
						$this->studentDao->rollback();
					}
				} catch (Exception $ex) {
					// ロールバック
					parent::writeLog('Line ' . $this->lineCnt . ':メンテキー(' . $data[0] . ')_' . $ex->getmessage(), self::LOG_FILE);
					$this->studentDao->rollback();
				}

				$this->lineCnt++;
			}

			// ファイルをクローズ
			fclose($fp);
		} else {
			// ファイルオープン失敗
			parent::writeLog('インポートファイルのオープンに失敗しました。', self::LOG_FILE);
		}

		return !$this->errFlg;
	}

	/**
	 * 学生データをゲットする
	 * @param Array $data
	 */
	private function getStudentData(&$data) {
		// 学生データ 存在しているかをチェック
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
	 * クラブ情報をセット
	 * @param array $data 登録データ
	 * @param int $pKey 学生ID
	 * @return array 登録内容
	 */
	private function setClubData(&$data, $pKey) {
		// クラブ情報の配列を作成
		$model = array();

		// 値をセット
		$this->clubDao->create();

		// クラブ分類をコード変換
		$resourcename = $this->resourceDao->find('first', array(
			'conditions' => array(
				'resource_name' => $data[3],
				'group_code' => Resource::CLUB_TYPE
			)
		));
		if ($resourcename == null) {
			throw new Exception("クラブ分類が見つかりません");
		} else {
			$data[3] = $resourcename['Resource']['resource_code'];
		}

		// クラブ情報をセット
		$model['student_id'] = $pKey;
		$model['student_type'] = 0;
		$model['club_name'] = $data[2];
		$model['club_type'] = $data[3];
		$model['club_post'] = $data[4];
		$model['in_date'] = $data[5];
		$model['out_date'] = $data[6];

		return $model;
	}

}
