<?php
/**
 * 在籍者情報のCSVファイルをダウンロードする
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Model', 'StudentAllInfo');

class DownloadSeminar {
	// 変数定義
    private $studentTypeList;
	
	private $header = array(
		'学生ID', '学生種別コード', '学生種別', 'ゼミ教員名', '履修年度'
        , 'ゼミ成績'
	);
	
	// 定数定義
	const OUTPUT_FILE_NAME = 'studentSeminar.csv';
	
	// コンストラクタ
	public function __construct() {
		// リソースのデータを取得する
		$resource = ClassRegistry::init('Resource');
		$this->studentTypeList = $resource->getResourceList(Resource::STUDENT_TYPE);
	}

	/**
	 * 所属ゼミ情報のCSVファイルを作成してダウンロードする
	 * @param array $condition 出力する所属ゼミの検索条件
	 */
	public function download($condition) {
		// 実行時間を無制限にする
		set_time_limit(0);
		
		// ダウンロードする件数によってはメモリを大量に消費するのでサイズを大きくする
		ini_set("memory_limit","512M");
		
		// サブクエリを作成
		$studentAllInfoDao = ClassRegistry::init('StudentAllInfo');
		$seminarDao = ClassRegistry::init('StudentSeminar');
		$dbo = $seminarDao->getDataSource();
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
		$options['order'] = array('StudentSeminar.student_id' => 'asc', 'completion_year' => 'desc', 'StudentSeminar.id' => 'asc');
		$options['joins'][] = array(
						'type' => 'INNER'
						, 'table' => "({$subQuery})"
						, 'alias' => 'StudentAllInfo'
						, 'conditions' => '"StudentAllInfo"."id" = "StudentSeminar"."student_id"'
					);
		$seminarDto = $seminarDao->find('all', $options);

		// 全体を保持する配列にヘッダ情報を登録
		$outputBuffer = array(mb_convert_encoding(implode(',', $this->header), 'sjis-win', 'utf-8'));
		
		// ボディの出力
		foreach ($seminarDto as $dto) {
			// 出力内容を配列に保持
			$seminar = array();
			$seminar[] = '"'.$dto['StudentSeminar']['student_id'].'"';
			$seminar[] = '"'.$dto['StudentSeminar']['student_type'].'"';
			$seminar[] = '"'.$this->studentTypeList[$dto['StudentSeminar']['student_type']].'"';
			$seminar[] = '"'.$dto['StudentSeminar']['seminar_teacher_name'].'"';
			$seminar[] = '"'.$dto['StudentSeminar']['completion_year'].'"';
			$seminar[] = '"'.$dto['StudentSeminar']['seminar_result'].'"';

			// エンコーディングをSJISに変換して出力
			$outputBuffer[] = mb_convert_encoding(implode(',', $seminar), 'sjis-win', 'utf-8');
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
