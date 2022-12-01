<?php
/*
 * 仮データを上げるためのコントローラ
 *
 * @author Naoya.Kuga
 */

App::import('Model', 'Resource');
App::import('Vendor', 'ImportAlumni');
App::import('Vendor', 'ImportJuniorHigh');
App::import('Vendor', 'ImportCollege');
App::import('Vendor', 'ImportNewCollege');
App::import('Vendor', 'ImportSeminarHistory');
App::import('Vendor', 'ImportClubAdd');

class TemporariesController extends AppController {
	public $name = 'Temporaries';
	public $uses = array('VmoSystem');

	/**
	 * 仮データをインポート 
	 */
	public function index($menu = 0) {
		// 値が渡されれば処理を行う
		if (!empty($this->data)) {
			// ファイルが指定されていなければエラーとする
			if (!empty($this->data['VmoSystem']['uploadFileName']['name'])) {
				// インポートする情報によって処理を分岐
				switch ($this->data['VmoSystem']['upload_type']) {
					case Resource::BASE_DATA_ALUMNI:            // 1_大学卒業生データ
						$importer = ClassRegistry::init('ImportAlumni');
						break;
					case Resource::BASE_DATA_ADD_JINIOR_HIGH:   // 2_中高のみ卒業生データ
                        $importer = ClassRegistry::init('ImportJuniorHigh');
						break;
					case Resource::BASE_DATA_COLLEGE:           // 3_新規作成（退学・除籍・卒業(同窓会拒否者)）データ
                        $importer = ClassRegistry::init('ImportCollege');
						break;
					case Resource::BASE_DATA_NEW_COLLEGE:       // 6_2013-2015の大学・大学院の卒業・退学・除籍データ
                        $importer = ClassRegistry::init('ImportNewCollege');
						break;
                    case Resource::BASE_DATA_ADD_SEMINAR:       // 7_2013-2015のゼミ情報
                        $importer = ClassRegistry::init('ImportSeminarHistory');
                        break;
                    case Resource::BASE_DATA_CLUB:              // 8_2013-2015のサークル情報
                        $importer = ClassRegistry::init('ImportClubAdd');
                        break;
				}
                
				// インポート処理
				$result = $importer->import($this->data['VmoSystem']['uploadFileName']);
				
				// 結果を表示
				if ($result) {
					parent::setMessage('アップロードが完了しました。', LOG_INFO);
				} else {
					parent::setMessage('アップロード時にエラーの発生した行があります。', LOG_WARNING);
					$this->set('errFile', $importer->getErrFile());
				}
			} else {
				parent::setMessage('アップロードするファイルが指定されていません。', LOG_ERR);
			}
		}
		
		// タイトルをセット
		$this->set('title_for_layout', '基本データインポート');
		
		// リソース情報を取得
		$resource = ClassRegistry::init('Resource');
		$this->set('uploadType', $resource->getResourceList(Resource::BASE_DATA));
	}

}
