<h1><?php echo h($post['Post']['title']); ?></h1>

<h2>
	Categories:
	<?php
		foreach ($post['Category'] AS &$category) {
            $category = $this->Html->tag('span',$category['title']); 
        }
        echo implode(", ",$post['Category']);
    ?>
</h2>

<p><small>Last Edited: <?php echo $post['Post']['modified']; ?></small></p>

<p><?php echo h($post['Post']['content']); ?></p>