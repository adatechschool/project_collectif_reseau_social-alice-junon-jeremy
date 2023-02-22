<?php
session_start();
?>
<!doctype html>

<?php
include 'composants/header.php';
?>
<div id="wrapper">
    <?php

    if (!isset($_SESSION['connected_id'])) {
        header('Location: login.php');
    }
    /**
     * Cette page est TRES similaire à wall.php. 
     * Vous avez sensiblement à y faire la meme chose.
     * Il y a un seul point qui change c'est la requete sql.
     */
    /**
     * Etape 1: Le mur concerne un utilisateur en particulier
     */
    $userId = intval($_SESSION['connected_id']);
    ?>
    <?php
    /**
     * Etape 2: se connecter à la base de donnée
     */
    include 'composants/callsql.php';
    ?>

    <aside>
        <?php
        /**
         * Etape 3: récupérer le nom de l'utilisateur
         */
        $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        $user = $lesInformations->fetch_assoc();
        //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
        //echo "<pre>" . print_r($user, 1) . "</pre>";
        echo "<pre>" . print_r($_SESSION['connected_id']) . "</pre>";
        ?>
        <img src="./img/user.jpg" alt="Portrait de l'utilisatrice" />
        <section>
            <h3>Présentation</h3>
            <p>Sur cette page vous trouverez tous les message des utilisatrices
                auxquel est abonnée l'utilisatrice <?php echo $user['alias'] ?>
            </p>

        </section>
    </aside>
    <main>
        <?php

        include('addlike.php');
        /**
         * Etape 3: récupérer tous les messages des abonnements
         */
        $laQuestionEnSql = "
                    SELECT posts.content,
                    users.id,
                    posts.id as post_id,
                    posts.created,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM followers 
                    JOIN users ON users.id=followers.followed_user_id
                    JOIN posts ON posts.user_id=users.id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE followers.following_user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        if (!$lesInformations) {
            echo ("Échec de la requete : " . $mysqli->error);
        }
        /**
         * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
         * A vous de retrouver comment faire la boucle while de parcours...
         */
        while ($post = $lesInformations->fetch_assoc()) {

        ?>
            <article>
                <h3>
                    <time datetime='<?php echo $post['created'] ?>'><?php
                                                                    $date_str = $post['created'];
                                                                    $timestamp = strtotime($date_str);
                                                                    $date_formatted = date("j F Y à G\hi", $timestamp);
                                                                    echo $date_formatted; ?></time>
                </h3>
                <address>par <a href=" wall.php?user_id=<?php echo $post['id'] ?>"><?php echo $post['author_name'] ?></a></address>
                <div>
                    <p><?php echo $post['content'] ?></p>
                </div>
                <footer>
                    <small>
                        <form method="post">
                            <input type="hidden" value="<?php echo $post['post_id'] ?>" name="post_id"></input>
                            <input type='submit' value="♥ <?php echo $post['like_number'] ?>">
                        </form>
                    </small>
                    <?php


                    $taglist = $post['taglist'];
                    $tags = explode(",", $post['taglist']);
                    foreach ($tags as $value) {
                    ?>
                        <a href="tags.php?tag_id=<?php echo $tag['id'] ?>"><?php echo "#" . $value ?></a>
                    <?php
                    }
                    ?>
                </footer>
            </article>
        <?php } ?>


    </main>
</div>
</body>

</html>