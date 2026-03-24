<?php
// pages/skills.php
$skills = [
    "PHP",
    "JavaScript",
    "HTML & CSS",
    "SQL (SQLite, MySQL)",
    "Git & GitHub",
    "Docker"
];
?>
<h2>Dovednosti</h2>
<ul>
    <?php foreach ($skills as $skill): ?>
        <li><?= htmlspecialchars($skill) ?></li>
    <?php endforeach; ?>
</ul>
