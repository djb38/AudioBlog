<h1>Admin Users</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Email</th>
        <th>Edit</th>
    </tr>

    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo $user['User']['id']; ?></td>
        <td><?php echo $user['User']['email']; ?></td>
        <td>
            <?php
                if ($user['User']['id'] > 1) {
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $user['User']['id']),
                        array('confirm' => 'Are you sure?')
                    );
                }
            ?>
            <?php
                echo $this->Html->link(
                    'Edit',
                    array('action' => 'edit', $user['User']['id'])
                );
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php unset($user); ?>
</table>

<?php echo $this->Html->link(
    'Add User',
    array('action' => 'add')
); ?>