<?php
/**
 * 在籍者情報のCSVファイルをダウンロードする
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'Resource');
App::import('Model', 'StudentAllInfo');

class DownloadStudent {
	// 変数定義
	private $sexList;
	private $paymentList;
    private $supportList;
	private $sendingList;
	private $deliverList;
	private $aliveList;
	
	private $header = array(
		" ID","個人ID","氏名(カナ)","氏名(標準)","氏名(外字)"
        ,"旧姓氏名(カナ)","旧姓氏名","性別コード","性別","生年月日"
        ,"郵便番号","住所","電話番号","携帯番号","メールアドレス(PC)"
		,"メールアドレス(携帯)","保証人氏名","保証人郵便番号","保証人住所","保証人電話番号"
        ,"勤務先","勤務先電話番号","同窓会入会コード","同窓会入会","同窓会費未払額"
		,"大学賛助金・寄付コード","大学賛助金・寄付","高校賛助金","地域支部名","職域支部名"
        ,"各種諸団体","案内状送付可否コード","案内状送付可否","案内状不着コード","案内状不着"
        ,"生存情報コード","生存情報","備考"
	);
	
	// 定数定義
	const OUTPUT_FILE_NAME = 'student.csv';
	
	// コンストラクタ
	public function __construct() {
		// リソースのデータを取得する
		$resource = ClassRegistry::init('Resource');
		$this->sexList = $resource->getResourceList(Resource::SEX);
		$this->paymentList = $resource->getResourceList(Resource::FEE_PAYMENT);
        $this->supportList = $resource->getResourceList(Resource::SUPPORT_PAYMENT);
		$this->sendingList = $resource->getResourceList(Resource::INVITATION_SENDING);
		$this->deliverList = $resource->getResourceList(Resource::INVITATION_DELIVERY);
		$this->aliveList = $resource->getResourceList(Resource::ALIVE_INFO);
	}

	/**
	 * 在籍者情報のCSVファイルを作成してダウンロードする
	 * @param array $condition 出力する在籍者の検索条件
	 */
	public function download($condition) {
		// 実行時間を無制限にする
		set_time_limit(0);
		
		// ダウンロードする件数によってはメモリを大量に消費するのでサイズを大きくする
		ini_set("memory_limit","1274M");

		// 出力条件を設定
		$params['fields'] = array(
			'DISTINCT ON (name_kana, id) name_kana'
			, 'id', 'personal_id', 'name_kana', 'name', 'name_ext'
			, 'name_old_kana', 'name_old', 'sex', 'birthday', 'zip'
            , 'address', 'tel', 'mobile_phone', 'mail_address_pc', 'mail_address_mp'
            , 'guarantor_name', 'guarantor_zip', 'guarantor_address', 'guarantor_tel', 'company'
            , 'company_tel', 'payment_flag', 'unsettled_price', 'support_flag', 'support_price'
            , 'area_branch_name', 'job_branch_name', 'group_name', 'sending_flag', 'deliver_ng_flag'
            , 'alive_status', 'notes'
		);
		$params['conditions'] = $condition;
		$params['order'] = array('name_kana' => 'asc', 'id' => 'asc');
		
		// 出力する内容を取得
		$studentAllInfoDao = ClassRegistry::init('StudentAllInfo');
		$studentAllInfoDao->recursive = -1;
		$studentDto = $studentAllInfoDao->find('all', $params);
		
		// 全体を保持する配列にヘッダ情報を登録
		$outputBuffer = array(mb_convert_encoding(implode(',', $this->header), 'sjis-win', 'utf-8'));
		
		// ボディの出力
		foreach ($studentDto as $dto) {
			// 出力内容を配列に保持
			$student = array();
			$student[] = '"'.$dto['StudentAllInfo']['id'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['personal_id'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['name_kana'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['name'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['name_ext'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['name_old_kana'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['name_old'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['sex'].'"';
			$student[] = '"'.$this->sexList[$dto['StudentAllInfo']['sex']].'"';
			$student[] = '"'.str_replace('-', '/', $dto['StudentAllInfo']['birthday']).'"';
			$student[] = '"'.$dto['StudentAllInfo']['zip'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['address'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['tel'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['mobile_phone'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['mail_address_pc'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['mail_address_mp'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['guarantor_name'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['guarantor_zip'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['guarantor_address'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['guarantor_tel'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['company'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['company_tel'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['payment_flag'].'"';
			$student[] = '"'.$this->paymentList[$dto['StudentAllInfo']['payment_flag']].'"';
			$student[] = '"'.$dto['StudentAllInfo']['unsettled_price'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['support_flag'].'"';
			$student[] = '"'.$this->supportList[$dto['StudentAllInfo']['support_flag']].'"';
			$student[] = '"'.$dto['StudentAllInfo']['support_price'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['area_branch_name'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['job_branch_name'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['group_name'].'"';
			$student[] = '"'.$dto['StudentAllInfo']['sending_flag'].'"';
			$student[] = '"'.$this->sendingList[$dto['StudentAllInfo']['sending_flag']].'"';
			$student[] = '"'.$dto['StudentAllInfo']['deliver_ng_flag'].'"';
			$student[] = '"'.$this->deliverList[$dto['StudentAllInfo']['deliver_ng_flag']].'"';
			$student[] = '"'.$dto['StudentAllInfo']['alive_status'].'"';
			$student[] = '"'.$this->aliveList[$dto['StudentAllInfo']['alive_status']].'"';
			$student[] = '"'.str_replace(array("\r\n", "\r", "\n"), "　", $dto['StudentAllInfo']['notes']).'"';

			// エンコーディングをSJISに変換して出力
			$outputBuffer[] = mb_convert_encoding(implode(',', $student), 'sjis-win', 'utf-8');
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
