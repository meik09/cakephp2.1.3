<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array('Session', 'DebugKit.Toolbar', 'Auth'  => Array(
			'loginRedirect'  => Array('controller' => 'students', 'action' => 'index'),
			'logoutRedirect' => Array('controller' => 'users',    'action' => 'login'),
			'loginAction'    => Array('controller' => 'users',    'action' => 'login'),
			'authenticate'   => Array('CustomForm' => Array('fields' => Array('username' => 'login_id'))),
			'authError'      => 'システムにログインされていません'
		));
	public $helpers = array('Session', 'Html', 'Form');
	public $useDebugKit = true;
	
	public $paginate = array('page'=>1, 'limit'=>20);
	
	var $useHTTPS = true;		// HTTPSを使用しないコントローラではfalseにする
	
	/**
	 * コンストラクタ
	 * @param CakeRequest  $request  リクエストオブジェクト
	 * @param CakeResponce $response レスポンスオブジェクト
	 */
	public function __construct($request, $response) {
		parent::__construct($request, $response);
		
		// Viewをキャッシュさせない
		$this->disableCache(); 

		// 基本レイアウトを指定
		$this->layout = 'gSaints';
	}
	
	/**
	 * コントローラが読み込まれる前処理を行う 
	 */
	public function beforeFilter() {
		// 全体をHTTPSにする
		// .htaccessを利用してリダイレクトしても良かったが、プログラムで処理するようにした
		// 気に入らなかったら変更してください
		if (USE_HTTPS && $this->useHTTPS) {
			if(env('HTTPS') === 'on' || env('HTTPS') === true) {
				// No Action
            } else {
                $HTTPS = SERVER_HTTPS . $this->here;
                $this->redirect($HTTPS);
            }
		}

		parent::beforeFilter();
	}
	
	/**
	 * セッションに値を保持する
	 * @param string $controllerName コントローラ名
	 * @param string $key 変数名
	 * @param any $value 変数の値
	 */
	protected function setSessionVar($controllerName, $key, $value) {
		$this->Session->write(SV_ROOT . '.' . $controllerName . '.' . $key, $value);
	}

	/**
	 * セッションから値を取得する
	 * @param string $controllerName コントローラ名
	 * @param string $key 変数名
	 * @return any 変数の値
	 */
	protected function getSessionVar($controllerName, $key) {
		return $this->Session->read(SV_ROOT . '.' . $controllerName . '.' . $key);
	}

	/**
	 * セッションに変数が保持されているかをチェックする
	 * @param string $controllerName コントローラ名
	 * @param string $key 変数名
	 * @return boolean true:保持されている false:保持されていない
	 */
	protected function checkSessionVar($controllerName, $key = null) {
		if($key != null) {
			return $this->Session->check(SV_ROOT . '.' . $controllerName . '.' . $key);
		} else {
			return $this->Session->check(SV_ROOT . '.' . $controllerName);
		}
	}

	/**
	 * セッションに保持されている変数を削除する
	 * @param string $controllerName コントローラ名
	 * @param string $key 変数名
	 * @return true:削除に成功 false:削除に失敗
	 */
	protected function deleteSessionVar($controllerName, $key = null) {
		if($key != null) {
			return $this->Session->delete(SV_ROOT . '.' . $controllerName . '.' . $key);
		} else {
			return $this->Session->delete(SV_ROOT . '.' . $controllerName);
		}
	}
	
	/**
	 * 処理結果メッセージを設定する
	 * @param string $msg 表示メッセージ
	 * @param string $type ログレベル（LOG_INFO/LOG_WARNING/LOG_ERR）
	 * @param string $info 追加情報
	 */
	protected function setMessage($msg, $type = LOG_INFO, $info = null) {
		// ログレベルにより処理をレイアウトを分ける
		if ($type == LOG_WARNING) {
			$this->Session->setFlash($msg, 'default', array('class' => 'warning'));
		} else if ($type == LOG_ERR) {
			$this->Session->setFlash($msg, 'default', array('class' => 'error'));
		} else {
			$this->Session->setFlash($msg, 'default', array('class' => 'info'));
		}
		
		// ログファイルへの出力
		if ($info != null) {
			$this->log(array($msg, $info), $type);
		} else {
			$this->log($msg, $type);
		}
	}
	
	/**
	 * 押下したボタンを確認する
	 * @param	array $param 確認したいパラメータ
	 * @return	boolean	true / false
	 */
	protected function isClick($param) {
		if (!empty($this->request->data[$param . '_x'])) {
			return true;
		} else if (isset($this->request->data[$param . '_x'])) {
			return true;
		} else if (!empty($this->request->query[$param . '_x'])) {
			return true;
		} else if (isset($this->request->query[$param . '_x'])) {
			return true;
		} else {
			return false;
		}
	}
}
