<?php
App::uses('FormAuthenticate', 'Controller/Component/Auth');

/*
 * 認証時にパスワードがハッシュ化されるのを回避する
 * 
 * @author Naoya.Kuga
 */
class CustomFormAuthenticate extends FormAuthenticate {
	/**
	 * パスワードをそのまま返す
	 * @param string $password パスワード
	 * @return string パスワード(何もしない) 
	 */
	protected function _password($password){
		return $password;
	}
}
?>
