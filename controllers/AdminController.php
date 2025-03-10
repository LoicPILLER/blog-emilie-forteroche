<?php 
/**
 * Contrôleur de la partie admin.
 */
 
class AdminController {

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin() : void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    /**
     * Affiche la page de monitoring.
     * @return void
     */
    public function showMonitoring() : void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On récupère le nombre de commentaires par article.
        $nbComments = [];
        $commentManager = new CommentManager();
        foreach ($articles as $article) {
            $nbComments[$article->getId()] = count($commentManager->getAllCommentsByArticleId($article->getId()));
        }

        //On récupère les paramètres de tri dans l'URL
        $sort = $_GET['sort'] ?? 'date';  // Par défaut tri par date
        $order = $_GET['order'] ?? 'desc'; // Par défaut en descendant

        // Tri des articles en fonction du paramètre
        usort($articles, function($a, $b) use ($sort, $order, $nbComments) {
            switch ($sort) {
                case 'title':
                    $valueA = $a->getTitle();
                    $valueB = $b->getTitle();
                    break;
                case 'views':
                    $valueA = $a->getViews();
                    $valueB = $b->getViews();
                    break;
                case 'comments':
                    $valueA = $nbComments[$a->getId()];
                    $valueB = $nbComments[$b->getId()];
                    break;
                case 'date':
                default:
                    $valueA = $a->getDateCreation()->getTimestamp();
                    $valueB = $b->getDateCreation()->getTimestamp();
                    break;
            }

            return ($order === 'asc') ? $valueA <=> $valueB : $valueB <=> $valueA;
        });

        // On affiche la page de monitoring.
        $view = new View("Monitoring");
        $view->render("adminMonitoring", ['articles' => $articles, 'nbComments' => $nbComments, 'sort' => $sort, 'order' => $order]);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected() : void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm() : void 
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser() : void 
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser() : void 
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle() : void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);
       
        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Suppression d'un commentaire.
     * @return void
     * @throws Exception
     */
    public function deleteComment() : void
    {
        $this->checkIfUserIsConnected();

        // Récupération et vérification de l'id du commentaire.
        $id = Utils::request("id", -1);

        // On vérifie que l'id est valide.
        if ($id === -1) {
            throw new Exception("L'id du commentaire est invalide.");
        }

        // On récupère le commentaire.
        $commentManager = new CommentManager();
        $comment = $commentManager->getCommentById($id);

        // On vérifie que le commentaire existe.
        if (!$comment) {
            throw new Exception("Le commentaire demandé n'existe pas.");
        }

        // On supprime le commentaire.
        $result = $commentManager->deleteComment($comment);

        // On vérifie que la suppression a bien fonctionné.
        if (!$result) {
            throw new Exception("Une erreur est survenue lors de la suppression du commentaire.");
        }

        // On redirige vers la page de l'article.
        Utils::redirect("showArticle", ['id' => $comment->getIdArticle()], 'comments-section');
    }
}