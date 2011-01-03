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

class View {
	public function __construct($config = array()) {
		if(array_key_exists('tempDir', $config)) {
			$this->setTempDir($config['tempDir']);
		} else {
			$config = Registry::get('aroo_configuration');
			$this->setTempDir($config['tempDir']);
		}
		if(array_key_exists('templates', $config)) {
			$this->setTemplatesDir($config['templates']);
		} else {
			$this->setTemplatesDir(realpath(APPLICATION_PATH . '/views'));
		}
	}
	
}