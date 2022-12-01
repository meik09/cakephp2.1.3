<?php
/**
 * バリデーションエラー表示
 * フォームで発生したバリデーションエラーをリスト形式で一覧表示する
 * 
 * saveAllとかは、おいおい対応
 * 
 * $this->validationErrors = array(
 *		ModelName => array(
 *			FieldName => array(
 *				0 => message,
 *				1 => message,
 *				…
 *			),
 *			FieldName => array(
 *				0 => message,
 *				1 => message,
 *				…
 *			)
 * );
 * 
 * @author	N.Kuga
 * @create	2012/07/31
 **/

// エラーが存在するか確認する
$cnt = 0;
foreach ($this->Form->validationErrors as $model => $arrayField) {
	// フィールドリストからエラーメッセージリストを取得
	foreach ($arrayField as $field => $arrayMessage) {
		$cnt++;
		break 2;
	}
}

// バリデーションエラー表示
if ($cnt > 0) {
	// モデル単位にエラーのフィールドリストを取得
	echo '<ul type="square">';
	foreach ($this->Form->validationErrors as $model => $arrayField) {
		// フィールドリストからエラーメッセージリストを取得
		foreach ($arrayField as $field => $arrayMessage) {
			if (!is_numeric($field)) {
				// $fieldの値が数値でなければエラーを表示
				echo $this->Form->error($model.'.'.$field, null, array('wrap' => 'li'));
			} else {
				// 数値の場合はもう１階層深く掘り下げる
				foreach ($arrayMessage as $lastField => $lastMessage) {
					$msg = $this->Form->error($model.'.'.$field.'.'.$lastField, null, array('wrap' => '')) . '(' . ($field + 1) . '件目)';
					echo $this->Form->error($model.'.'.$field.'.'.$lastField, $msg, array('wrap' => 'li'));
				}
			}
		}
	}
	echo '</ul>';
}
?>
