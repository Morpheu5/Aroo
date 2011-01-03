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

namespace Aroo\View;

require_once 'Smarty/Smarty.class.php';

class Smarty extends \Aroo\View {
	
	protected $engine = null;
	
	public function __construct($config = array()) {
		$this->engine = new \Smarty;
		
		parent::__construct($config);
	}
	
	public function setTempDir($directory = null) {
		if($directory === null) {
			throw new \Aroo\Exception\InvalidDirectory('Directory can\'t be null');
		}
		if(!file_exists($directory) || !is_dir($directory) || !is_writable($directory)) {
			throw new \Aroo\Exception\InvalidDirectory('Directory "'.$directory.'" is not valid');
		}

		$cplDir = $directory . '/templates_c';
		$cacheDir = $directory . '/cache';
		if(!file_exists($cplDir)) {
			mkdir($cplDir, 0777, true);
		}
		if(!file_exists($cacheDir)) {
			mkdir($cacheDir, 0777, true);
		}
		$this->engine->setCompileDir($cplDir);
		$this->engine->setCacheDir($cacheDir);
	}
	
	public function setTemplatesDir($directory = null) {
		if($directory === null) {
			throw new \Aroo\Exception\InvalidDirectory('Directory can\'t be null');
		}
		$this->engine->setTemplateDir($directory);
	}
}