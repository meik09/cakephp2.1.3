<?php
/*
 * 在籍者の全情報のモデル
 * 
 * @author Naoya.Kuga
 */
class StudentAllInfo extends AppModel {
	public $name = 'StudentAllInfo';
	
	// DISTINCTを使用したpaginateを使用する場合に、fieldsで使う配列をセットしておく
	// （paginateCount()も参照）
	public $distinctPaginateFields = null;
	
	// DISTINCTを使用したpaginateを使用するとページ数が狂う（cakeの仕様）
	// このためpaginateCountを定義して正常なレコード数を取得している
	// ※注意点
	//　　このモデルでページネータを使うと、ページカウントに必ずこのメソッドが使われる。
	//　　追加プロパティ「$distinctPaginateFields」の値の有無で判定を行い、通常の動作もサポートしている。
	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		// $distinctPaginateFieldsに値が入っていなければ通常の動作をする
		if ($this->distinctPaginateFields === null) {
			
			// paginateCount()はオーバーライドではないので下記コードをPaginatorComponent.phpから拝借
			// PaginatorComponent.phpは｢/cake/lib/Cake/Controller/Component/PaginatorComponent.php｣にある
			$parameters = compact('conditions');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$count = $object->find('count', array_merge($parameters, $extra));
		} else {

			// DISTINCT入りの$distinctPaginateFieldsを使用してページ数をカウントする
			$parameters = array('fields' => $this->distinctPaginateFields, 'conditions' => $conditions, 'recursive' => $recursive);
			$results = $this->find('all', array_merge($parameters, $extra));
			$count = count($results);
		}
		return $count;
	}
}
?>
