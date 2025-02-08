<h2>Monitoring</h2>

<div class="adminArticle">
    <?php foreach ($articles as $article) : ?>
        <div class="articleLine">
            <div class="title"><?= $article->getTitle() ?></div>
            <div><?= $article->getViews() ?></div>
            <div><?= $nbComments[$article->getId()] ?></div>
            <div><?= $article->getDateCreation()->format('d/m/Y') ?></div>
        </div>
    <?php endforeach; ?>
</div>