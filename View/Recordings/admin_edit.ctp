<h1>Edit Recording</h1>
<?php
echo $this->Form->create('Recording', array('type' => 'file'));
echo $this->Form->input('title');
echo $this->Form->input('filedata', array('type' => 'file'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Upload Recording');
?>