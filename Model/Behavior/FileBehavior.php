<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class FileBehavior extends ModelBehavior {

	protected $_defaults = array(
		'basePath' => '',
		'subPath' => '',
		'webPath' => '',
		'allowedTypes' => '*',
		'disallowedTypes' => array('exe', 'sh', 'php', 'htaccess'),
		'filenameFormField' => 'filedata',
		'filenameDatabaseField' => 'filename',
		'hashFilename' => true
	);

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

	public function beforeValidate(Model $Model, $options = array()) {
		extract($this->settings[$Model->alias]);

		$Model->validator()->add($filenameFormField, array(
		    'validName' => array(
		        'rule' => 'validateFilename',
		        'message' => 'Bad filename.'
		    ),
		    'validType' => array(
		        'rule' => 'validateFileType',
		        'message' => 'Invalid file type.'
		    ),
		    'uploadSucceeded' => array(
		    	'rule' => 'validateFileUpload',
		    	'message' => 'Upload failed.'
		    )
		));
	}

	public function validateFilename($data) {
        $ModelAlias = array_keys($data->data)[0];
		extract($this->settings[$ModelAlias]);

		$uploadError = $data->data[$ModelAlias][$filenameFormField]['error'];
		if (isset($data->id) && $data->id && $uploadError == UPLOAD_ERR_NO_FILE) {
			return true;
		}

		$rawFilename = $data->data[$ModelAlias][$filenameFormField]['name'];
		$filename = $this->_extractFilenameParts($rawFilename)['name'];
		return ($filename != '');
	}

	public function validateFileType($data) {
		$ModelAlias = array_keys($data->data)[0];
		extract($this->settings[$ModelAlias]);

		$uploadError = $data->data[$ModelAlias][$filenameFormField]['error'];
		if (isset($data->id) && $data->id && $uploadError == UPLOAD_ERR_NO_FILE) {
			return true;
		}

		$rawFilename = $data->data[$ModelAlias][$filenameFormField]['name'];
		$tla = $this->_extractFilenameParts($rawFilename)['tla'];

		if ($tla == '' || (is_array($disallowedTypes) && count($disallowedTypes) > 0 && in_array($tla, $disallowedTypes)) || $disallowedTypes == $tla) {
			return 'Filetype disallowed. You may not upload the following file types: '.implode(', ', $disallowedTypes);
		}
		if ((is_array($allowedTypes) && count($allowedTypes) > 0 && in_array($tla, $allowedTypes)) || $allowedTypes == "*" || $allowedTypes == $tla) {
			return true;
		}
		return 'Filetype not allowed. You may only upload the following file types: '.implode(', ', $allowedTypes);
	}

	public function validateFileUpload($data) {
		$ModelAlias = array_keys($data->data)[0];
		extract($this->settings[$ModelAlias]);

		$uploadError = $data->data[$ModelAlias][$filenameFormField]['error'];
		if (isset($data->id) && $data->id && $uploadError == UPLOAD_ERR_NO_FILE) {
			return true;
		}

		if ($uploadError == UPLOAD_ERR_OK) {
			return true;
		}

		return 'Upload failed with error code '.$uploadError.'. Please consult the PHP manual for details.';
	}

	public function beforeSave(Model $Model, $options = array()) {
		extract($this->settings[$Model->alias]);

		$fileData = $Model->data[$Model->alias][$filenameFormField];
		if ($fileData['error'] == UPLOAD_ERR_OK) {
			$newFilename = $this->_hashFilename($Model, $fileData['name']);
			$Model->data[$Model->alias][$filenameDatabaseField] = $newFilename;
			return $this->_uploadFile($Model, $fileData, $newFilename);
		}
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

	protected function _extractFilenameParts($filename) {
		$filenameParts = explode('.', $filename);
		return array('name' => trim(current($filenameParts)), 'tla' => trim(end($filenameParts)));
	}

	protected function _hashFilename(Model $Model, $rawFilename) {
		extract($this->settings[$Model->alias]);

		$filenameParts = $this->_extractFilenameParts($rawFilename);
		if ($hashFilename) {
			$filenameParts['name'] = uniqid();
		}
		return $filenameParts['name'] . '.' . $filenameParts['tla'];
	}

	protected function _getFilePath(Model $Model, $filename) {
		extract($this->settings[$Model->alias]);

		$filepath = $basePath . DS . $subPath;
		$folder = new Folder($filepath, true);

		return $filepath . DS . $filename;
	}

	protected function _uploadFile(Model $Model, $fileData, $newFilename) {
		$tmpFile = new File($fileData['tmp_name'], false);
		return $tmpFile->copy($this->_getFilePath($Model, $newFilename), true);
	}

	protected function _deleteFile(Model $Model, $filename) {
		$file = new File($this->_getFilePath($Model, $filename), false);
		return $file->delete();
	}
}

?>