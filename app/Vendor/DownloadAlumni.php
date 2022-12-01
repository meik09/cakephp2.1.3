<?php
/**
 * 同窓会用のCSVファイルをダウンロードする
 *
 * @author Naoya.Kuga
 */
App::import('Model', 'StudentHistory');
App::import('Model', 'StudentAllInfo');

class DownloadAlumni {
    // 変数定義
	private $header = array(
        "個人ID", "卒業年月日", "学部学科／卒年-クラス", "氏名カナ", "氏名標準",
        "氏名外字", "旧姓氏名カナ", "旧姓氏名", "性別コード", "生年月日",
        "郵便番号", "住所", "旧郵便番号", "旧住所", "電話番号", "携帯番号", "メールアドレスPC",
        "メールアドレス携帯", "保証人氏名", "保証人郵便番号", "保証人住所", "保証人電話番号",
        "勤務先", "勤務先電話番号", "同窓会入会コード", "同窓会費未払額", "大学賛助金・寄付コード",
        "高校賛助金", "地域支部名", "職域支部名", "各種諸団体", "案内状送付可否コード",
        "案内状不着コード", "生存情報コード", "備考"
    );
    
    private $alumniRefusal01 = "同窓会入会辞退";
    private $alumniRefusal02 = "同窓会未入会";
    private $graduate = '卒業';
	private $completion = '修了';
	private $end = '終了';

    // 定数定義
	const OUTPUT_FILE_NAME = 'studentAlumni.csv';
	
	// コンストラクタ
	public function __construct() {
        // No Action
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

        // 条件を同窓会CSV用に変更
        $condition = $this->setCondition($condition);

		// 出力条件を設定
		$params['fields'] = array(
			'DISTINCT ON (name_kana, id) name_kana'
			, 'id', 'personal_id', 'name_kana', 'name', 'name_ext'
			, 'name_old_kana', 'name_old', 'sex', 'birthday', 'zip'
            , 'address', 'zip_old', 'address_old', 'tel', 'mobile_phone', 'mail_address_pc', 'mail_address_mp'
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
		$studentAllDto = $studentAllInfoDao->find('all', $params);
		
		// 全体を保持する配列にヘッダ情報を登録
		$outputBuffer = array(mb_convert_encoding(implode(',', $this->header), 'sjis-win', 'utf-8'));
		
		// ボディの出力
        $historyDao = ClassRegistry::init('StudentHistory');
        $historyDao->recursive = -1;
		foreach ($studentAllDto as $dto) {
            // 入学履歴を再取得
            $histories = $historyDao->getAlumniDownloadList($dto['StudentAllInfo']['id'], $condition);
            
            foreach ($histories as $historyDto) {
                // 出力内容を配列に保持
                $student = array();
                $student[] = '"'.$dto['StudentAllInfo']['personal_id'].'"';
                $student[] = '"'.$historyDto['StudentHistory']['graduation_date'].'"';
                $student[] = '"'.$historyDto['StudentHistory']['faculty_name'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['name_kana'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['name'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['name_ext'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['name_old_kana'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['name_old'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['sex'].'"';
                $student[] = '"'.str_replace('-', '/', $dto['StudentAllInfo']['birthday']).'"';
                $student[] = '"'.$dto['StudentAllInfo']['zip'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['address'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['zip_old'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['address_old'].'"';
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
                $student[] = '"'.$dto['StudentAllInfo']['unsettled_price'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['support_flag'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['support_price'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['area_branch_name'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['job_branch_name'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['group_name'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['sending_flag'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['deliver_ng_flag'].'"';
                $student[] = '"'.$dto['StudentAllInfo']['alive_status'].'"';
                $student[] = '"'.str_replace(array("\r\n", "\r", "\n"), "　", $dto['StudentAllInfo']['notes']).'"';

                // エンコーディングをSJISに変換して出力
                $outputBuffer[] = mb_convert_encoding(implode(',', $student), 'sjis-win', 'utf-8');
            }
		}
		
		// ヘッダの設定
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="' . self::OUTPUT_FILE_NAME . '"');
		header('Cache-Control: private');
		header('Pragma:');
		
		// 保持していた情報を書き出す
		print(implode(PHP_EOL, $outputBuffer));
    }
    
    /**
     * 検索条件を追加
     * @param array $condition 検索条件
     * @return array 変更された検索条件
     */
    private function setCondition($condition) {
        // 大学院・法科が選択されている場合の条件を追加
        if (isset($condition['grad_student_type']) && !isset($condition['college_student_type']) &&
            !isset($condition['high_student_type']) && !isset($condition['junior_student_type'])
        ) {
            // 学生種別を追加
            $condition['grad_student_type'] = array(Resource::STUDENT_TYPE_GRADUATE, Resource::STUDENT_TYPE_MASTER, Resource::STUDENT_TYPE_SPECIAL);

            // 在籍状態区分を追加
            $gradCondition[] = array('or' => array(
                'grad_student_status like' => '%' . $this->graduate . '%',
                'or' => array(
                    'grad_student_status like' => '%' . $this->completion . '%',
                    'or' => array(
                        'grad_student_status like' => '%' . $this->end . '%'
                    )
                )
            ));

            // 備考の条件を追加
            $gradCondition[] = array(
                "coalesce(notes, '') not like" => '%' . $this->alumniRefusal01 . '%',
                'and' => array(
                    "coalesce(notes, '') not like" => '%' . $this->alumniRefusal02 . '%',
                )
            );

            $condition['and'] = $gradCondition;

        } else {
            // 備考の条件のみ追加
            $condition = array_merge($condition, array("coalesce(notes, '') not like" => '%' . $this->alumniRefusal01 . '%'));
        }

        return $condition;
    }
}
