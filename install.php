<?php
//This file is part of FreePBX.
//
//    FreePBX is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 2 of the License, or
//    (at your option) any later version.
//
//    FreePBX is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with FreePBX.  If not, see <http://www.gnu.org/licenses/>.
//
//    Copyright 2006 Greg MacLellan
//

global $db;
global $amp_conf;

$autoincrement = (($amp_conf["AMPDBENGINE"] == "sqlite") || ($amp_conf["AMPDBENGINE"] == "sqlite3")) ? "AUTOINCREMENT":"AUTO_INCREMENT";
// create the tables
$sql = "CREATE TABLE IF NOT EXISTS fagi (
	id INTEGER NOT NULL PRIMARY KEY $autoincrement,
	displayname varchar(32) NOT NULL,
	description varchar(50) NOT NULL,
	host varchar(30) default NULL,
	port varchar(30) default NULL,
	path varchar(100) default NULL,
	query varchar(100) default NULL,
        truegoto varchar(50) default NULL,
        falsegoto varchar(50) default NULL)";

$check = $db->query($sql);
if(DB::IsError($check)) {
	die_freepbx("Can not create announcement table");
}

?>
