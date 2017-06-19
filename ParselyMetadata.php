<?php
namespace Drupal\parsely;
use Drupal\Core\Language\Language;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;

class ParselyMetadata {

    protected   $creator;
    protected   $keywords;
    protected   $articleId;
    protected   $articleSection;
    protected   $context;
    protected   $dateCreated;
    protected   $headline;
    protected   $type;
    protected   $url;

    public function __construct($node) {
        $this->articleID = $this->setID($node);
        $this->creator = $this->setCreator($node);
        $this->datePublished = $this->setDate($node);
        $this->keywords = $this->setTags($node);
        $this->articleSection = $this->setSection($node);


    }

    /* ~~~ Setters (protected) ~~~ */



    /**
     * @param $node Node
     * @return string
     */

    protected function setID($node) {

        $prefix = \Drupal::config('parsely.settings')->get('parsely_content_id_prefix');
        if (!empty($prefix)) {
            $prefix = $prefix . '-';
        }

        $node_id = $node->id();
        return $prefix.$node_id;

    }



    // @TODO: profile this function.

    /**
     * @param $node Node
     * @return string
     */

    protected function setCreator($node) {
                $author = $node->getOwner();
                return $author->getDisplayName();
    }


    /**
     * @param $node Node
     * @return false|string
     */
    protected function setDate($node) {

        $pub_date = $node->getCreatedTime();

        return gmdate("Y-m-d\TH:i:s\Z", $pub_date);
    }


    /**
     * @param $node Node
     * @return array
     */
    protected function setTags($node) {

        $tags = [];
        $vocabularies = \Drupal::config('parsely.settings')->get('parsely_tag_vocabularies');
        if (!\Drupal::moduleHandler()->moduleExists('taxonomy') || $vocabularies === NULL || $vocabularies === '') {
            return array();
        }
        foreach($vocabularies as $vocab) {
            $entity = Vocabulary::load($vocab);
//            $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($entity->id());
            $clean_term_name = $entity->get('vid');
            $term_ids = $node->get('field_'.$clean_term_name)->getValue();

            foreach ($term_ids as $term_id) {
                    $term_name = Term::load($term_id['target_id'])->getName();
                   array_push($tags, $term_name);
            }
        }
        return $tags;
    }

    protected function setSection($node) {

        return parsely_get_section($node);

    }


    /* ~~~ Getters (public) ~~~ */

    public function getCreator() {

        return $this->creator;

    }

    public function getDate() {

        return $this->datePublished;

    }

    public function getID() {

        return $this->articleID;

    }

    public function getSection() {

        return $this->articleSection;

    }

    public function getTags() {

        return $this->keywords;

    }

    public function getURL($node) {
        return Url::fromRoute('entity.node.canonical', ['node' => $node->id()], ['absolute' => TRUE]);
    }

}
