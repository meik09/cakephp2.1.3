<?php
/*
 * ユーザの管理を行うコントローラ
 * ログイン・ログアウト機能も備える
 *
 * @author Naoya.Kuga
 */
class UsersController extends AppController {
	public $name = 'Users';
	public $uses = array('User');
	
	public $helpers = array('PaginatorEx');
	
	// 定数宣言
	const FIND_KEY = 'USER_FIND_CONDITION';
	const LIST_PAGE = 'USER_LIST_PAGE_NO';

	/**
	 * 前処理 
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login');
    }
	
	/**
	 *ユーザ一覧を表示 
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
				if (self::checkSessionVar($this->name, self::FIND_KEY)) $this->data = array('vmoUser' => self::getSessionVar($this->name, self::FIND_KEY));
				
				// ページ番号がパラメタにあれば、その番号を保持
				if (!empty($this->params['named']['page'])) {
					$pageNo = $this->params['named']['page'];
				} else {
					if (self::checkSessionVar($this->name, self::LIST_PAGE)) $pageNo = self::getSessionVar($this->name, self::LIST_PAGE);
				}
			}
		}
		
		// 検索条件があれば検索
		if (!empty($this->data['vmoUser'])) {
			// 検索条件を生成
			$this->loadModel('VmoUser');
			$this->VmoUser->create();
			
			// 値をセットする
			$this->VmoUser->set($this->data['vmoUser']);
			
			// バリデーションチェック
			if ($this->VmoUser->validates()) {
				// バリデーションが通ったので検索条件を取得
				$this->paginate['conditions'] = $this->VmoUser->getCondition4List();
				
				// 検索条件をセッションに保持
				parent::setSessionVar($this->name, self::FIND_KEY, $this->data['vmoUser']);
			} else {
				// バリデーションが通らなかったのでメッセージ表示
				parent::setMessage('検索条件に誤りがあります。条件を修正してやり直してください。', LOG_INFO);
			}
		}
		
		// タイトルをセット
		$this->set('title_for_layout', 'ユーザ一覧');

		// ページ情報をセッションに保持
		self::setSessionVar($this->name, self::LIST_PAGE, $pageNo);
		
		// ページネータの値をセット
		$this->paginate['page'] = $pageNo;
		$this->paginate['order'] = array('personnel_no' => 'asc');

		// 値を取得
		$this->set('recs', $this->paginate());
	}

	/**
	 * ユーザを新規登録する 
	 */
	public function add() {
		// 値が取得できれば登録処理
		if (!empty($this->data)) {
			// 登録処理
			if ($this->User->save($this->data['User'])) {
				parent::setMessage('ユーザを登録しました', LOG_INFO);
				$this->redirect(array('action' => 'index'));
			} else {
				parent::setMessage('ユーザの登録に失敗しました', LOG_WARNING);
			}
		}
		
		// タイトルをセット
		$this->set('title_for_layout', 'ユーザ新規登録');
	}
	
	/**
	 * ユーザを更新する 
	 */
	public function edit($id) {
		// IDがなければエラーとする
		if (empty($id)) {
			parent::setMessage('値が不正です', LOG_WARNING);
			$this->redirect(array('action' => 'index'));
		}
		
		if (!empty($this->data)) {
			if (parent::isClick('update')) {
				// 更新処理
				if ($this->User->save($this->data['User'])) {
					parent::setMessage('ユーザを更新しました', LOG_INFO);
					$this->redirect(array('action' => 'index'));
				} else {
					parent::setMessage('ユーザの更新に失敗しました', LOG_WARNING);
				}
			} else if (parent::isClick('delete')) {
				// 削除処理
				if ($this->User->delete($this->data['User']['id'])) {
					parent::setMessage('ユーザを削除しました', LOG_INFO);
					$this->redirect(array('action' => 'index'));
				} else {
					parent::setMessage('ユーザの削除に失敗しました', LOG_WARNING);
				}
			}
		} else {
			// 値がない場合にはレコードを取得
			$this->data = $this->User->findById($id);
		}
		
		// タイトルをセット
		$this->set('title_for_layout', 'ユーザ更新');
	}
	
	/**
	 * システムにユーザをログインさせる
	 * ログインが出来た場合には、在学生一覧に遷移する
	 */
    public function login() {
		// 画面表示時にレイアウトを使用しないように変更
		$this->autoLayout = false;
		
		// タイトルをセット
		$this->set('title_for_layout', 'ログイン');
		
		// ログインチェック
		if($this->request->is('post')) {
			if($this->Auth->login()) {
				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash('ログインIDまたはパスワードが違っています', 'default', array(), 'auth');
			}
		}
	}

	/**
	 * システムからユーザをログアウトさせる
	 * ログアウトできた場合には、ログイン画面に遷移する 
	 */
	public function logout($id = null) {
		// セッションの破棄
		$this->Session->destroy();
		
		// ログイン画面に遷移
		$this->redirect($this->Auth->logout());
	}
}
?>
