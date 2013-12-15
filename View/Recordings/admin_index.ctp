<h1>Recordings</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Data</th>
        <th>Edit</th>
    </tr>

    <?php foreach ($recordings as $recording): ?>
    <tr>
        <td><?php echo $recording['Recording']['id']; ?></td>
        <td><?php echo $recording['Recording']['title']; ?></td>
        <td>
            <?php pr($recording['Recording']['fileData']); ?>
        </td>
        <td>
            <?php
                echo $this->Form->postLink(
                    'Delete',
                    array('action' => 'delete', $recording['Recording']['id']),
                    array('confirm' => 'Are you sure?')
                );
            ?>
            <?php
                echo $this->Html->link(
                    'Edit',
                    array('action' => 'edit', $recording['Recording']['id'])
                );
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php unset($recording); ?>
</table>

<?php echo $this->Html->link(
    'Add Recording',
    array('action' => 'add')
); ?>