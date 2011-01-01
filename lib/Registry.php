<?php
/* Aroo
 * (C) by the authors.
 *
 * This file is part of Aroo.
 *
 * Aroo is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * Aroo is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Aroo. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aroo;

/**
 * A global storage for pretty much everything. Must use unique keys.
 * 
 * @author Andrea Franceschini <andrea.franceschini@gmail.com>
 * 
 * @package Aroo
 * @subpackage Core
 * @since 0.0.1
 */
class Registry {
	
	const OK = 0;
	const REPLACED = 1;
	const NULLIFIED = 2;
	const NONEXISTENT = 3;

	/**
	 * The storage.
	 * 
	 * @var mixed
	 */
	private static $registry = array();
	
	/**
	 * Returns the value stored with the given key.
	 * 
	 * @param string $key
	 * @return mixed if the key is set, null otherwise.
	 */
	public static function get($key = null) {
		if($key != null) {
			if(array_key_exists($key, self::$registry)) {
				return self::$registry[$key];
			}
		}
		return null;
	}
	
	/**
	 * Sets the value for the given key.
	 *
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return Registry::OK if everything went fine, Registry::REPLACED if the
	 *         key was already set, and Registry::NULLIFIED if $value was null.
	 */
	public static function set($key, $value = null) {
		$result = self::OK;
		if(array_key_exists($key, self::$registry)) {
			$result = self::REPLACED;
		}
		if($value == null) {
			$result = self::NULLIFIED;
		}
		
		self::$registry[$key] = $value;
		return $result;
	}
	
	/**
	 * Nullifies the value of the given key if the key exists, nop otherwise.
	 * 
	 * @param string $key
	 * 
	 * @return Registry::NULLIFIED if the key exists, Registry::NONEXISTENT if
	 *         the key was not found.
	 */
	public static function reset($key) {
		if(array_key_exists($key, self::$registry)) {
			return self::set($key);
		} else {
			return self::NONEXISTENT;
		}
	}
	
	/**
	 * Returns the whole storage.
	 */
	public static function getAll() {
		return self::$registry;
	}
}
