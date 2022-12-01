<?php
/**
 * 賛助金履歴情報のCSVファイルをダウンロードする
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'StudentAllInfo');

class DownloadSupportPrice {
	// 変数定義
	private $header = array(
		'学生ID', '年度', '金額'
	);
	
	// 定数定義
	const OUTPUT_FILE_NAME = 'studentSupportPrice.csv';
	
	// コンストラクタ
	public function __construct() {
		// No Action
	}

	/**
	 * 所属ゼミ情報のCSVファイルを作成してダウンロードする
	 * @param array $condition 出力する所属ゼミの検索条件
	 */
	public function download($condition) {
		// 実行時間を無制限にする
		set_time_limit(0);
		
		// ダウンロードする件数によってはメモリを大量に消費するのでサイズを大きくする
		ini_set("memory_limit","256M");
		
		// サブクエリを作成
		$studentAllInfoDao = ClassRegistry::init('StudentAllInfo');
		$seminarDao = ClassRegistry::init('StudentSupportPrice');
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
		$options['order'] = array('StudentSupportPrice.student_id' => 'asc', 'year' => 'desc', 'StudentSupportPrice.id' => 'asc');
		$options['joins'][] = array(
						'type' => 'INNER'
						, 'table' => "({$subQuery})"
						, 'alias' => 'StudentAllInfo'
						, 'conditions' => '"StudentAllInfo"."id" = "StudentSupportPrice"."student_id"'
					);
		$seminarDto = $seminarDao->find('all', $options);

		// 全体を保持する配列にヘッダ情報を登録
		$outputBuffer = array(mb_convert_encoding(implode(',', $this->header), 'sjis-win', 'utf-8'));
		
		// ボディの出力
		foreach ($seminarDto as $dto) {
			// 出力内容を配列に保持
			$seminar = array();
			$seminar[] = '"'.$dto['StudentSupportPrice']['student_id'].'"';
			$seminar[] = '"'.$dto['StudentSupportPrice']['year'].'"';
			$seminar[] = '"'.$dto['StudentSupportPrice']['price'].'"';

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
