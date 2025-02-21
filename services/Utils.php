<?php

/**
 * Classe utilitaire : cette classe ne contient que des méthodes statiques qui peuvent être appelées
 * directement sans avoir besoin d'instancier un objet Utils.
 * Exemple : Utils::redirect('home');
 */
class Utils
{
    /**
     * Convertit une date vers le format de type "Samedi 15 juillet 2023" en francais.
     * @param DateTime $date : la date à convertir.
     * @return string : la date convertie.
     */
    public static function convertDateToFrenchFormat(DateTime $date): string
    {
        // Attention, s'il y a un soucis lié à IntlDateFormatter c'est qu'il faut
        // activer l'extention intl_date_formater (ou intl) au niveau du serveur apache. 
        // Ca peut se faire depuis php.ini ou parfois directement depuis votre utilitaire (wamp/mamp/xamp)
        $dateFormatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::FULL);
        $dateFormatter->setPattern('EEEE d MMMM Y');
        return $dateFormatter->format($date);
    }

    /**
     * Retourne l'URL d'une action.
     * @param string $action : l'action que l'on veut faire (correspond aux actions dans le routeur).
     * @param array $params : Facultatif, les paramètres de l'action sous la forme ['param1' => 'valeur1', 'param2' => 'valeur2']
     * @return string : l'URL de l'action.
     */
    public static function actionUrl(string $action, array $params = []): string
    {
        $url = "index.php?action=$action";
        foreach ($params as $paramName => $paramValue) {
            $url .= "&$paramName=$paramValue";
        }
        return $url;
    }

    /**
     * Redirige vers une URL.
     * @param string $action : l'action que l'on veut faire (correspond aux actions dans le routeur).
     * @param array $params : Facultatif, les paramètres de l'action sous la forme ['param1' => 'valeur1', 'param2' => 'valeur2']
     * @return void
     */
    public static function redirect(string $action, array $params = []): void
    {
        $url = self::actionUrl($action, $params);
        header("Location: $url");
        exit();
    }

    /**
     * Cette méthode retourne le code js a insérer en attribut d'un bouton.
     * pour ouvrir une popup "confirm", et n'effectuer l'action que si l'utilisateur
     * a bien cliqué sur "ok".
     * @param string $message : le message à afficher dans la popup.
     * @return string : le code js à insérer dans le bouton.
     */
    public static function askConfirmation(string $message): string
    {
        return "onclick=\"return confirm('$message');\"";
    }

    /**
     * Cette méthode protège une chaine de caractères contre les attaques XSS.
     * De plus, elle transforme les retours à la ligne en balises <p> pour un affichage plus agréable.
     * @param string $string : la chaine à protéger.
     * @return string : la chaine protégée.
     */
    public static function format(string $string): string
    {
        // Etape 1, on protège le texte avec htmlspecialchars.
        $finalString = htmlspecialchars($string, ENT_QUOTES);

        // Etape 2, le texte va être découpé par rapport aux retours à la ligne, 
        $lines = explode("\n", $finalString);

        // On reconstruit en mettant chaque ligne dans un paragraphe (et en sautant les lignes vides).
        $finalString = "";
        foreach ($lines as $line) {
            if (trim($line) != "") {
                $finalString .= "<p>$line</p>";
            }
        }

        return $finalString;
    }

    /**
     * Cette méthode permet de récupérer une variable de la superglobale $_REQUEST.
     * Si cette variable n'est pas définie, on retourne la valeur null (par défaut)
     * ou celle qui est passée en paramètre si elle existe.
     * @param string $variableName : le nom de la variable à récupérer.
     * @param mixed $defaultValue : la valeur par défaut si la variable n'est pas définie.
     * @return mixed : la valeur de la variable ou la valeur par défaut.
     */
    public static function request(string $variableName, mixed $defaultValue = null): mixed
    {
        return $_REQUEST[$variableName] ?? $defaultValue;
    }

    /**
     * Retourne l'URL d'une icône de tri en fonction du type de tri et de la colonne.
     *
     * @param string $sort La colonne actuellement triée.
     * @param string $order L'ordre de tri ('asc' ou 'desc').
     * @param string $column La colonne pour laquelle on affiche l'icône.
     * @param string $type Le type de données de la colonne ('number', 'string', 'date').
     *
     * @return string L'URL de l'icône correspondante.
     * @throws InvalidArgumentException Si l'ordre ou le type est invalide.
     */
    public static function getSortIcon(string $sort, string $order, string $column, string $type): string
    {
        if (!in_array($order, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException("Valeur de tri invalide : '$order'. Valeurs acceptées : 'asc' ou 'desc'.");
        }

        $validTypes = ['number', 'string', 'date'];
        if (!in_array($type, $validTypes, true)) {
            throw new InvalidArgumentException("Type de colonne invalide : '$type'. Valeurs acceptées : 'int', 'string', 'date'.");
        }

        // Déterminer l'icône en fonction du type
        $iconType = match ($type) {
            'number' => '1-0',
            'string' => 'a-z',
            'date' => 'calendar',
        };

        // Vérifie si la colonne actuelle est celle qui est triée
        if ($sort === $column) {
            $icon = "arrow-" . $order . "-" . $iconType . ".svg";
        } else {
            $icon = "arrow-neutral.svg";
        }

        return "assets/icons/sort/" . $icon;
    }


}