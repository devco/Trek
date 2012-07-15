<?php

namespace Trek;

/**
 * Represents the most basic implementation of a version.
 * 
 * @category Versioning
 * @package  Trek
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  MIT http://www.opensource.org/licenses/mit-license.php
 */
interface VersionInterface
{
	/**
	 * Returns the version as a string.
	 * 
	 * @return string
	 */
	public function __toString();
	
	/**
	 * Compares this version to the specified version.
	 * 
	 * @param mixed $version The version to compare to.
	 * 
	 * @return int
	 */
	public function compare($version);
	
	/**
	 * Returns a namespace representing the version.
	 * 
	 * @return string
	 */
	public function ns();
}