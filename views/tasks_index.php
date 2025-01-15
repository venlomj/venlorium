<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
</head>
<body>
    <h1>Tasks</h1>

    <?php foreach ($tasks as $task): ?>
        <h2><?= htmlspecialchars($task["name"]) ?></h2>
        <p><?= $task["priority"] ?></p>
        <p><?= $task["is_completed"] ?></p>
    <?php endforeach;?>    
</body>
</html>