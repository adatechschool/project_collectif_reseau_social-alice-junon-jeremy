<?php $tagsArray = explode(",", $post['taglist']);
for ($i = 0; $i < count($tagsArray); $i++) {
    if ($tagsArray[0] == NULL) {
        echo "</br>";
    } else if ($i === count($tagsArray) - 1) { ?>
        <a href=<?php echo "tags.php?tag_label=" . $tagsArray[$i] ?>><?php echo "#" . $tagsArray[$i] ?></a>
    <?php } else { ?>
        <a href=<?php echo "tags.php?tag_label=" . $tagsArray[$i] ?>><?php echo "#" . $tagsArray[$i] ?></a>,
    <?php } ?>
<?php } ?>

<!--boucle for qui parcourt le tableau $tagsArray et qui modifie le DOM à chaque passage de boucle : ajoute un lien cliquable pour chaque tag. Au dernier tag, echo sans la "," -->
Footer