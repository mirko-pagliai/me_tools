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
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

if (!function_exists('am')) {
    /**
     * Merge a group of arrays.
     * Accepts variable arguments. Each argument will be converted into an
     *  array and then merged.
     * @return array All array parameters merged into one
     */
    function am()
    {
        $array = [];

        foreach (func_get_args() as $arg) {
            $array = array_merge($array, (array)$arg);
        }

        return $array;
    }
}

if (!function_exists('clearDir')) {
    /**
     * Cleans a directory, deleting all the files, even in sub-directories
     * @param string $directory Directory path
     * @return bool
     */
    function clearDir($directory)
    {
        $success = true;

        //Gets files
        $files = (new Folder($directory))->tree(false, ['empty'])[1];

        //Deletes each file
        foreach ($files as $file) {
            if (!(new File($file))->delete()) {
                $success = false;
            }
        }

        return $success;
    }
}

if (!function_exists('folderIsWriteable')) {
    /**
     * Checks if a directory and its subdirectories are readable and writable
     * @param string $dir Directory path
     * @return bool
     */
    function folderIsWriteable($dir)
    {
        if (!is_readable($dir) || !is_writeable($dir)) {
            return false;
        }

        $subdirs = (new Folder())->tree($dir, false, 'dir');

        foreach ($subdirs as $subdir) {
            if (!is_readable($subdir) || !is_writeable($subdir)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('getChildMethods')) {
    /**
     * Gets the class methods' names, but unlike the `get_class_methods()`
     *  function, this function excludes the methods of the parent class
     * @param string $class Class name
     * @param string|array $exclude Methods to be excluded
     * @return array|null
     */
    function getChildMethods($class, $exclude = [])
    {
        $methods = get_class_methods($class);
        $parent = get_parent_class($class);

        if ($parent) {
            $methods = array_diff($methods, get_class_methods($parent));
        }

        if ($exclude) {
            $methods = array_diff($methods, (array)$exclude);
        }

        return is_array($methods) ? array_values($methods) : null;
    }
}

if (!function_exists('isJson')) {
    /**
     * Checks if a string is JSON
     * @param string $string String
     * @return bool
     */
    function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('isPositive')) {
    /**
     * Checks if a string is a positive number
     * @param string $string String
     * @return bool
     */
    function isPositive($string)
    {
        return is_numeric($string) && $string > 0 && $string == round($string);
    }
}

if (!function_exists('isUrl')) {
    /**
     * Checks whether a url is invalid
     * @param string $url Url
     * @return bool
     */
    function isUrl($url)
    {
        return (bool)preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url);
    }
}

if (!function_exists('rtr')) {
    /**
     * Returns the relative path (to the APP root) of an absolute path
     * @param string $path Absolute path
     * @return string Relativa path
     */
    function rtr($path)
    {
        return preg_replace(sprintf('/^%s/', preg_quote(Folder::slashTerm(ROOT), DS)), null, $path);
    }
}

if (!function_exists('which')) {
    /**
     * Executes the `which` command.
     *
     * It shows the full path of (shell) commands.
     * @param string $command Command
     * @return string Full path of command
     */
    function which($command)
    {
        return exec(sprintf('which %s', $command));
    }
}
