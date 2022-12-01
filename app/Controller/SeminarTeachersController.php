<?php
/*
 * ゼミ教員の管理を行うコントローラ
 *
 * @author Naoya.Kuga
 */
class SeminarTeachersController extends AppController {
	public $name = 'SeminarTeachers';
	public $uses = array('SeminarTeacher');

	public $helpers = array('PaginatorEx');

	// 定数定義
	const FIND_KEY = 'SEMINAR_TEACHER_FIND_CONDITION';
	const LIST_PAGE = 'SEMINAR_TEACHER_LIST_PAGE_NO';
	
	/**
	 * ゼミ教員一覧を表示 
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
				if (self::checkSessionVar($this->name, self::FIND_KEY)) $this->data = array('vmoSeminarTeacher' => self::getSessionVar($this->name, self::FIND_KEY));
				
				// ページ番号がパラメタにあれば、その番号を保持
				if (!empty($this->params['named']['page'])) {
					$pageNo = $this->params['named']['page'];
				} else {
					if (self::checkSessionVar($this->name, self::LIST_PAGE)) $pageNo = self::getSessionVar($this->name, self::LIST_PAGE);
				}
			}
		}
		
		// 検索条件があれば検索
		if (!empty($this->data['vmoSeminarTeacher'])) {
			// 検索条件を生成
			$this->loadModel('VmoSeminarTeacher');
			$this->VmoSeminarTeacher->create();
			
			// 値をセットする
			$this->VmoSeminarTeacher->set($this->data['vmoSeminarTeacher']);
			
			// バリデーションチェック
			if ($this->VmoSeminarTeacher->validates()) {
				// バリデーションが通ったので検索条件を取得
				$this->paginate['conditions'] = $this->VmoSeminarTeacher->getCondition4List();
				
				// 検索条件をセッションに保持
				parent::setSessionVar($this->name, self::FIND_KEY, $this->data['vmoSeminarTeacher']);
			} else {
				// バリデーションが通らなかったのでメッセージ表示
				parent::setMessage('検索条件に誤りがあります。条件を修正してやり直してください。', LOG_INFO);
			}
		} else {
			// 検索条件が無い場合には0件になるようにする
			$this->paginate['conditions'] = array('id' => null);
		}
		
		// 画面表示時にレイアウトを使用しないように変更
		$this->autoLayout = false;
		
		// タイトルをセット
		$this->set('title_for_layout', 'ゼミ教員検索');

		// ページ情報をセッションに保持
		self::setSessionVar($this->name, self::LIST_PAGE, $pageNo);
		
		// ページネータの値をセット
		$this->paginate['page'] = $pageNo;
		$this->paginate['order'] = array('name_kana' => 'asc');

		// 値を取得
		$this->set('recs', $this->paginate());
	}
}
?>
