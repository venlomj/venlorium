<h1>Tasks</h1>

<a href="/tasks/new">New Task</a>

<?php foreach ($tasks as $task): ?>

    <h2>
        <a href="/tasks/<?=$task["id"] ?>/show">
            <?= htmlspecialchars($task["name"]) ?>
        </a>
    </h2>
<?php endforeach; ?>

</body>
</html>