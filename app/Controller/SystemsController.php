<?php
/*
 * システム管理を行うコントローラ
 * データのアップロード等を行う
 *
 * @author Naoya.Kuga
 */

App::import('Model', 'Resource');
App::import('Vendor', 'ImportStudent');
App::import('Vendor', 'ImportSeminar');
App::import('Vendor', 'ImportClub');
App::import('Vendor', 'ImportJuniorHighStudent');

class SystemsController extends AppController {
	public $name = 'Systems';
	public $uses = array('VmoSystem');

	/**
	 * システム管理を表示 
	 */
	public function index($menu = 0) {
		// 値が渡されれば処理を行う
		if (!empty($this->data)) {
			// ファイルが指定されていなければエラーとする
			if (!empty($this->data['VmoSystem']['uploadFileName']['name'])) {
				// インポートする情報によって処理を分岐
				switch ($this->data['VmoSystem']['upload_type']) {
					case Resource::UPLOAD_TYPE_STUDENT:		// 在籍者情報
						$importer = ClassRegistry::init('ImportStudent');
						break;
					case Resource::UPLOAD_TYPE_SEMINAR:		// ゼミ情報
						$importer = ClassRegistry::init('ImportSeminar');
						break;
					case Resource::UPLOAD_TYPE_CLUB:		// サークル情報
						$importer = ClassRegistry::init('ImportClub');
						break;
                    case Resource::UPLOAD_TYPE_JH_STUDENT:  // 在籍者情報（中学・高校）
                        $importer = ClassRegistry::init('ImportJuniorHighStudent');
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
                
                // 類似情報を表示
                if (strlen($importer->getSameStudentFile()) > 0) $this->set('sameStudentFile', $importer->getSameStudentFile());
			} else {
				parent::setMessage('アップロードするファイルが指定されていません。', LOG_ERR);
			}
		}
		
		// タイトルをセット
		$this->set('title_for_layout', 'システム管理');
		
		// リソース情報を取得
		$resource = ClassRegistry::init('Resource');
		$this->set('uploadType', $resource->getResourceList(Resource::UPLOAD_TYPE));
	}
}

