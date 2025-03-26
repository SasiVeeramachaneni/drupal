<?php

namespace Drupal\custom_api_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;

/**
 * Provides a block that fetches and displays data from an API.
 *
 * @Block(
 *   id = "api_data_block",
 *   admin_label = @Translation("API Data Block")
 * )
 */
class ApiDataBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * HTTP client to fetch external data.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Constructs a new ApiDataBlock instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $output = '';
  
    try {
      $response = $this->httpClient->request('GET', 'https://jsonplaceholder.typicode.com/posts');
      $data = json_decode($response->getBody(), TRUE);
  
      foreach (array_slice($data, 0, 6) as $item) {
        $id = $item['id'];
        $title = htmlspecialchars($item['title']);
        $body = nl2br(htmlspecialchars($item['body']));
        $image_url = "https://picsum.photos/seed/{$id}/300/200";
  
        // Wrap tile and modal separately to prevent layout issues
        $output .= "
          <div class='api-tile' data-modal-id='modal-{$id}'>
            <img src='{$image_url}' alt='{$title}' class='api-tile-image'>
            <h4 class='api-tile-title'>{$title}</h4>
          </div>
  
          <div id='modal-{$id}' class='api-modal' style='display:none;'>
            <div class='api-modal-content'>
              <span class='api-close'>&times;</span>
              <h3>{$title}</h3>
              <img src='{$image_url}' alt='{$title}' style='max-width: 100%; border-radius: 8px; margin-bottom: 1rem;'>
              <p>{$body}</p>
            </div>
          </div>
        ";
      }
    }
    catch (\Exception $e) {
      $output = '<p>Error fetching data from API.</p>';
    }
  
    return [
      '#markup' => "<div class='api-block-wrapper'><div class='api-tile-grid'>{$output}</div></div>",
      '#attached' => [
        'library' => ['custom_api_block/api_modal'],
      ],
      '#cache' => ['max-age' => 0],
    ];
  }
  
  

}
