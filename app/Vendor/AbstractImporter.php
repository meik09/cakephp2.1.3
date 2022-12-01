<?php
/**
 * Importクラス関連の抽象クラス
 *
 * @author Naoya.Kuga
 */
abstract class AbstractImporter {
	// 変数定義
	protected $errFlg = false;
	protected $errFile = '';
    protected $sameStudentFlg = false;
    protected $sameStudentFile = '';


    // 抽象メソッド
	abstract protected function import($files);

	/**
	 * インポートする値から余分な空白を除去する
	 * @param array $data インポートするデータ
	 */
	protected function trimData($data) {
		// データの数だけループする
		foreach ($data as $key => $value) {
            $value = preg_replace("/^[　]+/u", "", $value);
            $value = preg_replace("/[　]+$/u", "", $value);
			$data[$key] = trim($value);
		}
		
		return $data;
	}
	
	/**
	 * エラーファイルを書き出すメソッド
	 * @param string $msg ログメッセージ
	 * @param string $prefix ログファイルのプレフィックス
	 */
	public function writeLog($msg, $prefix) {
		// ログファイルを開く
		if (strlen($this->errFile) == 0) $this->errFile = $prefix.date('Ymd').'.log';
		$logFp = fopen(LOG_ROOT.$this->errFile, 'a+');
		
		// ログにメッセージを登録
		fwrite($logFp, mb_convert_encoding('['.date('H:i:s').'] '.$msg.PHP_EOL, 'sjis-win', 'utf-8'));
		
		// ファイルを閉じてフラグをONにする
		fclose($logFp);
		$this->errFlg = true;
	}

    /**
	 * 類似在籍者を書き出すメソッド
	 * @param string $msg ログメッセージ
	 * @param string $prefix ログファイルのプレフィックス
	 */
	public function writeSameStudentInfo($msg, $prefix) {
		// ログファイルを開く
		if (strlen($this->sameStudentFile) == 0) $this->sameStudentFile = $prefix.date('Ymd').'.log';
		$logFp = fopen(LOG_ROOT.$this->sameStudentFile, 'a+');
		
		// ログにメッセージを登録
		fwrite($logFp, mb_convert_encoding('['.date('H:i:s').'] '.$msg.PHP_EOL, 'sjis-win', 'utf-8'));
		
		// ファイルを閉じてフラグをONにする
		fclose($logFp);
		$this->sameStudentFlg = true;
	}

	/**
	 * モデルのバリデーションエラーを書き出す
	 * @param string $msg ログメッセージヘッダー
	 * @param array  $err モデルのバリデーションエラー
	 * @param string $prefix ログファイルのプレフィックス
	 */
	public function writeLog4Validation($msg, $err, $prefix) {
		// エラーの配列を展開しながら書き出す
		foreach ($err as $field => $validationErrs) {
			foreach ($validationErrs as $validationMsg) {
				$this->writeLog($msg . $validationMsg, $prefix);
			}
		}
	}
	
	/**
	 * 学部・学科・専攻のIDを返す
	 * @param string $facultyId 学部ID
	 * @param string $subjectId 学科ID
	 * @param string $specialityId 専攻ID
	 * @return string 学部・学科・専攻のIDを結合したID 
	 */
	public function getFacultyId($facultyId = "", $subjectId = "", $specialityId = "") {
		// 各パラメタが空白だったら｢00｣とする
		if (strlen($facultyId) == 0) $facultyId = '00';
		if (strlen($subjectId) == 0) $subjectId = '00';
		if (strlen($specialityId) == 0) $specialityId = '00';
		
		// 各値を連結して返す
		return $facultyId . $subjectId . $specialityId;
	}
	
	/**
	 * エラーファイルの名前を返す
	 * @return string エラーログのファイル名
	 */
	public function getErrFile() {
		return $this->errFile;
	}
    
    /**
     * 類似在籍者情報のファイル名を返す
     * @return string 類似在籍者のファイル名
     */
    public function getSameStudentFile() {
        return $this->sameStudentFile;
    }
}
?>
