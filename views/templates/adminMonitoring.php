<h2>Monitoring</h2>


<table class="monitoring-table">
    <thead>
    <tr>
        <th><a href="<?= Utils::actionUrl('monitoring', ['sort' => 'title', 'order' => ($sort === 'title' && $order === 'asc') ? 'desc' : 'asc']) ?>">Titre</a></th>
        <th><a href="<?= Utils::actionUrl('monitoring', ['sort' => 'views', 'order' => ($sort === 'views' && $order === 'desc') ? 'asc' : 'desc']) ?>">Vues</a></th>
        <th><a href="<?= Utils::actionUrl('monitoring', ['sort' => 'comments', 'order' => ($sort === 'comments' && $order === 'desc') ? 'asc' : 'desc']) ?>">Commentaires</a></th>
        <th><a href="<?= Utils::actionUrl('monitoring', ['sort' => 'date', 'order' => ($sort === 'date' && $order === 'asc') ? 'desc' : 'asc']) ?>">Date de création</a></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($articles as $article) : ?>
    <tr>
        <td><?= htmlspecialchars($article->getTitle()) ?></td>
        <td><?= $article->getViews() ?></td>
        <td><?= $nbComments[$article->getId()] ?></td>
        <td><?= $article->getDateCreation()->format('d/m/Y') ?></td>
    </tr>
        <?php endforeach; ?>
    </tbody>
</table>