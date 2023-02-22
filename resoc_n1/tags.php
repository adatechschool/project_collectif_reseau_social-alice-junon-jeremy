<?php
session_start();
?>

<!doctype html>

<?php
include 'composants/header.php';
?>

<div id="wrapper">
    <?php
    /**
     * Cette page est similaire à wall.php ou feed.php 
     * mais elle porte sur les mots-clés (tags)
     */
    /**
     * Etape 1: Le mur concerne un mot-clé en particulier
     */
    $userId = intval($_SESSION['connected_id']);
    $tagId = intval($_GET['tag_id']);
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
         * Etape 3: récupérer le nom du mot-clé
         */
        $laQuestionEnSql = "SELECT * FROM tags WHERE id= '$tagId' ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        $tag = $lesInformations->fetch_assoc();
        //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous
        // echo "<pre>" . print_r($tag, 1) . "</pre>";
        ?>
        <img src="./img/user.jpg" alt="Portrait de l'utilisatrice" />
        <section>
            <h3>Présentation</h3>
            <p>Sur cette page vous trouverez les derniers messages comportant
                le mot-clé <?php echo $tag['label'] ?>
            </p>

        </section>
    </aside>
    <main>
        <?php
        include('addlike.php');

        /**
         * Etape 3: récupérer tous les messages avec un mot clé donné
         */
        $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.id as post_id,
                    users.id as user_id, 
                    posts.created,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts_tags as filter 
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE filter.tag_id = '$tagId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        if (!$lesInformations) {
            // echo ("Échec de la requete : " . $mysqli->error);
        }

        /**
         * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
         */
        while ($post = $lesInformations->fetch_assoc()) {

            //echo "<pre>" . print_r($post, 1) . "</pre>";
        ?>
            <article>
                <h3>
                    <time datetime='<?php echo $post['created'] ?>'><?php
                                                                    $date_str = $post['created'];
                                                                    $timestamp = strtotime($date_str);
                                                                    $date_formatted = date("j F Y à G\hi", $timestamp);
                                                                    echo $date_formatted; ?></time>
                </h3>
                <address>par <a href="wall.php?user_id=<?php echo $post['id'] ?>"><?php echo $post['author_name'] ?></a></address>
                <div>
                    <p><?php echo $post['content'] ?></p>
                </div>
                <footer>
                    <small>
                        <form method="post">
                            <input class="likebutton" type="hidden" value="<?php echo $post['post_id'] ?>" name="post_id"></input>
                            <input class="likebutton" type='submit' value="♥ <?php echo $post['like_number'] ?>">
                        </form>
                    </small>
                    <?php
                    $taglist = $post['taglist'];
                    $tags = explode(",", $post['taglist']);
                    foreach ($tags as $value) {
                        echo "<a href=''> #" . $value . "</a>";
                    }
                    ?>
                </footer>
            </article>
        <?php } ?>


    </main>
</div>
</body>

</html>