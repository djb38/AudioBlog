<?php

class RecordingsController extends AppController {
	public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session');

    public function admin_index() {
    	$this->set('recordings', $this->Recording->find('all'));
    }

    public function admin_add() {
        if ($this->request->is('POST')) {
            $this->Recording->create();
            if ($this->Recording->save($this->request->data)) {
                $this->Session->setFlash(__('Your recording has been uploaded.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to upload your recording.'));
        }
    }

    public function admin_edit($id = null) {
	    if (!$id) {
	        throw new NotFoundException(__('Invalid recording'));
	    }

	    $recording = $this->Recording->findById($id);
	    if (!$recording) {
	        throw new NotFoundException(__('Invalid recording'));
	    }

	    if ($this->request->is(array('POST', 'PUT'))) {
	        $this->Recording->id = $id;
	        if ($this->Recording->save($this->request->data)) {
	            $this->Session->setFlash(__('Your recording has been updated.'));
	            return $this->redirect(array('action' => 'index', 'admin' => true));
	        }
	        $this->Session->setFlash(__('Unable to update your recording.'));
	    }

	    if (!$this->request->data) {
	        $this->request->data = $recording;
	    	$this->set('recording', $recording);
	    }
	}

	public function admin_delete($id = null) {
		if ($this->request->is('get')) {
	        throw new MethodNotAllowedException();
	    }

	    if ($this->Recording->delete($id)) {
	        $this->Session->setFlash(__('The recording with id: %s has been deleted.', h($id)));
	        return $this->redirect(array('action' => 'index', 'admin' => true));
	    }

	    $this->Session->setFlash(__('The recording with id: %s has NOT been deleted.', h($id)));
	}
}

?>