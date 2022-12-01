<?php
/*
 * 出身校の管理を行うコントローラ
 *
 * @author Naoya.Kuga
 */
class AlmaMatersController extends AppController {
	public $name = 'AlmaMaters';
	public $uses = array('AlmaMater');

	public $helpers = array('PaginatorEx');

	// 定数定義
	const FIND_KEY = 'ALMA_MATER_FIND_CONDITION';
	const LIST_PAGE = 'ALMA_MATER_LIST_PAGE_NO';
	
	/**
	 * 出身校一覧を表示 
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
				if (self::checkSessionVar($this->name, self::FIND_KEY)) $this->data = array('vmoAlmaMater' => self::getSessionVar($this->name, self::FIND_KEY));
				
				// ページ番号がパラメタにあれば、その番号を保持
				if (!empty($this->params['named']['page'])) {
					$pageNo = $this->params['named']['page'];
				} else {
					if (self::checkSessionVar($this->name, self::LIST_PAGE)) $pageNo = self::getSessionVar($this->name, self::LIST_PAGE);
				}
			}
		}
		
		// 検索条件があれば検索
		if (!empty($this->data['vmoAlmaMater'])) {
			// 検索条件を生成
			$this->loadModel('VmoAlmaMater');
			$this->VmoAlmaMater->create();
			
			// 値をセットする
			$this->VmoAlmaMater->set($this->data['vmoAlmaMater']);
			
			// バリデーションチェック
			if ($this->VmoAlmaMater->validates()) {
				// バリデーションが通ったので検索条件を取得
				$this->paginate['conditions'] = $this->VmoAlmaMater->getCondition4List();
				
				// 検索条件をセッションに保持
				parent::setSessionVar($this->name, self::FIND_KEY, $this->data['vmoAlmaMater']);
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
		$this->set('title_for_layout', '出身校検索');

		// ページ情報をセッションに保持
		self::setSessionVar($this->name, self::LIST_PAGE, $pageNo);
		
		// ページネータの値をセット
		$this->paginate['page'] = $pageNo;
		$this->paginate['order'] = array('alma_mater_id' => 'asc');

		// 値を取得
		$this->set('recs', $this->paginate());
	}
}
?>
