<?php
/**
 * Plugin Name: Avada ShortCode Remover
 * Plugin URI: http://codebeastmode.com
 * Description: Automatically saves avada JS & CSS and turns shortcodes into divs and gives them classes
 *
 * Version: 1.0
 * Author: Chris Quinn
 * Author URI: http://codebeastmode.com
 * Requires at least: 5.0
 */

/**
 * Check for direct call file.
 */
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

if (defined('AVA_VERSION')) {
   return;
}

class AVSCEnqueueController
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueGlobalAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueSourceAssets']);
    }

    public function enqueueGlobalAssets()
    {
        $bundleUrl = get_option('AVA-globalElementsCssFileUrl');
        if ($bundleUrl) {
            wp_enqueue_style('AVA:assets:global:styles:' . $this->slugify($bundleUrl), $bundleUrl);
        }
    }

    public function enqueueSourceAssets()
    {
        $sourceId = get_the_ID();
        $bundleUrl = get_post_meta($sourceId, 'AVASourceCssFileUrl', true);
        if ($bundleUrl) {
            wp_enqueue_style('AVA:assets:source:main:styles:' . $this->slugify($bundleUrl), $bundleUrl);
        }
    }

    public function enqueueAssets()
    {
        $sourceId = get_the_ID();
        $assetsFiles = get_post_meta($sourceId, 'AVASourceAssetsFiles', true);

        if (!is_array($assetsFiles)) {
            return;
        }

        if (isset($assetsFiles['cssBundles']) && is_array($assetsFiles['cssBundles'])) {
            foreach ($assetsFiles['cssBundles'] as $asset) {
                wp_enqueue_style('AVA:assets:source:styles:' . $this->slugify($asset), $asset);
            }
            unset($asset);
        }

        if (isset($assetsFiles['jsBundles']) && is_array($assetsFiles['jsBundles'])) {
            foreach ($assetsFiles['jsBundles'] as $asset) {
                wp_enqueue_script('AVA:assets:source:scripts:' . $this->slugify($asset), $asset);
            }
            unset($asset);
        }
    }

    public function slugify($str)
    {
        $str = strtolower($str);
        $str = html_entity_decode($str);
        $str = preg_replace('/[^\w\s]+/', '', $str);
        $str = preg_replace('/\s+/', '-', $str);

        return $str;
    }
}

new AVSCEnqueueController();
