<?php

class PostsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session');

    public function index() {
    	$this->set('posts', $this->Post->find('all'));
    }

    public function view($id = null) {
		if (!$id) {
		    throw new NotFoundException(__('Invalid post'));
		}

		$post = $this->Post->findById($id);
		if (!$post) {
		    throw new NotFoundException(__('Invalid post'));
		}
		$this->set('post', $post);
    }

    public function admin_index() {
		$this->set('posts', $this->Post->find('all'));
    }

    public function admin_add() {
        if ($this->request->is('POST')) {
            $this->Post->create();
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash(__('Your post has been saved.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add your post.'));
        }

        if (!$this->request->data) {
	        $this->set('categories', $this->Post->Category->find('list'));
	    }
    }

    public function admin_edit($id = null) {
	    if (!$id) {
	        throw new NotFoundException(__('Invalid post'));
	    }

	    $post = $this->Post->findById($id);
	    if (!$post) {
	        throw new NotFoundException(__('Invalid post'));
	    }

	    if ($this->request->is(array('POST', 'PUT'))) {
	        $this->Post->id = $id;
	        if ($this->Post->save($this->request->data)) {
	            $this->Session->setFlash(__('Your post has been updated.'));
	            return $this->redirect(array('action' => 'index', 'admin' => true));
	        }
	        $this->Session->setFlash(__('Unable to update your post.'));
	    }

	    if (!$this->request->data) {
	        $this->request->data = $post;
	        $this->set('categories', $this->Post->Category->find('list'));
	    }
	}

	public function admin_delete($id = null) {
		if ($this->request->is('get')) {
	        throw new MethodNotAllowedException();
	    }

	    if ($this->Post->delete($id)) {
	        $this->Session->setFlash(__('The post with id: %s has been deleted.', h($id)));
	        return $this->redirect(array('action' => 'index', 'admin' => true));
	    }

	    $this->Session->setFlash(__('The post with id: %s has NOT been deleted.', h($id)));
	}

}

?>