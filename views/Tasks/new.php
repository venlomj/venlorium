<h1>New Task</h1>

<form method="post" action="/tasks/create">
    <label for="name">Name</label>
    <input type="text" name="name" id="name">
    
    <?php if (isset($errors["name"])): ?>
        <p><?= $errors["name"] ?></p>
    <?php endif; ?>

    <label for="priority">Priority</label>
    <input type="text" name="priority" id="priority">
    
    <label for="description">Description</label>
    <textarea type="text" name="description" id="description"></textarea>

    <label for="is_completed">Completed</label>
    <input type="checkbox" name="is_completed" id="is_completed">

    <button>Save</button>
</form>