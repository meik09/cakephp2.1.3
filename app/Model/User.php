<?php
/*
 * ログインユーザのモデル
 * 
 * @author Naoya.Kuga
 */
class User extends AppModel {
	public $name = 'User';
	public $validate = Array(
		'personnel_no' => Array(
			'required' => Array(
				'rule' => Array('notEmpty'),
				'message' => '職員番号を入力してください。',
				'last' => true, // 続行しない
			),
			'custom' => array(
				'rule' => array('custom', '/^[0-9]{10}$/'),
				'message' => '職員番号は数値10桁で入力してください。',
				'last' => true, // 続行しない
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => '職員番号は登録済みです。他の値を入力してください。',
				'last' => true, // 続行しない
			)
		),
		'login_id' => Array(
			'required' => Array(
				'rule' => Array('notEmpty'),
				'message' => 'ログインIDを入力してください。',
				'last' => true, // 続行しない
			),
			'custom' => array(
				'rule' => array('custom', '/^[0-9A-Za-z-]+$/'),
				'message' => 'ログインIDは半角英数字と半角-(ハイフン)で入力してください。',
				'last' => true, // 続行しない
			),
            'maxLength' => array(
                'rule' => array('maxLength', 10),
				'message' => 'ログインIDは10文字以内で入力してください。',
				'last' => true, // 続行しない
            ),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'ログインIDは登録済みです。他の値を入力してください。',
				'last' => true, // 続行しない
			)
		),
		'password' => Array(
			'required' => Array(
				'rule' => Array('notEmpty'),
				'message' => 'パスワードを入力してください。',
				'last' => true, // 続行しない
			)
		)
    );
}
?>
