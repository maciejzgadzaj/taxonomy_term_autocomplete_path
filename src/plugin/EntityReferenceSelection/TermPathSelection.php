<?php

namespace Drupal\taxonomy_term_autocomplete_path\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\taxonomy\Plugin\EntityReferenceSelection\TermSelection;

/**
 * Provides specific access control for the taxonomy_term entity type.
 *
 * @EntityReferenceSelection(
 *   id = "default:taxonomy_term",
 *   label = @Translation("Taxonomy Term with path selection"),
 *   entity_types = {"taxonomy_term"},
 *   group = "default",
 *   weight = 1
 * )
 */
class TermPathSelection extends TermSelection {

  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $options = parent::getReferenceableEntities($match, $match_operator, $limit);

    if ($this->configuration['target_type'] == 'taxonomy_term') {
      foreach ($options as $bundle => $bundle_options) {
        foreach ($bundle_options as $term_id => $label) {
          $path = [];
          $parents = $this->entityManager->getStorage('taxonomy_term')->loadAllParents($term_id);
          foreach (array_reverse($parents) as $term) {
            $path[$term->id()] = $term->label();
          }
          $options[$bundle][$term_id] = Html::escape('/' . implode('/', $path));
        }
      }
    }

    return $options;
  }

}
