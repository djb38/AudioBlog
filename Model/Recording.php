<?php

class Recording extends AppModel {
	public $actsAs = array('File' => array(
								'allowedTypes' => array(
									'wav', 'mp3', 'wma'
								)
							)
						);

	public $validate = array(
        'title' => array(
            'rule' => 'notEmpty'
        )
    );
}

?>