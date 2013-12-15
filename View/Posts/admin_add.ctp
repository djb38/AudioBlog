<h1>Add Post</h1>
<?php
echo $this->Form->create();
echo $this->Form->input('title');
echo $this->Form->input('Category');
echo $this->Form->input('content', array('rows' => '3'));
echo $this->Form->end('Save Post');
?>