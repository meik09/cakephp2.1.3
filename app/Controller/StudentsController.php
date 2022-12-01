<?php
/*
 * 在籍者を管理するコントローラ
 *
 * @author Naoya.Kuga
 */
App::import('Vendor', 'DownloadStudent');
App::import('Vendor', 'DownloadSeminar');
App::import('Vendor', 'DownloadClub');
App::import('Vendor', 'DownloadHistory');
App::import('Vendor', 'DownloadSupportPrice');
App::import('Vendor', 'DownloadAlumni');

class StudentsController extends AppController {
	public $name = 'Students';
	public $uses = array('Student', 'StudentAllInfo');

	public $helpers = array('PaginatorEx', 'StringUtils');

	// 定数宣言
	const FIND_KEY = 'STUDENT_FIND_CONDITION';
	const LIST_PAGE = 'STUDENT_LIST_PAGE_NO';
	
	const DOWNLOAD_STUDENT = 1;
	const DOWNLOAD_SEMINAR = 2;
	const DOWNLOAD_CLUB = 3;
	const DOWNLOAD_HISTORY = 4;
    const DOWNLOAD_SUPPORT = 5;
    const DOWNLOAD_ALUMNI = 6;

	/**
	 * 在籍者一覧を表示 
	 */
	public function index($menu = 0) {
		// 表示ページ数を宣言
		$pageNo = 1;

		// 検索条件を復元
		if ($menu == 1) {
			// 検索条件が保持されていたら削除する
			if (self::checkSessionVar($this->name, self::FIND_KEY)) self::deleteSessionVar($this->name, self::FIND_KEY);
		} else {
			// 検索でない場合は検索条件を復元
			if (!parent::isClick('find')) {
				// 検索条件が保持されていたら復元する
				if (self::checkSessionVar($this->name, self::FIND_KEY)) $this->data = array('vmoStudent' => self::getSessionVar($this->name, self::FIND_KEY));
				
				// ページ番号がパラメタにあれば、その番号を保持
				if (!empty($this->params['named']['page'])) {
					$pageNo = $this->params['named']['page'];
				} else {
					if (self::checkSessionVar($this->name, self::LIST_PAGE)) $pageNo = self::getSessionVar($this->name, self::LIST_PAGE);
				}
			}
		}

		// 検索条件があれば検索
		$displayRec = array();
		if (!empty($this->data['vmoStudent'])) {
			// 検索条件を生成
			$this->loadModel('VmoStudent');
			$this->VmoStudent->create();
			
			// 値をセットする
			$this->VmoStudent->set($this->data['vmoStudent']);
			
			// バリデーションチェック
			if ($this->VmoStudent->validates()) {
				// バリデーションが通ったので検索条件を取得
				$condition = $this->data['vmoStudent'];
				$this->paginate['conditions'] = $this->VmoStudent->getCondition4List($condition);

				// DISTINCTを使用したpaginateを使用するためのフィールド定義
				$studentAllInfoDao = ClassRegistry::init('StudentAllInfo');
				$studentAllInfoDao->distinctPaginateFields = array(
					'DISTINCT ON (name_kana, id) name_kana AS "StudentAllInfo__name_kana"'
					, 'id', 'college_student_no', 'name', 'name_kana', 'name_ext'
                    , 'sex', 'address', 'birthday', 'college_semi_seminar_teacher_name', 'high_semi_seminar_teacher_name'
                    , 'junior_semi_seminar_teacher_name', 'college_club_club_name', 'high_club_club_name', 'junior_club_club_name', 'area_branch_name'
                    , 'job_branch_name', 'sending_flag', 'college_id', 'high_id', 'junior_id'
				);

				// ページネータの値をセット
				$this->paginate['fields'] = $studentAllInfoDao->distinctPaginateFields;
				$this->paginate['order'] = array(
					'name_kana' => 'asc'
					, 'id' => 'asc'
					, 'birthday' => 'asc'
					, 'college_id' => 'asc'
					, 'high_id' => 'asc'
					, 'junior_id' => 'desc'
				);
				$this->paginate['page'] = $pageNo;
				
				// 値を取得
				$displayRec = $this->paginate('StudentAllInfo');

				// 検索条件とページ条件をセッションに保持
				parent::setSessionVar($this->name, self::FIND_KEY, $this->data['vmoStudent']);
				parent::setSessionVar($this->name, self::LIST_PAGE, $pageNo);

			} else {
				// バリデーションが通らなかったのでメッセージ表示
				parent::setMessage('検索条件に誤りがあります。条件を修正してやり直してください。', LOG_INFO);
			}
		}

		// タイトルをセット
		$this->set('title_for_layout', '在学生一覧');

		// リソースを取得
		$resource = ClassRegistry::init('Resource');
		$this->set('sendingType', $resource->getResourceList(Resource::INVITATION_SENDING));
        $this->set('aliveType', $resource->getResourceList(Resource::ALIVE_INFO));
        $this->set('sex', $resource->getResourceList(Resource::SEX));

		// 一覧に表示する値をセット
		$this->set('recs', $displayRec);
	}
	
	/**
	 * 在籍者情報を新規登録する  
	 */
	public function add() {
		// 値が渡された場合は登録処理を行う
		if (!empty($this->data)) {
			// トランザクション(登録)開始
			$this->Student->begin();
			$saveFlg = true;
			$errMsg = "在籍者の登録に失敗しました";

			// 在籍者の基本情報を登録
			$saveFlg = $this->Student->save($this->data['Student']);
            
            // 賛助金履歴
            if ($saveFlg) {
                $supportDao = ClassRegistry::init('StudentSupportPrice');
                $saveModel = $supportDao->getSaveModels4Add($this->Student->getLastInsertID(), $this->data['SupportPrice']);
				if (count($saveModel) > 0) {
					$result = $supportDao->saveAll($saveModel, array('atomic' => false));
					$saveFlg = $supportDao->isSuccess4SaveAll($result);
				}
            }
			
			// 所属ゼミの情報を登録
			if ($saveFlg) {
				$seminarDao = ClassRegistry::init('StudentSeminar');
				$saveModel = $seminarDao->getSaveModels4Add($this->Student->getLastInsertID(), $this->data['CollegeSeminar'], $this->data['HighSeminar'], $this->data['JuniorSeminar']);
				if (count($saveModel) > 0) {
					$result = $seminarDao->saveAll($saveModel, array('atomic' => false));
					$saveFlg = $seminarDao->isSuccess4SaveAll($result);
				}
			}
			
			// 所属サークルの情報を登録
			if ($saveFlg) {
				$clubDao = ClassRegistry::init('StudentClub');
				$saveModel = $clubDao->getSaveModels4Add($this->Student->getLastInsertID(), $this->data['CollegeClub'], $this->data['HighClub'], $this->data['JuniorClub']);
				if (count($saveModel) > 0) {
					$result = $clubDao->saveAll($saveModel, array('atomic' => false));
					$saveFlg = $clubDao->isSuccess4SaveAll($result);
				}
			}
			
			// 入学履歴の情報を登録
			if ($saveFlg) {
				$historyDao = ClassRegistry::init('StudentHistory');
				$saveModel = $historyDao->getSaveModels4Add($this->Student->getLastInsertID(), $this->data['CollegeHistory'], $this->data['HighHistory'], $this->data['JuniorHistory']);
				if (count($saveModel) > 0) {
					$result = $historyDao->saveAll($saveModel, array('atomic' => false));
					$saveFlg = $historyDao->isSuccess4SaveAll($result);
				} else {
					$saveFlg = false;
					$errMsg = "有効な入学履歴を1つ以上は入力して下さい";
				}
			}
			
			// 保存結果
			if ($saveFlg) {
				$this->Student->commit();
				parent::setMessage('在籍者の情報を登録しました', LOG_INFO);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Student->rollback();
				parent::setMessage($errMsg, LOG_WARNING);
			}
		} else {
			// 値が無い場合には初期値を取得
			$this->data = $this->Student->getDefault();
		}
		
		// タイトルをセット
		$this->set('title_for_layout', '在籍者登録');
		
		// リソースを取得
		$resource = ClassRegistry::init('Resource');
		$this->set('studentCollegeType', $resource->getStudentType(Resource::STUDENT_TYPE_COLLEGE));
		$this->set('studentHighType', $resource->getStudentType(Resource::STUDENT_TYPE_HIGH));
		$this->set('studentJuniorType', $resource->getStudentType(Resource::STUDENT_TYPE_JUNIOR));
		$this->set('sex', $resource->getResourceList(Resource::SEX));
		$this->set('payment', $resource->getResourceList(Resource::FEE_PAYMENT));
        $this->set('support', $resource->getResourceList(Resource::SUPPORT_PAYMENT));
		$this->set('sending', $resource->getResourceList(Resource::INVITATION_SENDING));
		$this->set('alive', $resource->getResourceList(Resource::ALIVE_INFO));
		$this->set('clubType', $resource->getResourceList(Resource::CLUB_TYPE));
		$this->set('post', $resource->getResourceList(Resource::CLUB_POST));

		// 不着情報はチェックボックスで表現するのでkeyとvalueを別々に取り出し
		$deliver = $resource->getResourceList(Resource::INVITATION_DELIVERY);
		$this->set('deliverKey', array_keys($deliver));
		$this->set('deliverValue', array_values($deliver));
	}
	
	/**
	 * 在籍者情報を更新する 
	 */
	public function edit($id = null) {
		// IDがなければエラーとする
		if (empty($id)) {
			parent::setMessage('値が不正です', LOG_WARNING);
			$this->redirect(array('action' => 'index'));
		}
		
		if (!empty($this->data)) {
			if (parent::isClick('update')) {
				// トランザクション(登録)開始
				$this->Student->begin();
				$saveFlg = true;
				$errMsg = "在籍者の更新に失敗しました";

				// 在籍者情報を更新
				$saveFlg = $this->Student->save($this->data['Student']);
                
                // 賛助金履歴の削除
                $support = ClassRegistry::init('StudentSupportPrice');
                if ($saveFlg) {
					$deleteId = $support->getIds4Delete($this->data['SupportPrice']);
					if (count($deleteId) > 0) $saveFlg = $support->deleteAll(array('id' => $deleteId));                    
                }
                
                // 賛助金履歴の追加と修正
                if ($saveFlg) {
					$saveModel = $support->getSaveModel4Edit($this->data['Student']['id'], $this->data['SupportPrice']);
					if (count($saveModel) > 0) {
						$result = $support->saveAll($saveModel, array('atomic' => false));
						$saveFlg = $support->isSuccess4SaveAll($result);
					}
				}

				// 入学履歴情報の削除
				$history = ClassRegistry::init('StudentHistory');
				if ($saveFlg) {
					$deleteId = $history->getIds4Delete($this->data['CollegeHistory'], $this->data['HighHistory'], $this->data['JuniorHistory']);
					if (count($deleteId) > 0) $saveFlg = $history->deleteAll(array('id' => $deleteId));
				}
				
				// 入学履歴情報の追加と修正
				if ($saveFlg) {
					$saveModel = $history->getSaveModel4Edit($this->data['Student']['id'], $this->data['CollegeHistory'], $this->data['HighHistory'], $this->data['JuniorHistory']);
					if (count($saveModel) > 0) {
						$result = $history->saveAll($saveModel, array('atomic' => false));
						$saveFlg = $history->isSuccess4SaveAll($result);
					} else {
						$saveFlg = false;
						$errMsg = "有効な入学履歴を1つ以上は入力して下さい";
					}
				}
				
				// ゼミ情報の削除
				$seminar = ClassRegistry::init('StudentSeminar');
				if ($saveFlg) {
					$deleteId = $seminar->getIds4Delete($this->data['CollegeSeminar'], $this->data['HighSeminar'], $this->data['JuniorSeminar']);
					if (count($deleteId) > 0) $saveFlg = $seminar->deleteAll(array('id' => $deleteId));
				}
				
				// ゼミ情報の追加と修正
				if ($saveFlg) {
					$saveModel = $seminar->getSaveModel4Edit($this->data['Student']['id'], $this->data['CollegeSeminar'], $this->data['HighSeminar'], $this->data['JuniorSeminar']);
					if (count($saveModel) > 0) {
						$result = $seminar->saveAll($saveModel, array('atomic' => false));
						$saveFlg = $seminar->isSuccess4SaveAll($result);
					}
				}
				
				// サークル情報の削除
				$club = ClassRegistry::init('StudentClub');
				if ($saveFlg) {
					$deleteId = $club->getIds4Delete($this->data['CollegeClub'], $this->data['HighClub'], $this->data['JuniorClub']);
					if (count($deleteId) > 0) $saveFlg = $club->deleteAll(array('id' => $deleteId));
				}
				
				// サークル情報の追加と修正
				if ($saveFlg) {
					$saveModel = $club->getSaveModel4Edit($this->data['Student']['id'], $this->data['CollegeClub'], $this->data['HighClub'], $this->data['JuniorClub']);
					if (count($saveModel) > 0) {
						$result = $club->saveAll($saveModel, array('atomic' => false));
						$saveFlg = $club->isSuccess4SaveAll($result);
					}
				}
				
				// 更新結果
				if ($saveFlg) {
					$this->Student->commit();
					parent::setMessage('在籍者の情報を更新しました', LOG_INFO);
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Student->rollback();
					parent::setMessage($errMsg, LOG_WARNING);
				}
			} else if (parent::isClick('delete')) {
				// トランザクション開始(4テーブルの情報を削除)
				$this->Student->begin();
				$saveFlg = true;
				
                // 賛助金履歴を削除
				$support = ClassRegistry::init('StudentSupportPrice');
				$saveFlg = $support->deleteAll(array('student_id' => $this->data['Student']['id']), false);

				// ゼミ情報を削除
				$seminar = ClassRegistry::init('StudentSeminar');
				$saveFlg = $seminar->deleteAll(array('student_id' => $this->data['Student']['id']), false);
				
				// サークル情報を削除
				if ($saveFlg) {
					$club = ClassRegistry::init('StudentClub');
					$saveFlg = $club->deleteAll(array('student_id' => $this->data['Student']['id']), false);
				}
				
				// 入学履歴情報を削除
				if ($saveFlg) {
					$history = ClassRegistry::init('StudentHistory');
					$saveFlg = $history->deleteAll(array('student_id' => $this->data['Student']['id']), false);
				}
				
				// 在籍者情報を削除
				if ($saveFlg) $saveFlg = $this->Student->delete($this->data['Student']['id']);
				
				// 削除処理
				if ($saveFlg) {
					$this->Student->commit();
					parent::setMessage('在籍者を削除しました', LOG_INFO);
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Student->rollback();
					parent::setMessage('在籍者の削除に失敗しました', LOG_WARNING);
				}
			}
		}
		// 画面に表示するレコードを取得
		$student = $this->Student->findById($id);
		
		// 画面からPOSTされたデータがあれば、その内容で上書きする
		if (!empty($this->data['Student'])) $student['Student'] = $this->data['Student'];
		if (!empty($this->data['CollegeHistory'])) $student['CollegeHistory'] = $this->data['CollegeHistory'];
		if (!empty($this->data['CollegeSeminar'])) $student['CollegeSeminar'] = $this->data['CollegeSeminar'];
		if (!empty($this->data['CollegeClub'])) $student['CollegeClub'] = $this->data['CollegeClub'];
		if (!empty($this->data['HighHistory'])) $student['HighHistory'] = $this->data['HighHistory'];
		if (!empty($this->data['HighSeminar'])) $student['HighSeminar'] = $this->data['HighSeminar'];
		if (!empty($this->data['HighClub'])) $student['HighClub'] = $this->data['HighClub'];
		if (!empty($this->data['JuniorHistory'])) $student['JuniorHistory'] = $this->data['JuniorHistory'];
		if (!empty($this->data['JuniorSeminar'])) $student['JuniorSeminar'] = $this->data['JuniorSeminar'];
		if (!empty($this->data['JuniorClub'])) $student['JuniorClub'] = $this->data['JuniorClub'];
		if (!empty($this->data['SupportPrice'])) $student['SupportPrice'] = $this->data['SupportPrice'];
				
		// 在籍者情報の値をセット
		$this->data = $student;
		
		// タイトルをセット
		$this->set('title_for_layout', '在籍者更新');
		
		// リソースを取得
		$resource = ClassRegistry::init('Resource');
        $this->set('studentCollegeType', $resource->getStudentType(Resource::STUDENT_TYPE_COLLEGE));
		$this->set('studentHighType', $resource->getStudentType(Resource::STUDENT_TYPE_HIGH));
		$this->set('studentJuniorType', $resource->getStudentType(Resource::STUDENT_TYPE_JUNIOR));
		$this->set('sex', $resource->getResourceList(Resource::SEX));
		$this->set('payment', $resource->getResourceList(Resource::FEE_PAYMENT));
        $this->set('support', $resource->getResourceList(Resource::SUPPORT_PAYMENT));
		$this->set('sending', $resource->getResourceList(Resource::INVITATION_SENDING));
		$this->set('alive', $resource->getResourceList(Resource::ALIVE_INFO));
		$this->set('clubType', $resource->getResourceList(Resource::CLUB_TYPE));
		$this->set('post', $resource->getResourceList(Resource::CLUB_POST));

		// 不着情報はチェックボックスで表現するのでkeyとvalueを別々に取り出し
		$deliver = $resource->getResourceList(Resource::INVITATION_DELIVERY);
		$this->set('deliverKey', array_keys($deliver));
		$this->set('deliverValue', array_values($deliver));
	}
	
	/**
	 * 在籍者の情報をCSVファイルでダウンロードする
	 * @param int $type ダウンロードする情報のタイプ
	 */
	public function download($type = '') {
		// typeが一致しない場合はエラーにする
		if (($type != self::DOWNLOAD_STUDENT) && ($type != self::DOWNLOAD_SEMINAR) &&
			($type != self::DOWNLOAD_HISTORY) && ($type != self::DOWNLOAD_CLUB) &&
            ($type != self::DOWNLOAD_SUPPORT) && ($type != self::DOWNLOAD_ALUMNI)) {
			parent::setMessage('ダウンロードタイプが不正です', LOG_WARNING);
			$this->redirect(array('action' => 'index'));
		}

		// 画面を自動レンダリングしない
		$this->autoRender = false;
		
		// セッションの検索条件を復元する
		$condition = self::getSessionVar($this->name, self::FIND_KEY);

		// 検索条件を生成
		$this->loadModel('VmoStudent');
		$this->VmoStudent->create();
		$condition = $this->VmoStudent->getCondition4List($condition);
		
		// CSVを生成する
		switch ($type) {
			case self::DOWNLOAD_STUDENT:	// 在籍者情報
				$downloader = ClassRegistry::init('DownloadStudent');
				break;
			case self::DOWNLOAD_SEMINAR:	// 所属ゼミ情報
				$downloader = ClassRegistry::init('DownloadSeminar');
				break;
			case self::DOWNLOAD_CLUB:		// 所属サークル情報
				$downloader = ClassRegistry::init('DownloadClub');
				break;
			case self::DOWNLOAD_HISTORY:	// 入学履歴情報
				$downloader = ClassRegistry::init('DownloadHistory');
				break;
            case self::DOWNLOAD_SUPPORT:      // 賛助金履歴
                $downloader = ClassRegistry::init('DownloadSupportPrice');
                break;
            case self::DOWNLOAD_ALUMNI:
                $downloader = ClassRegistry::init('DownloadAlumni');
                break;
		}
		$downloader->download($condition);
	}
}
?>
