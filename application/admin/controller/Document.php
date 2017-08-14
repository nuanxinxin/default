<?php

namespace app\admin\controller;

use app\common\model\Document as DocumentModel;
use app\common\model\Setting;
use app\common\model\Question;

class Document extends AdminBase {
	public function _initialize() {
		parent::_initialize ();
		if (LOGIN_TYPE != 'admin') {
			$this->error ( '非法操作' );
		}
	}
	
	// 文档列表
	public function index() {
		$list = DocumentModel::order ( 'id asc' )->paginate ( 100 );
		$this->assign ( 'list', $list );
		return $this->fetch ();
	}
	
	// 编辑文档
	public function edit($id) {
		if (isPost ()) {
			try {
				$this->setData ( DocumentModel::get ( $id ) )->save ();
			} catch ( \Exception $e ) {
				$this->error ( $e->getMessage () );
			}
			$this->success ( '修改成功', cookie ( 'referer' ) );
		} else {
			cookie ( 'referer', $this->request->header ( 'referer' ) );
			$this->assign ( 'data', DocumentModel::get ( $id ) );
			return $this->fetch ( 'form' );
		}
	}
	
	/**
	 * 设置需要保存的数据
	 * 
	 * @param
	 *        	$model
	 * @return mixed
	 */
	private function setData($model) {
		$model->title = $this->request->param ( 'title' );
		$model->pic = $this->request->param ( 'pic' );
		$model->content = $this->request->param ( 'content' );
		return $model;
	}
	public function slide() {
		if (isPost ()) {
			$data = $this->request->except ( '__token__', 'post' );
			cache ( 'slide', $data, [ 
					'path' => CACHE_PATH . 'slide' 
			] );
			$this->success ( '保存成功' );
		} else {
			cache ( [ 
					'path' => CACHE_PATH . 'slide' 
			] );
			$this->assign ( 'data', cache ( 'slide' ) );
			return $this->fetch ();
		}
	}
	public function webConfig() {
		if (isPost ()) {
			$data = $this->request->except ( '__token__', 'post' );
			$model = new Setting ();
			foreach ( $data as $index => $item ) {
				$model->clearCache ( 4 )->where ( 'name', $index )->setField ( 'value', $item );
			}
			$this->success ( '设置成功' );
		} else {
			$this->assign ( 'data', Setting::getConfig ( 4 ) );
			return $this->fetch ( 'web_config' );
		}
	}
	public function question() {
		if (isPost ()) {
			try {
				$id = $this->request->param ( 'id/d' );
				$content = $this->request->param ( 'content/s' );
				if (empty ( $content ))
					abort ( 500, '请输入回复内容' );
				$question = Question::get ( $id );
				$question->answer = $content;
				$question->answer_time = date ( 'Y-m-d H:i:s' );
				$question->save ();
			} catch ( \Exception $e ) {
				return json ( [ 
						'code' => 500,
						'message' => $e->getMessage () 
				] );
			}
			return json ( [ 
					'code' => 200,
					'message' => '回复成功' 
			] );
		} else {
			$list = Question::order ( 'id desc,answer_time asc' )->paginate ( 30 );
			$this->assign ( 'list', $list );
			return $this->fetch ();
		}
	}
	public function delQuestion() {
		try {
			$id = $this->request->param ( 'id/d' );
			$result = Question::get ( $id );
			$delResult = $result->delete ();
		} catch ( \Exception $e ) {
			$this->error ($e->getMessage ());
		}
		$this->success ( '删除成功' );
	}
}