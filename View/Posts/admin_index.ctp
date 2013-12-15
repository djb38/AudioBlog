<h1>Blog posts</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Categories</th>
        <th>Edit</th>
        <th>Last Modified</th>
    </tr>

    <?php foreach ($posts as $post): ?>
    <tr>
        <td><?php echo $post['Post']['id']; ?></td>
        <td>
            <?php echo $this->Html->link($post['Post']['title'], array('action' => 'view', $post['Post']['id'], 'admin' => false)); ?>
        </td>
        <td>
            <?php
                foreach ($post['Category'] AS &$category) {
                    $category = $this->Html->link($category['title'], array('controller' => 'categories', 'action' => 'view', $category['id'])); 
                }
                echo implode(", ",$post['Category']);
            ?>
        </td>
        <td>
            <?php
                echo $this->Form->postLink(
                    'Delete',
                    array('action' => 'delete', $post['Post']['id']),
                    array('confirm' => 'Are you sure?')
                );
            ?>
            <?php
                echo $this->Html->link(
                    'Edit',
                    array('action' => 'edit', $post['Post']['id'])
                );
            ?>
        </td>
        <td><?php echo $post['Post']['modified']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($post); ?>
</table>

<?php echo $this->Html->link(
    'Add Post',
    array('action' => 'add')
); ?>