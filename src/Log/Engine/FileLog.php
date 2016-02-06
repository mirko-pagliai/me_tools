<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://api.cakephp.org/3.1/class-Cake.Log.Engine.FileLog.html FileLog
 */
namespace MeTools\Log\Engine;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Log\Engine\FileLog as CakeFileLog;
use Cake\Network\Exception\InternalErrorException;

/**
 * File Storage stream for Logging. Writes logs to different files based on the level of log it is.
 * 
 * Rewrites {@link http://api.cakephp.org/3.1/class-Cake.Log.Engine.FileLog.html FileLog}.
 */
class FileLog extends CakeFileLog {
	/**
	 * Gets all log files
	 * @return array Log files
	 */
	public static function all() {
		//Gets log files
		//For each file, the array key will be the filename without extension
		foreach((new Folder(LOGS))->find('[^\.]+\.log(\.[^\-]+)?', TRUE) as $k => $file)
			$files[pathinfo($file, PATHINFO_FILENAME)] = $file;
		
		return $files;
	}
	
    /**
     * Checks if the logs directory is readable and writable
     * @return boolean
     */
	public static function check() {
		return folder_is_writable(LOGS);
	}
	
	/**
     * Clears all log files
     * @return boolean
	 * @uses check()
	 */
	public static function clear() {
		if(!self::check())
			return FALSE;
		
		$success = TRUE;
		
		//Deletes each file
        foreach((new Folder(LOGS))->findRecursive() as $file)
            if(!(new File($file))->delete() && $success)
                $success = FALSE;
		
        return $success;
	}
	
	/**
	 * Gets a log file
	 * @param string $log Log name
	 * @return string Log content
	 * @throws InternalErrorException
	 */
	public static function get($log) {
		if(!is_readable($file = LOGS.$log))
			throw new InternalErrorException(__d('me_tools', 'File or directory `{0}` not readable', $file));
		
		return @file_get_contents($file);
	}
	
	/**
	 * Gets the logs size
	 * @return int Logs size
	 */
	public static function size() {
        return dirsize(LOGS);
	}
}