<?php

namespace Drupal\current_page_crumb;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\system\PathBasedBreadcrumbBuilder;
use Drupal\Core\Routing\RouteObjectInterface;

/**
 * Adds the current page title to the breadcrumb.
 *
 * Extend PathBased Breadcrumbs to include the current page title as an unlinked
 * crumb. The module uses the path if the title is unavailable and it excludes
 * all admin paths.
 *
 * {@inheritdoc}
 */
class BreadcrumbBuilder extends PathBasedBreadcrumbBuilder {

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumbs = parent::build($route_match);

    $request = \Drupal::request();
    $path = trim($this->context->getPathInfo(), '/');
    $path_elements = explode('/', $path);
    $route = $request->attributes->get(RouteObjectInterface::ROUTE_OBJECT);

    // Do not adjust the breadcrumbs on admin paths and front page.
    if ($route && !$route->getOption('_admin_route') && !$this->pathMatcher->isFrontPage()) {
      $title = $this->titleResolver->getTitle($request, $route);
      if (empty($title)) {

        // Fallback to using the raw path component as the title if the
        // route is missing a _title or _title_callback attribute.
        $title = str_replace(['-', '_'], ' ', Unicode::ucfirst(end($path_elements)));
      }
      $breadcrumbs->addLink(Link::createFromRoute($title, '<none>'));
    }

    // Handle expiring views paths and any entity default page cache.
    $parameters = $route_match->getParameters();
    foreach ($parameters as $key => $parameter) {
      if ($key === 'view_id') {
        $breadcrumbs->addCacheTags(['config:views.view.' . $parameter]);
      }

      if ($parameter instanceof CacheableDependencyInterface) {
        $breadcrumbs->addCacheableDependency($parameter);
      }
    }

    // Expire the cache when things need to update based on route, path and language.
    $breadcrumbs->addCacheContexts(['route', 'url.path', 'languages']);

    return $breadcrumbs;
  }

}
