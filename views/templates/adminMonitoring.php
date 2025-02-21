<h2>Monitoring</h2>


<table class="monitoring-table">
    <thead>
    <tr>
        <th>
            <a class="<?= ($sort === 'title') ? 'active' : '' ?>"
               href="<?= Utils::actionUrl('monitoring', ['sort' => 'title', 'order' => ($sort === 'title' && $order === 'asc') ? 'desc' : 'asc']) ?>">
                Titre
                <img src="<?= Utils::getSortIcon($sort, $order, 'title', 'string') ?>" alt="Trier">
            </a>
        </th>
        <th>
            <a class="<?= ($sort === 'views') ? 'active' : '' ?>"
               href="<?= Utils::actionUrl('monitoring', ['sort' => 'views', 'order' => ($sort === 'views' && $order === 'desc') ? 'asc' : 'desc']) ?>">
                Vues
                <img src="<?= Utils::getSortIcon($sort, $order, 'views', 'number') ?>" alt="Trier">
            </a>
        </th>
        <th>
            <a class="<?= ($sort === 'comments') ? 'active' : '' ?>"
               href="<?= Utils::actionUrl('monitoring', ['sort' => 'comments', 'order' => ($sort === 'comments' && $order === 'desc') ? 'asc' : 'desc']) ?>">Commentaires
                <img src="<?= Utils::getSortIcon($sort, $order, 'comments', 'number') ?>" alt="Trier">
            </a>
        </th>
        <th>
            <a class="<?= ($sort === 'date') ? 'active' : '' ?>"
               href="<?= Utils::actionUrl('monitoring', ['sort' => 'date', 'order' => ($sort === 'date' && $order === 'asc') ? 'desc' : 'asc']) ?>">
                Date de création
                <img src="<?= Utils::getSortIcon($sort, $order, 'date', 'date') ?>" alt="Trier">
            </a>
        </th>
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