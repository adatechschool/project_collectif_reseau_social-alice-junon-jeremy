<?php


$taglist = $post['taglist'];
$tags = explode(",", $post['taglist']);
foreach ($tags as $value) {
    $laQuestionEnSql = "SELECT tags.id FROM tags WHERE tags.label= '$value' ";
    $lesInformations = $mysqli->query($laQuestionEnSql);
    $tag = $lesInformations->fetch_assoc();
    //echo print_r($tag) 
?>
    <a href="tags.php?tag_id=<?php echo $tag['id'] ?>"><?php echo "#" . $value ?></a>
<?php
}
