<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class FileBehavior extends ModelBehavior {

	protected $_defaults = array('basePath' => '',
								 'subPath' => '',
								 'allowedTypes' => '*',
								 'disallowedTypes' => array('exe', 'sh', 'php', 'htaccess'),
								 'filenameFormField' => 'filedata',
								 'filenameDatabaseField' => 'filename',
								 'hashFilename' => true);

	protected $_deletedRow = array();

	public function setup(Model $Model, $settings = array()) {
	    $this->settings[$Model->alias] = array_merge($this->_defaults, (array)$settings);

	    if (!$this->settings[$Model->alias]['basePath']) {
	    	$this->settings[$Model->alias]['basePath'] = WWW_ROOT . "files" . DS . "uploads";
	    }

	    if (!$this->settings[$Model->alias]['subPath']) {
	    	$this->settings[$Model->alias]['subPath'] = strtolower($Model->alias);
	    }
	}

	public function beforeSave(Model $Model, $options = array()) {
		extract($this->settings[$Model->alias]);
		
		$fileData = $Model->data[$Model->alias][$filenameFormField];

		if ($Model->id && $fileData['error'] == 4) {
			return true;
		}

		if ($fileData['error'] > 0) {
			return false;
		}

		$newFilename = $this->_sanitizeFilename($Model, $fileData['name']);
		if (!$newFilename) {
			return false;
		}

		$Model->data[$Model->alias][$filenameDatabaseField] = $newFilename;

		return $this->_uploadFile($Model, $fileData, $newFilename);
	}

	public function afterFind(Model $Model, $results, $primary = false) {
		extract($this->settings[$Model->alias]);

		if (empty($results)) {
			return $results;
		}

		foreach ($results AS &$row) {
			$file = new File($this->_getFilePath($Model, $row[$Model->alias][$filenameDatabaseField]));
			$row[$Model->alias]['fileData'] = $file->info();
		}

		return $results;
	}

	public function beforeDelete(Model $Model, $cascade = true) {
		extract($this->settings[$Model->alias]);
		$data = $Model->find('first', array(
			'conditions' => array($Model->escapeField($Model->primaryKey) => $Model->id)
		));
		if ($data) {
			$this->_deletedRow[$Model->alias] = current($data);
		}
		return true;
	}

	public function afterDelete(Model $Model) {
		extract($this->settings[$Model->alias]);
		$data = $this->_deletedRow[$Model->alias];
		$this->_deletedRow[$Model->alias] = null;
		return $this->_deleteFile($Model, $data[$filenameDatabaseField]);
	}

	protected function _hashFilename($filename) {
		return uniqid();
	}

	protected function _checkFiletype(Model $Model, $tla) {
		extract($this->settings[$Model->alias]);
		if ($tla == '' || (is_array($disallowedTypes) && count($disallowedTypes) > 0 && in_array($tla, $disallowedTypes)) || $disallowedTypes == $tla) {
			return false;
		}
		if ((is_array($allowedTypes) && count($allowedTypes) > 0 && in_array($tla, $allowedTypes)) || $allowedTypes == "*" || $allowedTypes == $tla) {
			return true;
		}
		return false;
	}

	protected function _sanitizeFilename(Model $Model, $rawFilename) {
		extract($this->settings[$Model->alias]);

		$filenameParts = explode('.', $rawFilename);
		$filenameName = trim(current($filenameParts));
		$filenameTla = trim(end($filenameParts));
		if (!$filenameName || !$this->_checkFileType($Model, $filenameTla)) {
			return false;
		}

		if ($hashFilename) {
			$filenameName = $this->_hashFilename($filenameName);
		}
		return $filenameName . '.' . $filenameTla;
	}

	protected function _buildPath($Model) {
		extract($this->settings[$Model->alias]);
		$filepath = $basePath . DS . $subPath;
		$folder = new Folder($filepath, true);
		return $filepath;
	}

	protected function _getFilePath(Model $Model, $filename) {
		return $this->_buildPath($Model) . DS . $filename;
	}

	protected function _uploadFile(Model $Model, $file, $newFilename) {
		$tmpFile = new File($file['tmp_name'], false);
		return $tmpFile->copy($this->_getFilePath($Model, $newFilename), true);
	}

	protected function _deleteFile(Model $Model, $filename) {
		$file = new File($this->_getFilePath($Model, $filename), false);
		return $file->delete();
	}
}

?>