<?php
App::import('Helper','Paginator');
/**
 * ページネーター拡張クラス
 * @package app.views.helpers
 * @author	N.Kuga
 * @create	2012/07/30
 */
class PaginatorExHelper extends PaginatorHelper {

	/**
	 * ページカウンターを表示する(件数)
	 */
	function pagingCounters() {
		echo parent::counter(array('format' => '全%count%件中 %start%件 - %end%件'));
	}
	
	/**
	 * ページリンクを一括で表示する
	 * @param string $modulus ナンバーページリンクの表示数
	 */
	function pagingLinks($modulus = 4) {
		$model = parent::params();
		if($model) {
			// ページ数が1ページ以下なら非表示
			if($model['pageCount'] <= 1) return;
			
			echo parent::first('最初へ');
			echo '&nbsp;';
			echo parent::prev('前へ');
			echo '&nbsp;';
			echo parent::numbers(array('modulus' => $modulus, 'separator' => '&nbsp;'));
			echo '&nbsp;';
			echo parent::next('次へ');
			echo '&nbsp;';
			echo parent::last('最後へ');
		}
	}
}
?>
