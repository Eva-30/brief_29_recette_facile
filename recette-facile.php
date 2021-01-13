<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>recette-facile</title>
  <meta name="description" content="le levain il n'y a que ça de vrai...">
</head>
<body><pre><?php

  // séparer ses identifiants et les protéger, une bonne habitude à prendre

  include "recette-facile.dbconf.php";

  try {

    // instancie un objet $connexion à partir de la classe PDO

    $connexion = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);

    //requête de sélection pour requêter des données issues de la table "recettes".

    $requeteSelectionTable = "SELECT * FROM `recettes`";
    $prepare = $connexion->prepare($requeteSelectionTable);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requeteSelectionTable, $resultat]); // debug & vérification

    // requête de sélection pour requêter des données issues de la table "recettes" via l'id

    $requeteSelectioncolonne = "SELECT *
                                FROM `recettes`
                                WHERE `recette_id` = :recette_id"; // on cible la recette dont l'id est ...
    $prepare = $connexion->prepare($requeteSelectioncolonne);
    $prepare->execute(array(":recette_id" => 2)); // on cible la recette dont l'id est 2
    $resultat = $prepare->fetchAll();
    print_r([$requeteSelectioncolonne, $resultat]); // debug & vérification

    // requête d'insertion qui ajoute une nouvelle recette de mon choix dans la table "recettes".

    $requeteInsertion = "INSERT INTO `recettes` (`recette_titre`, `recette_contenu`, `recette_datetime`) 
                         VALUES (:recette_titre, :recette_contenu, :recette_datetime);";
    $prepare = $connexion->prepare($requeteInsertion);
    $prepare->execute(array(
      ":recette_titre" => "Gnocchi maison",
      ":recette_contenu" => "## Ingrédients \n -1 kg de pommes de terre \n -250 g de farine de blé \n -2 jaunes d’œufs \n -1 c. à soupe de beurre \n -sel \n -poivre \n ##Préparation \n
      -Lavez les pommes de terre puis plongez-les dans une grande casserole remplie d’eau fraîche et salée.\n -Faites-les cuire pendant environ 30 min.\n -En fin de cuisson, plantez la lame d’un couteau dans une pomme de terre.\n -Lorsque la chair est cuite, la lame s’enfonce sans résistance.\n2.
      -Égouttez les pommes de terre, épluchez-les et réduisez-les en purée à la fourchette ou à l’aide d’un presse-purée à manivelle.\n -Dans un saladier, mélangez la purée de pommes de terre, la farine tamisée et les jaunes d’œufs.\n -Assaisonnez à votre convenance avec le sel et le poivre.\n -Pétrissez ensuite à la main la préparation jusqu'à l'obtention d'une pâte homogène.\n
      -Farinez généreusement votre plan de travail.\n -Toujours à la main, formez de petites boules de pâtes régulières.\n -Roulez les gnocchi de pommes de terre sur le dos d’une fourchette.\n -Vous pouvez également réaliser les stries avec un couteau, pour démarquer les crans des gnocchi.\n
      -Dans une grande casserole, faites bouillir de l’eau salée.\n -Pochez-y les gnocchi jusqu’à ce qu’ils remontent à la surface.\n -Gouttez-les pour vérifier leur texture et leur cuisson.\n -Égouttez-les dans une passoire.\n
      -Faites chauffer une poêle avec la cuillère à soupe de beurre.\n -Faites-y dorer les gnocchi quelques minutes, pour le côté croustillant.\n -Servez aussitôt avec un peu de parmesan râpé.",
      ":recette_datetime" => date('Y-m-d H:i:s'),
    ));
    $resultat = $prepare->rowCount(); // rowCount() nécessite PDO::MYSQL_ATTR_FOUND_ROWS => true
    $lastInsertedRecetteId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL
    print_r([$requeteInsertion, $resultat, $lastInsertedRecetteId]); // debug & vérification

    // requête de modification qui modifie la nouvelle recette,rajoute l'émoji de mon choix au début du titre.

    $patateIcone = "🥔"; //création variable de l'émoji.
    $requeteModification = "UPDATE `recettes`
                            SET `recette_titre` = :recette_titre
                            WHERE `recette_id` = :recette_id;";
    $prepare = $connexion->prepare($requeteModification);
    $prepare->execute(array(
      ":recette_id"   => $lastInsertedRecetteId,
      ":recette_titre" => $patateIcone . "Gnocchi maison : souvenirs en famille"
    ));
    $resultat = $prepare->rowCount();
    print_r([$requeteModification, $resultat]); // debug & vérification

// requête de suppression qui supprime une entrée de la table "recettes".

//     $requeteSuppression = "DELETE FROM `recettes`
//                            WHERE ((`recette_id` = :recette_id));";
//     $prepare = $connexion->prepare($requeteSuppression);
//     $prepare->execute(array($lastInsertedRecetteId)); // on lui passe l'id tout juste créé
//     $resultat = $prepare->rowCount();
//     print_r([$requeteSuppression, $resultat, $lastInsertedRecetteId]); // debug & vérification

//  requête qui ajoute l'entrée "levain" dans la table "hashtags"

    $requeteInsertion2 = "INSERT INTO `hashtags`(`hashtag_nom`)
                          VALUES (:hashtag_nom);";
    $prepare = $connexion->prepare($requeteInsertion2);
    $prepare->execute(array(
      ":hashtag_nom" => "levain"
    ));
    $resultat = $prepare->rowCount();
    print_r([$requeteInsertion2, $resultat]); // debug & vérification

    //requete qui lie la colonne du levain au celle de la recette du pain au levain dans la table associative

    $requete = "INSERT INTO `assoc_hashtags_recettes`(`assoc_hr_hashtag_id`, `assoc_hr_recette_id`)
                VALUES (:assoc_hr_hashtag_id, :assoc_hr_recette_id);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":assoc_hr_hashtag_id" => 4,
      ":assoc_hr_recette_id" => 1
    ));
    $resultat = $prepare->rowCount();
    print_r([$requete, $resultat]); // debug & vérification

   // Essai requête de sélection pour requêter des données dont le hashtag est "nourriture" et afficher le titre de chaque recette concernée.

   $requeteSelectioncolonne = "SELECT hashtag_nom, recette_titre
                               FROM assoc_hashtags_recettes
                               INNER JOIN hashtags ON hashtag_id = assoc_hr_hashtag_id
                               INNER JOIN recettes ON recette_id = assoc_hr_recette_id
                               WHERE hashtag_id = 1;";
    $prepare = $connexion->prepare($requeteSelectioncolonne);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requeteSelectioncolonne, $resultat]); // debug & vérification

   } catch (PDOException $e) {

    // en cas d'erreur, on récup et on affiche, grâce au try/catch

    exit("❌🙀💀 OOPS :\n" . $e->getMessage());

  }

?></pre></body>
</html>