<?php

class Post extends AppModel {
	public $hasAndBelongsToMany = array(
    	'Category' => array(
            'className' => 'Category',
            'joinTable' => 'categories_posts',
            'foreignKey' => 'post_id',
            'associationForeignKey' => 'category_id',
            'unique' => true
        )
    );

	public $validate = array(
        'title' => array(
            'rule' => 'notEmpty'
        ),
        'content' => array(
            'rule' => 'notEmpty'
        )
    );
}

?>