<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package tool
 * @subpackage mergeusers
 * @author Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 Servei de Recursos Educatius (http://www.sre.urv.cat)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Take actions on upgrading mergeusers tool.
 * @package tool_mergeusers
 * @global moodle_database $DB
 * @param int $oldversion old plugin version.
 * @return boolean true when success, false on error.
 */
function xmldb_tool_mergeusers_upgrade ($oldversion) {
    global $CFG, $DB;
    require_once $CFG->dirroot . '/admin/tool/mergeusers/lib.php';

    $dbman = $DB->get_manager();

    if ($oldversion < 2013112912) {

        // Define table tool_mergeusers to be created
        $table = new xmldb_table('tool_mergeusers');

        // Adding fields to table tool_mergeusers
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('touserid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fromuserid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('success', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('log', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_mergeusers
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table tool_mergeusers
        $table->add_index('mdl_toolmerg_tou_ix', XMLDB_INDEX_NOTUNIQUE, array('touserid'));
        $table->add_index('mdl_toolmerg_fru_ix', XMLDB_INDEX_NOTUNIQUE, array('fromuserid'));
        $table->add_index('mdl_toolmerg_suc_ix', XMLDB_INDEX_NOTUNIQUE, array('success'));
        $table->add_index('mdl_toolmerg_tfs_ix', XMLDB_INDEX_NOTUNIQUE, array('touserid', 'fromuserid', 'success'));

        // Conditionally launch create table for tool_mergeusersr
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // mergeusers savepoint reached
        upgrade_plugin_savepoint(true, 2013112912, 'tool', 'mergeusers');
    }

    if ($oldversion < 2023040401) {

        // Define field mergedbyuserid to be added to tool_mergeusers.
        $table = new xmldb_table('tool_mergeusers');
        $field = new xmldb_field('mergedbyuserid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'success');

        // Conditionally launch add field mergedbyuserid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mergeusers savepoint reached.
        upgrade_plugin_savepoint(true, 2023040401, 'tool', 'mergeusers');
    }

    if ($oldversion < 2024111400) {
        // Try to create custom fields
        tool_mergeusers_create_user_profile_fields();

        // Mergeusers savepoint reached.
        upgrade_plugin_savepoint(true, 2024111400, 'tool', 'mergeusers');
    }

    return true;
}
