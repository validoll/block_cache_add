<?php

namespace Drupal\block_cache_add\Entity;

use Drupal\block\Entity\Block as BlockCore;

/**
 * Defines a Block configuration entity class to override origin.
 */
class Block extends BlockCore {

  public function __construct(array $values, $entity_type) {
    parent::__construct($values, $entity_type);

    // Apply custom cache settings.
    $this->applyCustomCacheContexts();
    $this->applyCustomCacheTags();
    $this->applyCustomMaxAge();
  }

  /**
   * Read and load custom cache contexts.
   */
  protected function applyCustomCacheContexts() {
    $block_cache_contexts = $this->getThirdPartySetting('block_cache_add', 'cache_context', []);
    $block_cache_contexts = array_filter(array_map(function ($context) {
      if (!empty($context['cache_context_id'])) {
        $context_value = trim($context['cache_context_id']);
        $context_value = !isset($context['cache_context_value']) ? $context_value : $context_value . ':' . $context['cache_context_value'];
        return $context_value;
      }
      return NULL;
    },
      $block_cache_contexts
    ));
    $this->addCacheContexts($block_cache_contexts);
  }

  /**
   * Read and load custom cache tags.
   */
  protected function applyCustomCacheTags() {
    $block_cache_tags_values = $this->getThirdPartySetting('block_cache_add', 'cache_tags', []);
    foreach ($block_cache_tags_values as $tags) {
      $tags = explode(',', $tags['cache_tags_value']);
      $tags = array_filter(array_map(function ($tag) {
        return trim($tag);
      },
        $tags
      ));
      $this->addCacheTags($tags);
    }
  }

  /**
   * Read and load custom 'max-age' header value.
   */
  protected function applyCustomMaxAge() {
    $block_cache_max_age = $this->getThirdPartySetting('block_cache_add', 'cache_max_age', ['value' => $this->getCacheMaxAge()]);

    $this->mergeCacheMaxAge((int) $block_cache_max_age['value']);
  }

}
