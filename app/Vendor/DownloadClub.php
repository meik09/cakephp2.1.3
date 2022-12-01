<?php
/**
 * 所属サークル情報のCSVファイルをダウンロードする
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Model', 'StudentAllInfo');

class DownloadClub {
	// 変数定義
    private $studentTypeList;
	private $typeList;
	private $postList;
	
	private $header = array(
		"学生ID", "学生種別コード", "学生種別", "サークル分類コード", "サークル分類"
        , "サークル名", "サークル役職コード", "サークル役職名", "入部年月日", "退部年月日"
	);
	
	// 定数定義
	const OUTPUT_FILE_NAME = 'studentClub.csv';
	
	// コンストラクタ
	public function __construct() {
		// リソースのデータを取得する
		$resource = ClassRegistry::init('Resource');
		$this->studentTypeList = $resource->getResourceList(Resource::STUDENT_TYPE);
		$this->typeList = $resource->getResourceList(Resource::CLUB_TYPE);
		$this->postList = $resource->getResourceList(Resource::CLUB_POST);
	}

	/**
	 * 所属サークル情報のCSVファイルを作成してダウンロードする
	 * @param array $condition 出力する所属サークルの検索条件
	 */
	public function download($condition) {
		// 実行時間を無制限にする
		set_time_limit(0);
		
		// ダウンロードする件数によってはメモリを大量に消費するのでサイズを大きくする
		ini_set("memory_limit","512M");
		
		// サブクエリを作成
		$studentAllInfoDao = ClassRegistry::init('StudentAllInfo');
		$clubDao = ClassRegistry::init('StudentClub');
		$dbo = $clubDao->getDataSource();
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
		$options['order'] = array('StudentClub.student_id' => 'asc', 'in_date' => 'desc', 'StudentClub.id' => 'asc');
		$options['joins'][] = array(
						'type' => 'INNER'
						, 'table' => "({$subQuery})"
						, 'alias' => 'StudentAllInfo'
						, 'conditions' => '"StudentAllInfo"."id" = "StudentClub"."student_id"'
					);
		$clubDto = $clubDao->find('all', $options);
		
		// 全体を保持する配列にヘッダ情報を登録
		$outputBuffer = array(mb_convert_encoding(implode(',', $this->header), 'sjis-win', 'utf-8'));
		
		// ボディの出力
		foreach ($clubDto as $dto) {
			// 出力内容を配列に保持
			$club = array();
			$club[] = '"'.$dto['StudentClub']['student_id'].'"';
			$club[] = '"'.$dto['StudentClub']['student_type'].'"';
			$club[] = '"'.$this->studentTypeList[$dto['StudentClub']['student_type']].'"';
			$club[] = '"'.$dto['StudentClub']['club_type'].'"';
			$club[] = (isset($this->typeList[$dto['StudentClub']['club_type']])) ? '"'.$this->typeList[$dto['StudentClub']['club_type']].'"' : '""';
			$club[] = '"'.$dto['StudentClub']['club_name'].'"';
			$club[] = '"'.$dto['StudentClub']['club_post'].'"';
			$club[] = (isset($this->postList[$dto['StudentClub']['club_post']])) ? '"'.$this->postList[$dto['StudentClub']['club_post']].'"' : '""';
			$club[] = '"'.$dto['StudentClub']['in_date'].'"';
			$club[] = '"'.$dto['StudentClub']['out_date'].'"';

			// エンコーディングをSJISに変換して出力
			$outputBuffer[] = mb_convert_encoding(implode(',', $club), 'sjis-win', 'utf-8');
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
