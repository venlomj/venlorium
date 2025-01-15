

<h1>Tasks</h1>

<?php foreach ($tasks as $task): ?>

    <h2><?= htmlspecialchars($task["name"]) ?></h2>
    <p><?= htmlspecialchars($task["description"]) ?></p>

<?php endforeach; ?>

</body>
</html>