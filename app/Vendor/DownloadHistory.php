<?php
/**
 * 入学履歴情報のCSVファイルをダウンロードする
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Model', 'StudentHistory');

class DownloadHistory {
	// 変数定義
	private $studentTypeList;
	
	private $header = array(
		"学生ID", "在学番号", "学生種別コード", "学生種別", "学籍状態区分情報"
        , "学部学科専攻名", "入学年月日", "卒業(退学･除籍)年月日", "卒業後進路", "出身校"
	);
	
	// 定数定義
	const OUTPUT_FILE_NAME = 'studentHistory.csv';
	
	// コンストラクタ
	public function __construct() {
		// リソースのデータを取得する
		$resource = ClassRegistry::init('Resource');
		$this->studentTypeList = $resource->getResourceList(Resource::STUDENT_TYPE);
	}

	/**
	 * 入学履歴情報のCSVファイルを作成してダウンロードする
	 * @param array $condition 出力する入学履歴の検索条件
	 */
	public function download($condition) {
		// 実行時間を無制限にする
		set_time_limit(0);
		
		// ダウンロードする件数によってはメモリを大量に消費するのでサイズを大きくする
		ini_set("memory_limit","768M");
		
		// サブクエリを作成
		$studentAllInfoDao = ClassRegistry::init('StudentAllInfo');
		$historyDao = ClassRegistry::init('StudentHistory');
		$dbo = $historyDao->getDataSource();
		$subQuery = $dbo->buildStatement(array(
						'fields'     => array("DISTINCT ON (id) id"),
						'table'      => 'student_all_infos',
						'alias'      => 'StudentAllInfo',
						'limit'      => null,
						'offset'     => null,
						'joins'      => array(),
						'conditions' => $condition,
						'order'      => null,
						'group'      => null),
					$studentAllInfoDao);
		
		// 出力内容を設定
		$options = array();
		$options['order'] = array('StudentHistory.student_id' => 'asc', 'admission_date' => 'desc', 'StudentHistory.id' => 'asc');
		$options['joins'][] = array(
						'type' => 'INNER'
						, 'table' => "({$subQuery})"
						, 'alias' => 'StudentAllInfo'
						, 'conditions' => '"StudentAllInfo"."id" = "StudentHistory"."student_id"'
					);
		$historyDto = $historyDao->find('all', $options);
		
		// 全体を保持する配列にヘッダ情報を登録
		$outputBuffer = array(mb_convert_encoding(implode(',', $this->header), 'sjis-win', 'utf-8'));
		
		// ボディの出力
		foreach ($historyDto as $dto) {
			// 出力内容を配列に保持
			$history = array();
			$history[] = '"'.$dto['StudentHistory']['student_id'].'"';
			$history[] = '"'.$dto['StudentHistory']['student_no'].'"';
			$history[] = '"'.$dto['StudentHistory']['student_type'].'"';
			$history[] = '"'.$this->studentTypeList[$dto['StudentHistory']['student_type']].'"';
			$history[] = '"'.$dto['StudentHistory']['student_status'].'"';
			$history[] = '"'.$dto['StudentHistory']['faculty_name'].'"';
			$history[] = '"'.str_replace('-', '/', $dto['StudentHistory']['admission_date']).'"';
			$history[] = '"'.str_replace('-', '/', $dto['StudentHistory']['graduation_date']).'"';
			$history[] = '"'.$dto['StudentHistory']['graduation_course'].'"';
			$history[] = '"'.$dto['StudentHistory']['alma_mater_name'].'"';

			// エンコーディングをSJISに変換して出力
			$outputBuffer[] = mb_convert_encoding(implode(',', $history), 'sjis-win', 'utf-8');
		}
		
		// ヘッダの設定
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="' . self::OUTPUT_FILE_NAME . '"');
		header('Cache-Control: private');
		header('Pragma:');
		
		// 保持していた情報を書き出す
		print(implode(PHP_EOL, $outputBuffer));
	}
}
?>
