<?php
// pages/interests.php
global $db; // PDO objekt z db.php

// ------------------------------------------------------------------------
// Zpracování POST požadavků (CRUD)
// ------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $_SESSION['error'] = 'Název zájmu nesmí být prázdný.';
        } else {
            // Kontrola duplicit
            $stmt = $db->prepare('SELECT COUNT(*) FROM interests WHERE name = ?');
            $stmt->execute([$name]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error'] = 'Tento zájem už v databázi existuje.';
            } else {
                $stmt = $db->prepare('INSERT INTO interests (name) VALUES (?)');
                $stmt->execute([$name]);
                $_SESSION['success'] = 'Zájem byl úspěšně přidán.';
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare('DELETE FROM interests WHERE id = ?');
            $stmt->execute([$id]);
            $_SESSION['success'] = 'Zájem byl úspěšně smazán.';
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        
        if ($name === '') {
            $_SESSION['error'] = 'Název zájmu nesmí být prázdný.';
        } elseif ($id > 0) {
            // Kontrola duplicit (nesmí se jmenovat jako jiný zájem mimo aktuálně upravovaný)
            $stmt = $db->prepare('SELECT COUNT(*) FROM interests WHERE name = ? AND id != ?');
            $stmt->execute([$name, $id]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error'] = 'Takový zájem už v databázi existuje.';
            } else {
                $stmt = $db->prepare('UPDATE interests SET name = ? WHERE id = ?');
                $stmt->execute([$name, $id]);
                $_SESSION['success'] = 'Zájem byl úspěšně upraven.';
            }
        }
    }

    // PRG přesměrování
    header('Location: ?page=interests');
    exit;
}

// ------------------------------------------------------------------------
// Získání dat pro zobrazení
// ------------------------------------------------------------------------
$stmt = $db->query('SELECT * FROM interests ORDER BY id DESC');
$interests = $stmt->fetchAll();

// Zobrazení formuláře pro editaci
$editId = (int)($_GET['edit'] ?? 0);
$editItem = null;
if ($editId > 0) {
    $stmt = $db->prepare('SELECT * FROM interests WHERE id = ?');
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}
?>

<h2>Moje Zájmy</h2>

<?php if ($editItem): ?>
    <h3>Upravit zájem</h3>
    <form method="post" action="?page=interests">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($editItem['name']) ?>" required>
        <button type="submit">Uložit úpravy</button>
        <a href="?page=interests" style="margin-left: 10px; display: inline-block; padding: 0.5rem; text-decoration: none; color: #333; border: 1px solid #ccc; border-radius: 4px; background: #eee;">Zrušit</a>
    </form>
<?php else: ?>
    <h3>Přidat nový zájem</h3>
    <form method="post" action="?page=interests">
        <input type="hidden" name="action" value="add">
        <input type="text" name="name" placeholder="Např. Čtení sci-fi" required>
        <button type="submit">Přidat</button>
    </form>
<?php endif; ?>

<h3>Seznam zájmů</h3>
<?php if (count($interests) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Název</th>
                <th style="width: 150px;">Akce</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($interests as $interest): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$interest['id']) ?></td>
                    <td><?= htmlspecialchars($interest['name']) ?></td>
                    <td class="actions">
                        <a href="?page=interests&edit=<?= $interest['id'] ?>" class="edit-btn">Upravit</a>
                        
                        <form method="post" action="?page=interests" style="display:inline; margin:0; padding:0;" onsubmit="return confirm('Opravdu chcete tento zájem smazat?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $interest['id'] ?>">
                            <button type="submit" class="delete-btn" style="padding: 0.35rem 0.6rem; font-size: 0.85rem;">Smazat</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Zatím nebyly přidány žádné zájmy.</p>
<?php endif; ?>
