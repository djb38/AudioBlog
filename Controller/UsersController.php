<?php

class UsersController extends AppController {
	public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session');

    public function login() {
    	if ($this->request->is('post')) {
	        if ($this->Auth->login()) {
	            return $this->redirect($this->Auth->redirect());
	        }
	        $this->Session->setFlash(__('Invalid username or password, try again'));
	    }
	}

	public function logout() {
		$this->Session->setFlash(__('You have successfully logged out'));
		return $this->redirect($this->Auth->logout());
	}

	public function admin_index() {
    	$this->set('users', $this->User->find('all'));
    }

    public function admin_add() {
        if ($this->request->is('POST')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('New user created.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add user.'));
        }
    }

    public function admin_edit($id = null) {
	    if (!$id) {
	        throw new NotFoundException(__('Invalid user'));
	    }

	    $user = $this->User->findById($id);
	    if (!$user) {
	        throw new NotFoundException(__('Invalid user'));
	    }

	    if ($this->request->is(array('POST', 'PUT'))) {
	        $this->User->id = $id;
	        if ($this->User->save($this->request->data)) {
	            $this->Session->setFlash(__('The user has been updated.'));
	            return $this->redirect(array('action' => 'index', 'admin' => true));
	        }
	        $this->Session->setFlash(__('Unable to update the user.'));
	    }

	    if (!$this->request->data) {
	        $this->request->data = $user;
	    }
	}

	public function admin_delete($id = null) {
		if ($this->request->is('get')) {
	        throw new MethodNotAllowedException();
	    }

	    if ($this->User->delete($id)) {
	        $this->Session->setFlash(__('The user with id: %s has been deleted.', h($id)));
	        return $this->redirect(array('action' => 'index'));
	    }

	    $this->Session->setFlash(__('The user with id: %s has NOT been deleted.', h($id)));
	}

}

?>