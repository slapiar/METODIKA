<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('asset_url')) {
	/**
	 * Returns versioned asset URL for cache-busting.
	 *
	 * Version priority:
	 * 1) RELEASE_VERSION marker
	 * 2) filemtime fallback for local edits
	 */
	function asset_url(string $path): string
	{
		static $releaseVersion;
		static $releaseVersionLoaded = false;

		$normalizedPath = ltrim($path, '/');
		$url = base_url($normalizedPath);

		if (! $releaseVersionLoaded) {
			$releaseVersionLoaded = true;
			$releaseVersion = null;

			$versionFiles = [
				ROOTPATH . 'RELEASE_VERSION',
				dirname(ROOTPATH) . '/RELEASE_VERSION',
				ROOTPATH . 'deploy/RELEASE_VERSION.txt',
			];

			foreach ($versionFiles as $versionFile) {
				if (! is_file($versionFile) || ! is_readable($versionFile)) {
					continue;
				}

				$value = trim((string) file_get_contents($versionFile));
				if ($value !== '') {
					$releaseVersion = $value;
					break;
				}
			}
		}

		if (is_string($releaseVersion) && $releaseVersion !== '') {
			return $url . '?v=' . rawurlencode($releaseVersion);
		}

		$assetFile = FCPATH . $normalizedPath;
		if (is_file($assetFile)) {
			return $url . '?v=' . filemtime($assetFile);
		}

		return $url;
	}
}
