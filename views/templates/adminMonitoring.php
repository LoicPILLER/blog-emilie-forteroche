<h2>Monitoring</h2>


<table class="monitoring-table">
    <thead>
    <tr>
        <th>Titre</th>
        <th>Vues</th>
        <th>Commentaires</th>
        <th>Date de création</th>
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