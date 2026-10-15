
<?php
$keyword = trim($_GET['q'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Search</h1>

    <form action="search.php" method="get">
        <label for="q">Search keyword</label>

        <input
            type="text"
            id="q"
            name="q"
            value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
            placeholder="Enter a keyword"
        >

        <button type="submit">Search</button>
    </form>

    <?php if ($keyword !== ''): ?>
        <h2>
            Search result for:
            <?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>
        </h2>
    <?php endif; ?>
</div>

</body>
</html>
